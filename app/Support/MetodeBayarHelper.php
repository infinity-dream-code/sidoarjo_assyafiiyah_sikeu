<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class MetodeBayarHelper
{
    public const ANDROID_FIDBANK = '6';
    public const SALDO_FIDBANK = '1140002';

    public static function isMobileNoreff(?string $noreff): bool
    {
        return strcasecmp(trim((string) $noreff), 'Mobile') === 0;
    }

    public static function isMnlNoreff(?string $noreff): bool
    {
        return strcasecmp(trim((string) $noreff), 'MNL') === 0;
    }

    /**
     * Label metode/keterangan dari sccttran.METODE → Indonesia.
     */
    public static function translateMetodeLabel(?string $metode): string
    {
        $raw = trim((string) $metode);
        if ($raw === '') {
            return '-';
        }

        return match (strtoupper($raw)) {
            'CASH' => 'Tunai',
            'FROM TELLER' => 'Dari Teller',
            'TELLER' => 'Teller',
            'TOP UP' => 'Top Up',
            'MOBILE' => 'Mobile',
            'MNL' => 'Manual',
            default => $raw,
        };
    }

    /**
     * Aturan tampilan metode (sumber utama: scctbill):
     * - scctbill.NOREFF = Mobile → ANDROID (FIDBANK apa saja)
     * - selain itu pakai scctbill.FIDBANK
     *   1140000 + MNL → Manual Tunai
     *   1140001 + MNL → Manual BMI
     *   1140002 + MNL → Manual Saldo
     *   1140003 → Transfer Bank Lain
     */
    public static function resolveDisplayFidBank(?string $fidBank, ?string $billNoreff = null): string
    {
        if (self::isMobileNoreff($billNoreff)) {
            return self::ANDROID_FIDBANK;
        }

        return (string) ($fidBank ?? '');
    }

    /**
     * Filter Bank = ANDROID: hanya scctbill.NOREFF Mobile ATAU FIDBANK 6.
     * (Jangan pakai sccttran.NOREFF — sering "Mobile" padahal bukan ANDROID di bill.)
     *
     * @param  Builder|QueryBuilder  $query
     * @return Builder|QueryBuilder
     */
    public static function applyAndroidBankFilter($query, string $tranAlias = 'sccttran', string $billAlias = 'scctbill')
    {
        return $query->where(function ($q) use ($billAlias) {
            $q->where("{$billAlias}.FIDBANK", self::ANDROID_FIDBANK)
                ->orWhereRaw('UPPER(TRIM(COALESCE(' . $billAlias . '.NOREFF, ""))) = ?', ['MOBILE']);
        });
    }

    /**
     * Filter Bank selain ANDROID: jangan ikutkan baris scctbill.NOREFF Mobile.
     *
     * @param  Builder|QueryBuilder  $query
     * @return Builder|QueryBuilder
     */
    public static function excludeMobileNoreff($query, string $tranAlias = 'sccttran', string $billAlias = 'scctbill')
    {
        return $query->whereRaw(
            'UPPER(TRIM(COALESCE(' . $billAlias . '.NOREFF, ""))) <> ?',
            ['MOBILE']
        );
    }

    /**
     * Filter berdasarkan pilihan bank dropdown.
     *
     * @param  Builder|QueryBuilder  $query
     * @return Builder|QueryBuilder
     */
    public static function applyBankFilter($query, ?string $bank, string $fidColumn, string $tranAlias = 'sccttran', string $billAlias = 'scctbill')
    {
        $bank = trim((string) $bank);
        if ($bank === '' || strtolower($bank) === 'all') {
            return $query;
        }

        if ($bank === self::ANDROID_FIDBANK) {
            return self::applyAndroidBankFilter($query, $tranAlias, $billAlias);
        }

        $query->where($fidColumn, $bank);

        return self::excludeMobileNoreff($query, $tranAlias, $billAlias);
    }
}
