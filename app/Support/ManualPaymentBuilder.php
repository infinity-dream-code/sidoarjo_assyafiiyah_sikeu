<?php

namespace App\Support;

use App\Models\scctbill;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ManualPaymentBuilder
{
    private const SALDO_FIDBANK = '1140002';

    public function payBill(
        scctbill $tagihan,
        int $nominal,
        string $fidBank,
        string $paidAt,
        ?string $transno,
        Request $request
    ): void {
        $custId = (string) $tagihan->CUSTID;
        $aa = (string) $tagihan->AA;
        $billCd = (string) ($tagihan->BILLCD ?? '');
        $userId = $this->resolveCyberKeyUserId();

        if ($fidBank === self::SALDO_FIDBANK) {
            $this->callBuilderPaymentBill($aa, $nominal);
        } else {
            $this->callBuilderPaymentCash(
                $custId,
                $fidBank,
                $userId,
                $this->formatPaymentDate($paidAt),
                $billCd,
                $aa,
                $nominal
            );
        }

        // Function DB tetap pakai tanggal setting; setelah OK override PAIDDT (jam 12:00)
        // dan catat waktu aktual input di PAIDDT_ACTUAL.
        $this->applyPaidDates($aa, $paidAt);
    }

    /**
     * PAIDDT = tanggal bayar yang di-set user, jam selalu 12:00:00.
     * PAIDDT_ACTUAL = waktu sekarang saat pembayaran diinput.
     */
    private function applyPaidDates(string $aa, string $paidAt): void
    {
        $paidDt = Carbon::parse($paidAt)->setTime(12, 0, 0)->toDateTimeString();
        $paidDtActual = Carbon::now()->toDateTimeString();

        scctbill::where('AA', $aa)->update([
            'PAIDDT' => $paidDt,
            'PAIDDT_ACTUAL' => $paidDtActual,
        ]);

        Log::info('manual-payment.builder.paid_dates', [
            'aa' => $aa,
            'paiddt' => $paidDt,
            'paiddt_actual' => $paidDtActual,
        ]);
    }

    /**
     * BuilderPaymentCash(v_CUSTID, p_FIDBANK, p_User, p_Date, p_BILLCD, p_AA, p_Payment)
     * p_Date format: YYYYMMDD (8 karakter)
     */
    private function callBuilderPaymentCash(
        string $custId,
        string $fidBank,
        string $userId,
        string $paymentDate,
        string $billCd,
        string $aa,
        int $nominal
    ): void {
        Log::info('manual-payment.builder.call', [
            'function' => 'BuilderPaymentCash',
            'custid' => $custId,
            'fidbank' => $fidBank,
            'users' => $userId,
            'date' => $paymentDate,
            'billcd' => $billCd,
            'aa' => $aa,
            'nominal' => $nominal,
        ]);

        $result = $this->invokeStoredFunction('BuilderPaymentCash', [
            $custId,
            $fidBank,
            $userId,
            $paymentDate,
            $billCd,
            $aa,
            $nominal,
        ]);

        $this->assertBuilderResult('BuilderPaymentCash', $result, [
            'custid' => $custId,
            'aa' => $aa,
            'nominal' => $nominal,
        ]);
    }

    /** BuilderPaymentBill(aa, nominal) — 2 param sesuai definition DB */
    private function callBuilderPaymentBill(string $aa, int $nominal): void
    {
        Log::info('manual-payment.builder.call', [
            'function' => 'BuilderPaymentBill',
            'aa' => $aa,
            'nominal' => $nominal,
        ]);

        $result = $this->invokeStoredFunction('BuilderPaymentBill', [
            $aa,
            $nominal,
        ]);

        $this->assertBuilderResult('BuilderPaymentBill', $result, [
            'aa' => $aa,
            'nominal' => $nominal,
        ]);
    }

    /** MySQL FUNCTION (fx) — pakai SELECT, bukan CALL (procedure). */
    private function invokeStoredFunction(string $functionName, array $params): string
    {
        $placeholders = implode(', ', array_fill(0, count($params), '?'));

        $rows = DB::connection('DATA_MYSQL')->select(
            "SELECT {$functionName}({$placeholders}) AS builder_result",
            $params
        );

        return trim((string) (($rows[0] ?? null)->builder_result ?? ''));
    }

    private function assertBuilderResult(string $functionName, string $result, array $context = []): void
    {
        if ($result === 'OK') {
            Log::info('manual-payment.builder.ok', array_merge(['function' => $functionName], $context));
            return;
        }

        Log::warning('manual-payment.builder.failed', array_merge([
            'function' => $functionName,
            'result' => $result,
        ], $context));

        throw new RuntimeException($this->mapBuilderResultMessage($result));
    }

    private function mapBuilderResultMessage(string $result): string
    {
        return match ($result) {
            'NOMINAL_SALAH_TAGIHAN_TIDAK_BOLEH_DICICIL' => 'Nominal pembayaran salah. Tagihan ini tidak boleh dicicil — harus dibayar lunas.',
            'MELEBIHI_TAGIHAN' => 'Nominal pembayaran melebihi sisa tagihan.',
            '' => 'Pembayaran gagal diproses oleh sistem (tidak ada respons dari database).',
            default => "Pembayaran gagal diproses oleh sistem ({$result}).",
        };
    }

    private function formatPaymentDate(string $paidAt): string
    {
        return Carbon::parse($paidAt)->format('Ymd');
    }

    private function resolveCyberKeyUserId(): string
    {
        $user = Auth::user();

        if ($user === null) {
            return '';
        }

        return (string) ($user->urut ?? Auth::id() ?? '');
    }
}
