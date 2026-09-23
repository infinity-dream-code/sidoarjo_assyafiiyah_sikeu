<?php

namespace App\Imports\Keuangan\TagihanSiswa;

use App\Models\scctcust;
use App\Support\SchoolScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportTagihanExcel implements WithMultipleSheets, ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public const CACHE_KEY = 'import_tagihan_excel';

    public const REQUIRED_COLUMNS = [
        'nama',
        'unit',
        'kelas',
        'kelompok',
        'angkatan',
        'nominal',
    ];

    public const COLUMN_LABELS = [
        'nik' => 'NIK',
        'nis' => 'NIS',
        'nama' => 'Nama',
        'unit' => 'Unit',
        'kelas' => 'Kelas',
        'kelompok' => 'Kelompok',
        'angkatan' => 'Angkatan',
        'nominal' => 'Nominal',
    ];

    public function __construct(
        public ?string $sekolah = null,
        private ?string $cacheKey = null,
    ) {
        $this->cacheKey ??= self::CACHE_KEY;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $collection): void
    {
        $processedData = [];

        foreach ($collection as $row) {
            if ($row->filter()->isEmpty()) {
                continue;
            }

            $rowData = $this->normalizeRow($row->toArray());
            $nis = $rowData['nis'] ?? '';
            $nominal = $rowData['nominal'] ?? null;
            $nominalBlank = $nominal === null || trim((string) $nominal) === '';

            if ($nis === '' && $nominalBlank) {
                continue;
            }

            $rowData['status'] = 1;
            $statusKet = [];

            if ($nis === '') {
                $rowData['status'] = 0;
                $statusKet[] = 'NIS/NIK tidak boleh kosong';
            } else {
                $siswa = scctcust::where('NOCUST', $nis);
                SchoolScope::apply($siswa, 'scctcust', $this->sekolah);
                $siswa = $siswa->first();
                if (!$siswa) {
                    $rowData['status'] = 0;
                    $statusKet[] = "NIS/NIK {$nis} tidak ditemukan";
                }
            }

            if ($this->isBlankKelas($rowData['kelas'] ?? null)) {
                $rowData['status'] = 0;
                $statusKet[] = 'KELAS wajib diisi (tidak boleh kosong)';
                $rowData['kelas'] = '';
            }

            foreach (['nama', 'unit', 'kelompok', 'angkatan'] as $column) {
                if (trim((string) ($rowData[$column] ?? '')) === '') {
                    $rowData['status'] = 0;
                    $statusKet[] = strtoupper($column).' wajib diisi';
                }
            }

            if ($nominalBlank) {
                $rowData['status'] = 0;
                $statusKet[] = 'Nominal tidak boleh kosong';
            } else {
                $nominalInt = $this->excelInteger($nominal);
                $rowData['nominal'] = $nominalInt;
                if ($nominalInt === null || $nominalInt <= 0) {
                    $rowData['status'] = 0;
                    $statusKet[] = 'Nominal harus lebih dari 0';
                }
            }

            $rowData['nocust'] = $nis;
            $rowData['keterangan'] = $statusKet === [] ? null : implode(', ', array_unique($statusKet));
            $processedData[] = $rowData;
        }

        Cache::forget($this->cacheKey);
        if (!empty($processedData)) {
            Cache::put($this->cacheKey, $processedData, now()->addMinutes(60));
        }
    }

    /**
     * @param  array<string, mixed>  $rowData
     * @return array<string, mixed>
     */
    private function normalizeRow(array $rowData): array
    {
        $lookup = [];
        foreach ($rowData as $key => $value) {
            $normalized = strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $key) ?? (string) $key);
            $lookup[$normalized] = $value;
        }

        $pick = function (array $aliases) use ($lookup) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $lookup)) {
                    return $lookup[$alias];
                }
            }

            return null;
        };

        $nisRaw = $pick(['nik', 'nis', 'nocust']);
        $kelasRaw = $pick(['kelas', 'kelassiswa']);
        $nominalRaw = $pick(['nominal', 'jumlah', 'tagihan']);

        $nis = $this->excelId($nisRaw);

        return [
            'nis' => $nis,
            'nik' => $nis,
            'nocust' => $nis,
            'nama' => trim((string) ($pick(['nama', 'nmcust']) ?? '')),
            'unit' => trim((string) ($pick(['unit']) ?? '')),
            'kelas' => $this->normalizeKelas($kelasRaw),
            'kelompok' => trim((string) ($pick(['kelompok']) ?? '')),
            'angkatan' => trim((string) ($pick(['angkatan']) ?? '')),
            'gender' => trim((string) ($pick(['gender', 'jk', 'jeniskelamin']) ?? '')) ?: null,
            'alamat' => trim((string) ($pick(['alamat']) ?? '')) ?: null,
            'ortu' => trim((string) ($pick(['ortu', 'genus', 'ayah', 'wali']) ?? '')) ?: null,
            'nominal' => $nominalRaw,
        ];
    }

    private function normalizeKelas(mixed $value): string
    {
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
        if ($this->isBlankKelas($text)) {
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

    private function isBlankKelas(mixed $value): bool
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

    private function excelId(mixed $value): string
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
        if ($text === '' || $text === '-') {
            return '';
        }

        if (is_numeric($text)) {
            return sprintf('%.0f', (float) $text);
        }

        return $text;
    }

    private function excelInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = is_string($value)
            ? str_replace(['.', ',', ' '], '', $value)
            : $value;

        if (!is_numeric($raw)) {
            return null;
        }

        return (int) $raw;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
