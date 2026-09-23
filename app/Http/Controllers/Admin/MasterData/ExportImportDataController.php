<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Imports\MasterData\ImportDataSiswa;
use App\Models\mst_kelas;
use App\Models\mst_sekolah;
use App\Models\scctcust;
use App\Models\ValidationMessage;
use App\Support\InputSiswaProcedure;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Validators\ValidationException;

class ExportImportDataController extends Controller
{
    public string $title = 'Master Data';
    public string $mainTitle = 'Export Import Data';
    public string $dataTitle = 'Export Import Data';
    public string $cacheKey = 'import_data_siswa';

    public function index()
    {
        $data['title'] = $this->title;
        $data['mainTitle'] = $this->mainTitle;
        $data['dataTitle'] = $this->dataTitle;
        $data['columnsUrl'] = route('admin.master-data.export-import-data.get-column');
        $data['datasUrl'] = route('admin.master-data.export-import-data.get-data');

        return view('admin.master_data.export_import_data.index', $data);
    }

    public function getColumn()
    {
        return [
            ['data' => null, 'name' => 'no', 'className' => 'text-center', 'columnType' => 'row'],
            ['data' => 'nis', 'name' => 'NIS', 'searchable' => false, 'orderable' => false],
            ['data' => 'nodaftar', 'name' => 'No Pend', 'searchable' => false, 'orderable' => false],
//            ['data' => 'NOVA', 'name' => 'NO VA'],
            ['data' => 'name', 'name' => 'NAMA', 'searchable' => false, 'orderable' => false],
            ['data' => 'status', 'name' => 'Status', 'searchable' => true, 'orderable' => true, 'columnType' => 'importstatus'],
            ['data' => 'keterangan', 'name' => 'Keterangan', 'searchable' => true, 'orderable' => true],
            ['data' => 'unit', 'name' => 'Unit', 'searchable' => false, 'orderable' => false],
            ['data' => 'kelas', 'name' => 'Kelas', 'searchable' => false, 'orderable' => false],
            ['data' => 'kelompok', 'name' => 'Kelompok', 'searchable' => false, 'orderable' => false],
            ['data' => 'angkatan', 'name' => 'Angkatan', 'searchable' => false, 'orderable' => false],
            ['data' => 'gender', 'name' => 'Jenis Kelamin', 'searchable' => false, 'orderable' => false],
            ['data' => 'ortu', 'name' => 'Ortu / Wali', 'searchable' => false, 'orderable' => false],
            ['data' => 'alamat', 'name' => 'Alamat', 'searchable' => false, 'orderable' => false],
        ];
    }

    public function getData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length');

        $columnName_arr = $request->get('columns');
        $search_arr = $request->get('search');

        $defaultColumn = 'scctcust.nocust';
        $defaultOrder = 'asc';

        if ($request->has('order')) {
            $columnIndex_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnSortOrder = $columnIndex_arr[0]['dir'];
        } else {
            $columnIndex = $defaultColumn;
            $columnSortOrder = $defaultOrder;
        }

        $columnName = $columnName_arr[$columnIndex]['data'];
        $searchValue = $search_arr['value'];

        if (!$columnName || $columnName == 'no') {
            $columnName = $defaultColumn;
            $columnSortOrder = $defaultOrder;
        }

        $filters = [];
        $filterQuery = null;

        $cachedData = collect(Cache::get($this->cacheKey) ?? []);
        $paginatedData = $cachedData->slice($start, $rowperpage)->values();


        $nisList = collect($cachedData)->pluck('nis')->toArray();
        $nisCount = count($cachedData);

        $whereAny = [
            'scctcust.NMCUST',
            'scctcust.NOCUST',
        ];

        $select = array_unique(array_merge($whereAny, [
            'scctcust.NUM2ND',
            'scctcust.CODE02',
            'scctcust.DESC02',
            'scctcust.DESC03',
            'scctcust.DESC04',

        ]));

