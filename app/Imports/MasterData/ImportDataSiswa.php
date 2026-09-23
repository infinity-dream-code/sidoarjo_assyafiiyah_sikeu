<?php

namespace App\Imports\MasterData;

use App\Models\scctcust;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportDataSiswa implements WithMultipleSheets, ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $collection): void
    {
        $cacheKey = 'import_data_siswa';
        $requiredKeys = ['nama', 'unit', 'kelas', 'kelompok', 'angkatan'];
        $parsedRows = [];

        foreach ($collection as $row) {
            if ($row->filter()->isEmpty()) {
                continue;
            }

            $rowData = $row->toArray();
            $rowData['unit'] = $this->normalizeRequiredText($rowData['unit'] ?? null);
            $rowData['kelas'] = $this->normalizeKelas($rowData['kelas'] ?? $rowData['kelassiswa'] ?? null);
            $rowData['kelompok'] = $this->normalizeRequiredText($rowData['kelompok'] ?? null);
            $rowData['nama'] = $this->normalizeRequiredText($rowData['nama'] ?? null);
            $rowData['angkatan'] = $this->normalizeRequiredText($rowData['angkatan'] ?? null);

            $nis = $this->normalizeId($rowData['nik'] ?? null);
            if ($nis === '') {
                $nis = $this->normalizeId($rowData['nis'] ?? null);
            }
            $nodaftar = $this->normalizeId($rowData['nodaftar'] ?? null);
            $rowData['nis'] = $nis !== '' ? $nis : null;
            $rowData['nik'] = $rowData['nis'];
            $rowData['nodaftar'] = $nodaftar !== '' ? $nodaftar : null;
            $rowData['ortu'] = $this->normalizeRequiredText($rowData['ortu'] ?? $rowData['genus'] ?? $rowData['ayah'] ?? '') ?: null;

            $parsedRows[] = $rowData;
        }

        if (empty($parsedRows)) {
            Cache::forget($cacheKey);

            return;
        }

        $nisList = array_values(array_filter(array_column($parsedRows, 'nis')));
        $nodaftarList = array_values(array_filter(array_column($parsedRows, 'nodaftar')));

        $existingNis = $nisList !== []
            ? scctcust::whereIn('NOCUST', $nisList)->pluck('NOCUST')->flip()->all()
            : [];
        $existingNodaftar = $nodaftarList !== []
            ? scctcust::whereIn('NUM2ND', $nodaftarList)->pluck('NUM2ND')->flip()->all()
            : [];

        $processedData = [];

        foreach ($parsedRows as $rowData) {
            $rowData['status'] = 1;
            $statusKet = null;

            if (!$rowData['nis'] && !$rowData['nodaftar']) {
                $rowData['status'] = 0;
                $statusKet = 'NIK atau Nomor Pendaftaran wajib diisi';
            }

            foreach ($requiredKeys as $column) {
                if ($this->isBlank($rowData[$column] ?? null)) {
                    $rowData['status'] = 0;
                    $label = $column === 'kelas' ? 'KELAS' : strtoupper($column);
                    $statusKet = $this->appendKet($statusKet, $label.' wajib diisi');
                }
            }

            // Kelas wajib keras: tanpa kelas baris tidak boleh disimpan
            if ($this->isBlank($rowData['kelas'] ?? null)) {
                $rowData['status'] = 0;
                $statusKet = $this->appendKet($statusKet, 'KELAS wajib diisi (tidak boleh kosong)');
            }

            if ($rowData['nis'] && !is_numeric($rowData['nis'])) {
                $rowData['status'] = 0;
                $statusKet = $this->appendKet($statusKet, 'NIK harus berupa angka');
            } elseif ($rowData['nis'] && isset($existingNis[$rowData['nis']]) && (int) $rowData['status'] !== 0) {
                $rowData['status'] = 2;
                $statusKet = $this->appendKet(
                    $statusKet,
                    "Siswa dengan NIK {$rowData['nis']} sudah ada, data akan diupdate"
                );
            }

            if ($rowData['nodaftar'] && !is_numeric($rowData['nodaftar'])) {
                $rowData['status'] = 0;
                $statusKet = $this->appendKet($statusKet, 'Nomor Pendaftaran harus berupa angka');
            } elseif ($rowData['nodaftar'] && isset($existingNodaftar[$rowData['nodaftar']]) && (int) $rowData['status'] !== 0) {
                $rowData['status'] = 2;
                $statusKet = $this->appendKet(
                    $statusKet,
                    "Siswa dengan nomor pendaftaran {$rowData['nodaftar']} sudah ada, data akan diupdate"
                );
            }

            $rowData['keterangan'] = $statusKet;
            $processedData[] = $rowData;
        }

        Cache::put($cacheKey, $processedData, now()->addMinutes(60));
    }

    private function normalizeKelas(mixed $value): string
    {
        // Sel Excel kosong pada kolom angka sering jadi 0 — anggap kosong
        if ($value === null || $value === false || $value === '') {
            return '';
        }

        if (is_int($value) || is_float($value)) {
            if ((float) $value == 0.0) {
                return '';
            }

            return abs($value - (int) $value) < 0.00001
                ? (string) (int) $value
                : rtrim(rtrim(sprintf('%.8F', $value), '0'), '.');
        }

        $text = trim((string) $value);
        if ($this->isBlank($text)) {
            return '';
        }

        if (is_numeric($text) && (float) $text == 0.0) {
            return '';
        }

        if (is_numeric($text) && !str_contains($text, '.')) {
            return (string) (int) $text;
        }

        return $text;
    }

    private function normalizeRequiredText(mixed $value): string
    {
        if ($value === null || $value === false) {
            return '';
        }

        $text = trim((string) $value);
        if ($this->isBlank($text)) {
            return '';
        }

        return $text;
    }

    private function normalizeId(mixed $value): string
    {
        if ($value === null || $value === false || $value === '') {
            return '';
        }

        if (is_int($value) || is_float($value)) {
            if ((float) $value == 0.0) {
                return '';
            }

            return sprintf('%.0f', $value);
        }

        $text = trim((string) $value);
        if ($this->isBlank($text)) {
            return '';
        }

        if (is_numeric($text)) {
            return sprintf('%.0f', (float) $text);
        }

        return $text;
    }

    private function isBlank(mixed $value): bool
    {
        if ($value === null || $value === false) {
            return true;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value == 0.0;
        }

        $text = strtolower(trim((string) $value));

        return $text === ''
            || $text === '-'
            || $text === 'null'
            || $text === 'n/a'
            || $text === 'na'
            || $text === 'none'
            || $text === '0'
            || $text === '0.0';
    }

    private function appendKet(?string $current, string $message): string
    {
        if ($current === null || $current === '') {
            return $message;
        }

        if (str_contains($current, $message)) {
            return $current;
        }

        return $current.', '.$message;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
