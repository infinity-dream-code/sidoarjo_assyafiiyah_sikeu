<?php

namespace App\Http\Controllers\Admin\Keuangan\TagihanSiswa;

use App\Http\Controllers\Controller;
use App\Imports\Keuangan\TagihanSiswa\ImportTagihanExcel;
use App\Models\mst_tagihan;
use App\Models\scctcust;
use App\Models\ValidationMessage;
use App\Support\InputTagihanProcedure;
use App\Support\SchoolScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Validators\ValidationException;

class UploadTagihanExcelController extends Controller
{
    public string $title = 'Keuangan';
    public string $mainTitle = 'Tagihan Siswa';
    public string $dataTitle = 'Buat Tagihan Excel';
    public string $cacheKey = ImportTagihanExcel::CACHE_KEY;

    public ?string $sekolah = null;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->sekolah = Auth::user()->sekolah;
                $this->cacheKey = ImportTagihanExcel::CACHE_KEY.'_'.Auth::id();
            }

            return $next($request);
        });
    }

    public function index()
    {
        $currentYear = (int) date('Y');

        $data['title'] = $this->title;
        $data['mainTitle'] = $this->mainTitle;
        $data['dataTitle'] = $this->dataTitle;
        $data['columnsUrl'] = route('admin.keuangan.tagihan-siswa.upload-tagihan-excel.get-column');
        $data['datasUrl'] = route('admin.keuangan.tagihan-siswa.upload-tagihan-excel.get-data');
        $data['periode_tahun_list'] = range($currentYear - 2, $currentYear + 5);
        $data['periode_tahun_default'] = $currentYear;
        $data['periode_bulan_default'] = (int) date('m');
        $data['tagihan'] = mst_tagihan::orderBy('urut', 'asc')->get();

        return view('admin.keuangan.tagihan_siswa.upload_tagihan_excel.index', $data);
    }

    public function getColumn()
    {
        return [
            ['data' => null, 'name' => 'No', 'className' => 'text-center', 'columnType' => 'row'],
            ['data' => 'nis', 'name' => 'NIS', 'searchable' => true, 'orderable' => true],
            ['data' => 'name', 'name' => 'Nama', 'searchable' => true, 'orderable' => true],
            ['data' => 'status', 'name' => 'Status', 'searchable' => true, 'orderable' => true, 'columnType' => 'importstatus'],
            ['data' => 'keterangan', 'name' => 'Keterangan', 'searchable' => true, 'orderable' => true],
            ['data' => 'unit', 'name' => 'Unit', 'searchable' => true, 'orderable' => true],
            ['data' => 'kelas', 'name' => 'Kelas', 'searchable' => true, 'orderable' => true],
            ['data' => 'kelompok', 'name' => 'Kelompok', 'searchable' => true, 'orderable' => true],
            ['data' => 'angkatan', 'name' => 'Angkatan', 'searchable' => true, 'orderable' => true],
            ['data' => 'gender', 'name' => 'Jenis Kelamin', 'searchable' => true, 'orderable' => true],
            ['data' => 'ortu', 'name' => 'Ortu / Wali', 'searchable' => true, 'orderable' => true],
            ['data' => 'alamat', 'name' => 'Alamat', 'searchable' => true, 'orderable' => true],
            ['data' => 'nominal', 'name' => 'Nominal', 'searchable' => true, 'orderable' => true, 'columnType' => 'currency'],
        ];
    }

    public function getData(Request $request)
    {
        $draw = $request->get('draw');
        $cachedData = Cache::get($this->cacheKey, []);
        $nisCount = count($cachedData);

        $select = [
            'scctcust.NMCUST',
            'scctcust.NOCUST',
            'scctcust.CODE02',
            'scctcust.DESC02',
            'scctcust.DESC03',
            'scctcust.DESC04',
            'scctcust.CODE04',
            'scctcust.DESC05',
            'scctcust.GENUS',
        ];

        $records = collect($cachedData)->map(function ($item) use ($select) {
            $nis = (string) ($item['nocust'] ?? $item['nis'] ?? '');
            $siswa = scctcust::select($select)->where('scctcust.NOCUST', $nis);
            SchoolScope::apply($siswa, 'scctcust', $this->sekolah);
            $siswa = $siswa->first();

            return [
                'nis' => $nis,
                'name' => $item['nama'] ?: ($siswa->NMCUST ?? null),
                'unit' => $item['unit'] ?: ($siswa->CODE02 ?? null),
                'kelas' => $item['kelas'] ?: ($siswa->DESC02 ?? null),
                'kelompok' => $item['kelompok'] ?: ($siswa->DESC03 ?? null),
                'angkatan' => $item['angkatan'] ?: ($siswa->DESC04 ?? null),
                'gender' => $item['gender'] ?: ($siswa->CODE04 ?? null),
                'alamat' => $item['alamat'] ?: ($siswa->DESC05 ?? null),
                'ortu' => $item['ortu'] ?: ($siswa->GENUS ?? null),
                'nominal' => $item['nominal'] ?? null,
                'status' => $item['status'] ?? 0,
                'keterangan' => $item['keterangan'] ?? null,
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $nisCount,
            'recordsFiltered' => $nisCount,
            'data' => $records,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'fileImport' => [
                    'required',
                    'file',
                    'mimes:xls,xlsx',
                    'mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/octet-stream',
                    'max:1024',
                ],
            ],
            ValidationMessage::messages(),
            ValidationMessage::attributes()
        );

        $file = $request->file('fileImport');

        try {
            $headingsData = (new HeadingRowImport)->toArray($file);
            if (empty($headingsData) || !isset($headingsData[0][0])) {
                throw new \Exception('Tidak dapat membaca judul kolom dari file. Pastikan file memiliki header yang sesuai.');
            }

            $headings = array_map(
                static fn ($heading) => strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $heading) ?? (string) $heading),
                $headingsData[0][0]
            );

            $requiredColumns = ImportTagihanExcel::REQUIRED_COLUMNS;
            $missingColumns = [];
            $hasNik = in_array('nik', $headings, true) || in_array('nis', $headings, true) || in_array('nocust', $headings, true);
            if (!$hasNik) {
                $missingColumns[] = 'NIS / NIK';
            }
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $headings, true)) {
                    $missingColumns[] = $column;
                }
            }

            if (!empty($missingColumns)) {
                $formattedMissingColumns = implode(', ', array_map([$this, 'displayColumn'], $missingColumns));
                throw new \Exception(
                    "Kolom {$formattedMissingColumns} tidak ditemukan.<br><hr>".
                    'pastikan kolom berikut ada: <b>NIS, Nama, Unit, Kelas, Kelompok, Angkatan, Nominal</b>.'.
                    '<br>Format sama dengan Import Data Siswa, ditambah kolom NOMINAL.'
                );
            }

            Cache::forget($this->cacheKey);
            Excel::import(new ImportTagihanExcel($this->sekolah, $this->cacheKey), $file);

            $data = Cache::get($this->cacheKey, []);
            if (empty($data)) {
                throw new \Exception('File berhasil dibaca, tetapi tidak ada baris data yang dapat diproses. Pastikan file berisi NIS dan Nominal.');
            }

            Log::info('Upload tagihan excel berhasil', [
                'user_id' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'row_count' => count($data),
            ]);

            return response()->json(['message' => 'Sukses, data tagihan telah diimport, silahkan periksa kembali', 'data' => $data], 200);
        } catch (ValidationException $e) {
            $errorMessages = $e->errors();
            $errorMessage = $errorMessages['error'][0] ?? 'Terjadi kesalahan saat melakukan import data.';

            return response()->json(['message' => $errorMessage, 'error' => $errorMessages], 422);
        } catch (\Throwable $e) {
            Log::error('Upload tagihan excel gagal', [
                'user_id' => auth()->id(),
                'file_name' => $file?->getClientOriginalName(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'message' => "Gagal!<br> tidak dapat melakukan {$this->mainTitle}.<hr> {$e->getMessage()}",
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function validateExcel(Request $request)
    {
        $request->validate([
            'tagihan' => ['required'],
            'periode_tahun' => ['required', 'integer', 'digits:4', 'min:2000', 'max:2099'],
            'periode_bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
        ], ValidationMessage::messages(), ValidationMessage::attributes());

        $data = Cache::get($this->cacheKey);
        if (empty($data)) {
            return response()->json(['message' => 'Silahkan import data tagihan terlebih dahulu'], 422);
        }

        $bta = $request->filled('periode_bulan')
            ? sprintf('%04d%02d', (int) $request->periode_tahun, (int) $request->periode_bulan)
            : sprintf('%04d', (int) $request->periode_tahun);

        $tagihan = mst_tagihan::where('urut', $request->tagihan)->first();
        if (!$tagihan) {
            return response()->json(['message' => 'Tagihan tidak ditemukan, silahkan muat ulang halaman!'], 422);
        }

        $nmTagihan = trim((string) $tagihan->tagihan);
        $isNyicil = (string) ((int) ($tagihan->isINSTALLMENT ?? 0));

        try {
            $skippedInactive = [];
            $skippedInvalid = [];
            $insertedCount = 0;

            foreach ($data as $item) {
                if ((int) ($item['status'] ?? 0) !== 1) {
                    $skippedInvalid[] = trim(($item['nocust'] ?? $item['nis'] ?? '-').' - '.($item['keterangan'] ?? 'Data tidak valid'));
                    continue;
                }

                $nocust = trim((string) ($item['nocust'] ?? $item['nis'] ?? ''));
                $siswa = scctcust::where('NOCUST', $nocust);
                SchoolScope::apply($siswa, 'scctcust', $this->sekolah);
                $siswa = $siswa->first();
                if (!$siswa) {
                    return response()->json(['message' => "Siswa dengan NIS {$nocust} tidak ditemukan."], 422);
                }

                if ((int) ($siswa->STCUST ?? 0) === 0) {
                    $skippedInactive[] = trim($nocust.' - '.($siswa->NMCUST ?? 'Tanpa Nama'));
                    continue;
                }

                InputTagihanProcedure::call(
                    $nocust,
                    (int) ($item['nominal'] ?? 0),
                    $nmTagihan,
                    $bta,
                    $bta,
                    $isNyicil,
                );
                $insertedCount++;
            }

            Cache::forget($this->cacheKey);

            $message = "Data tagihan disimpan. Berhasil dibuat untuk {$insertedCount} siswa.";
            if (!empty($skippedInactive)) {
                $message .= '<hr>Tagihan tidak dibuat untuk siswa nonaktif: '.count($skippedInactive).' siswa.<br>'.
                    implode('<br>', $skippedInactive);
            }
            if (!empty($skippedInvalid)) {
                $message .= '<hr>Baris tidak diproses karena data tidak valid: '.count($skippedInvalid).' baris.';
            }

            return response()->json(['message' => $message], 200);
        } catch (\Throwable $e) {
            Log::error('Simpan tagihan excel gagal', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function clear()
    {
        Cache::forget($this->cacheKey);
        Cache::forget(ImportTagihanExcel::CACHE_KEY);

        return response()->json(['message' => 'Data import telah dibersihkan']);
    }

    private function displayColumn(string $column): string
    {
        return ImportTagihanExcel::COLUMN_LABELS[$column]
            ?? match ($column) {
                'nis / nik', 'nis/nik' => 'NIS / NIK',
                default => ucwords(str_replace('_', ' ', $column)),
            };
    }
}