        $records = collect($paginatedData)->map(function ($item) {
            $nis = $item['nis'];
            return [
                'nis' => $nis,
                'nodaftar' => $item['nodaftar'] ?? null,
                'name' => $item['nama'] ?? null,
                'unit' => $item['unit'] ?? null,
                'kelas' => $item['kelas'] ?? null,
                'kelompok' => $item['kelompok'] ?? null,
                'angkatan' => $item['angkatan'] ?? null,
                'gender' => $item['gender'] ?? null,
                'ortu' => $item['ortu'] ?? $item['genus'] ?? null,
                'alamat' => $item['alamat'] ?? null,
                'status' => $item['status'] ?? 0,
                'keterangan' => $item['keterangan'],
            ];
        });

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $nisCount,
            'recordsFiltered' => $nisCount,
            'data' => $records,
        );
        return response()->json($response);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'fileImport' => ['required', 'mimes:xls,xlsx', 'max:1024']
            ],
            ValidationMessage::messages(),
            ValidationMessage::attributes()
        );

        $file = $request->fileImport;

        try {
            $headingsData = (new HeadingRowImport)->toArray($file);
            $requiredColumns = [
                'nama', 'unit', 'kelas', 'kelompok', 'angkatan',
            ];

            $conditionalColumns = ['nik', 'nodaftar'];
            if (empty($headingsData) || !isset($headingsData[0][0])) throw new \Exception ('Tidak dapat membaca judul kolom dari file. Pastikan file memiliki header yang sesuai.');
            $headings = $headingsData[0][0];
            $headings = array_map(static function ($heading) {
                return strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $heading) ?? (string) $heading);
            }, $headings);
            $missingColumns = [];
            $hasNik = in_array('nik', $headings, true) || in_array('nis', $headings, true);
            $hasNodaftar = in_array('nodaftar', $headings, true);

            if (!$hasNik && !$hasNodaftar) {
                $missingColumns[] = 'NIK / NODAFTAR';
            }
            foreach ($requiredColumns as $column) if (!in_array($column, $headings, true)) $missingColumns[] = $column;

            if (!empty($missingColumns)) {
                $formattedMissingColumns = strtoupper(str_replace('_', ' ', implode(', ', $missingColumns)));
                $formattedRequiredColumns = 'NIS, NAMA, UNIT, KELAS, KELOMPOK, ANGKATAN';
                throw new Exception (
                    "Kolom $formattedMissingColumns tidak ditemukan.<br><hr>
                               pastikan kolom berikut ada dan terisi pada file import yang akan diproses: $formattedRequiredColumns. <br>
                               Catatan: NIS atau NODAFTAR wajib salah satu terisi. Format sama dengan Buat Tagihan Excel; bedanya tagihan ada tambahan NOMINAL."
                );
            }

            Cache::forget($this->cacheKey);
            Excel::import(new ImportDataSiswa(), $file);

            $data = Cache::get($this->cacheKey) ?? [];
            $invalidCount = collect($data)->where('status', 0)->count();
            $message = 'Sukses, data siswa telah diimport, silahkan periksa kembali';
            if ($invalidCount > 0) {
                $message .= " ({$invalidCount} baris perlu diperbaiki, lihat kolom Keterangan)";
            }

            return response()->json(['message' => $message, 'data' => $data], 200);
        } catch (ValidationException $e) {
            $errorMessages = $e->errors();
            $errorMessage = $errorMessages['error'][0] ?? 'Terjadi kesalahan saat melakukan import data.';
            return response()->json(['message' => $errorMessage, 'error' => $errorMessages], 422);
        } catch (Exception $e) {
            $error = $e->getMessage();
            return response()->json(['message' => "Gagal!<br> tidak dapat melakukan $this->mainTitle.<hr> $error", 'error' => $error], 422);
        }
    }

    public function validateData(Request $request)
    {
        $rules = [
            'metode' => ['required', 'in:1,2,3,4'],
        ];

        $request->validate(
            $rules,
            ValidationMessage::messages(),
            ValidationMessage::attributes()
        );

        $data = Cache::get($this->cacheKey);
        if (is_null($data) || (is_array($data) && empty($data))) {
            return response()->json(['message' => 'Tidak ada data yang dapat diproses, silahkan upload file terlebih dahulu'], 422);
        }

        $connection = DB::connection('DATA_MYSQL');

        try {
            if ($request->metode != '1') {
                $connection->beginTransaction();
            }

            if ($request->metode == '1') {
                $invalidCount = collect($data)->where('status', 0)->count();
                if ($invalidCount > 0) {
                    return response()->json([
                        'message' => "Ada {$invalidCount} baris bermasalah. Perbaiki data di kolom Keterangan terlebih dahulu.",
                    ], 422);
                }

                $missingKelas = collect($data)->filter(function ($item) {
                    return $this->isBlankKelas($item['kelas'] ?? null);
                })->count();
                if ($missingKelas > 0) {
                    return response()->json([
                        'message' => "Ada {$missingKelas} baris tanpa KELAS. Siswa tanpa kelas tidak dapat disimpan.",
                    ], 422);
                }

                $rows = array_filter($data, function ($item) {
                    return !empty($item['nis'] ?? null) && !$this->isBlankKelas($item['kelas'] ?? null);
                });
                if (empty($rows)) {
                    return response()->json(['message' => 'Tidak ada baris dengan NIK dan KELAS yang dapat disimpan'], 422);
                }

                $saved = 0;
                foreach ($rows as $item) {
                    $item = $this->normalizeImportItem($item);
                    $nis = (string) ($item['nis'] ?? '');
                    $kelasText = trim((string) ($item['kelas'] ?? ''));
                    if ($nis === '' || $this->isBlankKelas($kelasText)) {
                        continue;
                    }

                    $matchedKelas = mst_kelas::findForImport(
                        $item['unit'] ?? null,
                        $item['kelas'] ?? null,
                        $item['kelompok'] ?? null,
                    );
                    if (!$matchedKelas) {
                        return response()->json([
                            'message' => sprintf(
                                'Kelas tidak ditemukan di Master Kelas untuk NIS %s (Unit: %s, Kelas: %s, Kelompok: %s). Tambahkan dulu di menu Master Kelas (mis. unit MTS).',
                                $nis,
                                $item['unit'] ?? '-',
                                $item['kelas'] ?? '-',
                                $item['kelompok'] ?? '-',
                            ),
                        ], 422);
                    }

                    InputSiswaProcedure::call(
                        $nis,
                        (string) ($item['nama'] ?? ''),
                        (string) ($matchedKelas->jenjang ?? $kelasText),
                        (string) ($matchedKelas->unit ?? ($item['unit'] ?? '')),
                        (string) ($matchedKelas->kelompok ?? ''),
                        (string) ($matchedKelas->kelas ?? ($item['kelompok'] ?? '')),
                        (string) ($item['angkatan'] ?? ''),
                        $item['alamat'] ?? null,
                        $item['gender'] ?? null,
                        $this->resolveOrtuForDb($item),
                    );

                    $saved++;
                }

                if ($saved === 0) {
                    return response()->json(['message' => 'Tidak ada data siswa yang berhasil diproses'], 422);
                }
            } elseif ($request->metode == '2') {
                $rows = array_filter($data, fn ($item) => !empty($item['nodaftar'] ?? null));

                foreach ($rows as $item) {
                    if ((int) ($item['status'] ?? 1) === 0) {
                        continue;
                    }
                    if ($this->isBlankKelas($item['kelas'] ?? null)) {
                        continue;
                    }
                    $item = $this->normalizeImportItem($item);
                    $lookupKey = $item['nodaftar'] ?? '';

                    if (strlen($lookupKey) > 10) {
                        continue;
                    }

                    $existingCust = scctcust::where('NUM2ND', $item['nodaftar'])->first();

                    $matchedKelas = mst_kelas::findForImport(
                        $item['unit'] ?? null,
                        $item['kelas'] ?? null,
                        $item['kelompok'] ?? null,
                    );
                    if (!$matchedKelas) {
                        $connection->rollBack();

                        return response()->json([
                            'message' => sprintf(
                                'Kelas tidak ditemukan di Master Kelas untuk No Pendaftaran %s (Unit: %s, Kelas: %s, Kelompok: %s).',
                                $item['nodaftar'],
                                $item['unit'] ?? '-',
                                $item['kelas'] ?? '-',
                                $item['kelompok'] ?? '-',
                            ),
                        ], 422);
                    }

                    if (!$existingCust) {
                        if (!empty($item['nis'])) {
                            $existingNis = scctcust::where('NOCUST', $item['nis'])->first();
                            if ($existingNis) {
                                $connection->rollBack();

                                return response()->json(['message' => 'Gagal, siswa dengan NIK :' . $item['nis'] . ' sudah ada!'], 422);
                            }
                        }

                        scctcust::create($this->buildScctcustPayload($item, false, null, $matchedKelas));
                    } else {
                        $existingCust->update($this->buildScctcustPayload(
                            $item,
                            true,
                            $existingCust,
                            $matchedKelas,
                        ));
                    }
                }
            } elseif ($request->metode == '3') {
                $rows = array_filter($data, fn ($item) => !empty($item['nis'] ?? null));

                foreach ($rows as $item) {
                    if ((int) ($item['status'] ?? 1) === 0) {
                        continue;
                    }
                    if ($this->isBlankKelas($item['kelas'] ?? null)) {
                        continue;
                    }
                    $item = $this->normalizeImportItem($item);
                    if (strlen((string) ($item['nis'] ?? '')) > 10) {
                        continue;
                    }

                    $existingCust = scctcust::where('NOCUST', $item['nis'])->first();

                    if ($existingCust) {
                        $existingCust->update([
                            'CODE02' => $item['unit'] ?? null,
                            'DESC02' => $item['kelas'] ?? null,
                            'DESC03' => $item['kelompok'] ?? null,
                        ]);
                    }
                }
            } elseif ($request->metode == '4') {
                $rows = array_filter($data, fn ($item) => !empty($item['nodaftar'] ?? null) && !empty($item['nis'] ?? null));

                foreach ($rows as $item) {
                    if ((int) ($item['status'] ?? 1) === 0) {
                        continue;
                    }
                    $item = $this->normalizeImportItem($item);
                    if (strlen((string) ($item['nodaftar'] ?? '')) > 10) {
                        continue;
                    }
                    if (strlen((string) ($item['nis'] ?? '')) > 10) {
                        continue;
                    }

                    $existingNis = scctcust::where('NOCUST', $item['nis'])->first();
                    if ($existingNis) {
                        $connection->rollBack();

                        return response()->json(['message' => 'Gagal, NIK :' . $item['nis'] . ' sudah ada!'], 422);
                    }

                    $existingCust = scctcust::where('NUM2ND', $item['nodaftar'])->first();
                    if ($existingCust && in_array(trim((string) $existingCust->NOCUST), ['', '-'], true)) {
                        $existingCust->update([
                            'NOCUST' => $item['nis'],
                        ]);
                    }
                }
            }

            if ($request->metode != '1' && $connection->transactionLevel() > 0) {
                $connection->commit();
            }
            Cache::forget($this->cacheKey);

            return response()->json(['message' => 'Sukses, data siswa telah disimpan, silahkan periksa kembali'], 200);
        } catch (\Throwable $e) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            Log::error('export_import_data.validateData.failed', [
                'metode' => $request->metode,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $response = ['message' => 'Gagal, data tidak dapat disimpan'];
            if (config('app.debug')) {
                $response['error'] = $e->getMessage();
            }

            return response()->json($response, 422);
        }
    }

    public function clearData()
    {
        Cache::forget($this->cacheKey);
        return response()->json(['message' => 'Data dibersihkan'], 200);
    }

    private function normalizeImportItem(array $item): array
    {
        $item['nis'] = isset($item['nis']) && $item['nis'] !== '' && $item['nis'] !== null
            ? (string) $item['nis']
            : null;
        $item['nodaftar'] = isset($item['nodaftar']) && $item['nodaftar'] !== '' && $item['nodaftar'] !== null
            ? (string) $item['nodaftar']
            : null;
        $item['kelas'] = $this->isBlankKelas($item['kelas'] ?? null)
            ? ''
            : trim((string) $item['kelas']);

        return $item;
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

    /** Nama ortu/wali utama (kolom ortu / genus / ayah di Excel). */
    private function resolveOrtuForDb(array $item): ?string
    {
        $ortu = trim((string) ($item['ortu'] ?? $item['genus'] ?? $item['ayah'] ?? ''));

        return $ortu !== '' ? $ortu : null;
    }

    /** Nama ortu kedua — hanya untuk file lama (kolom ibu). */
    private function resolveOrtuSecondForDb(array $item): ?string
    {
        $second = trim((string) ($item['ibu'] ?? ''));

        return $second !== '' ? $second : null;
    }

    private function resolveSekolahForImport(?string $unit, ?mst_kelas $kelas): ?mst_sekolah
    {
        // Prioritas: kode sekolah dari mst_kelas.kelompok (angka, mis. 104/105)
        $schoolCode = trim((string) ($kelas->kelompok ?? ''));
        if ($schoolCode !== '' && preg_match('/^\d+$/', $schoolCode)) {
            $byCode = mst_sekolah::query()->where('CODE01', $schoolCode)->first();
            if ($byCode) {
                return $byCode;
            }
        }

        $unit = trim((string) $unit);
        if ($unit !== '') {
            $byUnit = mst_sekolah::query()
                ->where(function ($query) use ($unit) {
                    $query->where('DESC01', 'like', '%'.$unit.'%')
                        ->orWhere('CODE01', $unit)
                        ->orWhereRaw('UPPER(TRIM(DESC01)) = ?', [strtoupper($unit)]);
                })
                ->first();

            if ($byUnit) {
                return $byUnit;
            }
        }

        if (!$kelas) {
            return null;
        }

        $kelasUnit = trim((string) ($kelas->unit ?? ''));
        if ($kelasUnit === '') {
            return null;
        }

        return mst_sekolah::query()
            ->where(function ($query) use ($kelasUnit) {
                $query->where('DESC01', 'like', '%'.$kelasUnit.'%')
                    ->orWhere('CODE01', $kelasUnit)
                    ->orWhereRaw('UPPER(TRIM(DESC01)) = ?', [strtoupper($kelasUnit)]);
            })
            ->first();
    }

    private function buildScctcustPayload(
        array $item,
        bool $metodeByNodaftar = false,
        ?scctcust $existingCust = null,
        ?mst_kelas $matchedKelas = null,
    ): array {
        $kelas = $matchedKelas ?? mst_kelas::findForImport(
            $item['unit'] ?? null,
            $item['kelas'] ?? null,
            $item['kelompok'] ?? null,
        );

        if (!$kelas) {
            throw new Exception(sprintf(
                'Kelas tidak ditemukan di Master Kelas (Unit: %s, Kelas: %s, Kelompok: %s).',
                $item['unit'] ?? '-',
                $item['kelas'] ?? '-',
                $item['kelompok'] ?? '-',
            ));
        }

        $sekolah = $this->resolveSekolahForImport($item['unit'] ?? null, $kelas);

        // CODE01 harus kode sekolah angka (104/105), bukan teks UNIT (SD)
        $code01 = $sekolah?->CODE01
            ?? (preg_match('/^\d+$/', trim((string) ($kelas->kelompok ?? ''))) ? trim((string) $kelas->kelompok) : null);

        if ($code01 === null || $code01 === '') {
            throw new Exception(sprintf(
                'Kode sekolah (CODE01) tidak ditemukan untuk unit %s. Periksa Master Sekolah / kolom kelompok di Master Kelas.',
                $item['unit'] ?? '-',
            ));
        }

        $payload = [
            'NOCUST' => $item['nis'] ?? '-',
            'NMCUST' => $item['nama'],
            'NUM2ND' => $item['nodaftar'] ?? '-',
            'STCUST' => 1,
            'CODE01' => $code01,
            'DESC01' => $sekolah?->DESC01,
            'CODE02' => $kelas->unit,
            'DESC02' => $kelas->jenjang,
            'CODE03' => $kelas->id,
            'DESC03' => $kelas->kelas,
            'CODE04' => $item['gender'] ?? null,
            'DESC04' => $item['angkatan'] ?? null,
            'DESC05' => $item['alamat'] ?? null,
            'GENUS' => $this->resolveOrtuForDb($item),
            'LastUpdate' => Carbon::now(),
        ];

        if ($this->isBlankKelas($payload['DESC02'] ?? null)) {
            throw new Exception('KELAS wajib diisi. Siswa tanpa kelas tidak dapat disimpan.');
        }

        if ($existingCust) {
            $payload['NOCUST'] = $metodeByNodaftar ? ($item['nis'] ?? $existingCust->NOCUST) : $existingCust->NOCUST;
            $payload['NUM2ND'] = $metodeByNodaftar
                ? $existingCust->NUM2ND
                : ($item['nodaftar'] ?? $existingCust->NUM2ND ?? '-');

            return $payload;
        }

        $payload['CUSTID'] = scctcust::nextCustId();

        return $payload;
    }
}
