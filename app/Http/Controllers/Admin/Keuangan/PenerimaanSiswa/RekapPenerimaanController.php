<?php

namespace App\Http\Controllers\Admin\Keuangan\PenerimaanSiswa;

use App\Http\Controllers\Controller;
use App\Support\PerNisMatrixPdf;
use App\Support\MetodeBayarHelper;
use App\Models\mst_kelas;
use App\Models\mst_tagihan;
use App\Models\mst_thn_aka;
use App\Models\mst_sekolah;
use App\Models\u_akun;
use App\Models\scctbill;
use App\Models\scctcust;
use App\Models\sccttran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekapPenerimaanController extends Controller
{
    public ?string $sekolah = null;
    private string $title = "Keuangan";
    private string $mainTitle = 'Data Pembayaran';
    private string $dataTitle = 'Data Pembayaran';
    private array $allowedFilters = [
        'tanggal-pembuatan' => 'scctbill.FTGLTagihan',
        'periode_mulai' => 'scctbill.BILLAC_start',
        'periode_akhir' => 'scctbill.BILLAC_end',
        'tahun_akademik' => 'scctbill.BTA',
        'post' => 'scctbill.BILLNM',
        'nama_tagihan' => 'scctbill.BILLNM',
        'bank' => 'sccttran.FIDBANK',
        'kelas' => 'scctcust.DESC02',
        'sekolah' => 'scctcust.CODE02',
        'siswa' => 'scctcust.nmcust',
        'custid' => 'scctcust.CUSTID',
    ];
    private string $cacheKey = 'rekap_penerimaan';

    public function __construct()
    {
        $key = Str::slug($this->cacheKey) . '_cache_version';

        Cache::add($key, 1);
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $user = Auth::user();
                $this->sekolah = $user->sekolah;
            }
            return $next($request);
        });
    }

    private function parseDateRange(string $value): ?array
    {
        $value = trim($value);
        if (!preg_match('/^(\d{2}-\d{2}-\d{4})\s*(?:~|-)\s*(\d{2}-\d{2}-\d{4})$/', $value, $matches)) {
            return null;
        }

        try {
            $startDate = Carbon::createFromFormat('d-m-Y', $matches[1])->startOfDay();
            $endDate = Carbon::createFromFormat('d-m-Y', $matches[2])->endOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        return [$startDate, $endDate];
    }

    private function applyUnitScope($query, string $table = 'scctcust'): void
    {
        \App\Support\SchoolScope::apply($query, $table, $this->sekolah);
    }

    private function resolveScopedSchoolCodes(): array
    {
        if (blank($this->sekolah)) {
            return [];
        }

        return [trim((string) $this->sekolah)];
    }

    private function paymentBaseQuery()
    {
        return sccttran::query()
            ->leftJoin('scctbill', function ($join) {
                $join->on('scctbill.AA', '=', 'sccttran.BILLID')
                    ->on('scctbill.CUSTID', '=', 'sccttran.CUSTID');
            })
            ->leftJoin('scctcust', 'scctcust.CUSTID', '=', 'sccttran.CUSTID')
            ->where(function ($q) {
                $q->whereNull('sccttran.isreversal')
                    ->orWhere('sccttran.isreversal', 0)
                    ->orWhere('sccttran.isreversal', '0');
            })
            ->whereRaw("UPPER(TRIM(COALESCE(sccttran.METODE, ''))) <> 'TOP UP'");
    }

    private function resolveMetodeLabel(object $item, array $metodeBayarMap): string
    {
        $displayFid = MetodeBayarHelper::resolveDisplayFidBank(
            $item->BILL_FIDBANK ?? $item->FIDBANK ?? null,
            $item->BILL_NOREFF ?? null
        );

        return $metodeBayarMap[$displayFid] ?? ($displayFid !== '' ? $displayFid : '-');
    }

    private function resolvePeriode(object $item): ?string
    {
        $billac = trim((string) ($item->BILLAC ?? ''));
        if ($billac !== '' && $billac !== '-') {
            return $billac;
        }

        if (blank($item->TRXDATE ?? null)) {
            return null;
        }

        try {
            return Carbon::parse($item->TRXDATE)->format('Ym');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveNominalAmount(object $item): int
    {
        $debet = (int) ($item->DEBET ?? 0);
        if ($debet > 0) {
            return $debet;
        }

        return (int) ($item->KREDIT ?? 0);
    }

    private function mapPaymentRecord(object $item, array $metodeBayarMap, bool $withItemId = true): object
    {
        if ($withItemId) {
            $item->item_id = $item->urut;
            $item->CUSTID = $item->CUSTID;
        }

        $item->PAIDDT = $item->TRXDATE;
        $item->BILLAM = $this->resolveNominalAmount($item);
        $item->BILLNM = $item->BILLNM ?? $item->BILLTARGET ?? (strtoupper(trim((string) ($item->METODE ?? ''))) === 'TOP UP' ? 'TOP UP' : '-');
        $item->METODE_BAYAR = $this->resolveMetodeLabel($item, $metodeBayarMap);
        $item->REMARK = trim((string) ($item->METODE ?? '')) ?: '-';
        $item->NOREFF = trim((string) ($item->NOREFF ?? '')) ?: '-';
        $item->kelas_label = trim(($item->DESC02 ?? '') . ' ' . ($item->DESC03 ?? ''));
        $item->NOVA = ($item->NOCUST && $item->NOCUST != '-') ? scctcust::showVA($item->NOCUST) : '-';

        if (!$item->NOCUST || $item->NOCUST == '-') {
            $item->NOCUST = null;
        }
        if (!$item->NUM2ND || $item->NUM2ND == '-') {
            $item->NUM2ND = null;
        }

        return $item;
    }

    private function resolveOrderColumn(string $column): string
    {
        return match ($column) {
            'BILLAC', 'BTA', 'FUrutan', 'PAIDST', 'BILLCD' => "scctbill.{$column}",
            'BILLNM', 'NamaAkun' => 'scctbill.BILLNM',
            'BILLAM', 'DEBET' => 'sccttran.DEBET',
            'PAIDDT', 'TRXDATE' => 'sccttran.TRXDATE',
            'METODE_BAYAR', 'FIDBANK' => 'sccttran.FIDBANK',
            'METODE', 'REMARK' => 'sccttran.METODE',
            'NOREFF' => 'sccttran.NOREFF',
            'NOVA' => 'scctcust.NOCUST',
            'kelas_label' => 'scctcust.DESC02',
            'NMCUST' => 'scctcust.NMCUST',
            'NOCUST' => 'scctcust.NOCUST',
            'CODE02', 'DESC02', 'DESC03', 'DESC04' => "scctcust.{$column}",
            'INSTALLMENT' => 'sccttran.INSTALLMENT',
            default => str_contains($column, '.') ? $column : 'sccttran.TRXDATE',
        };
    }

    public function getColumn()
    {
        return [
            ['data' => null, 'name' => 'no', 'columnType' => 'row', 'exportable' => true],
            ['data' => 'PAIDDT', 'name' => 'Tanggal', 'columnType' => 'timestamp', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'NOVA', 'name' => 'VANO', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'NMCUST', 'name' => 'Nama', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'kelas_label', 'name' => 'Kelas', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'CODE02', 'name' => 'Unit', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'BILLAM', 'name' => 'Nominal', 'searchable' => true, 'orderable' => true, 'columnType' => 'currency', 'className' => 'text-end', 'exportable' => true],
            ['data' => 'BILLNM', 'name' => 'Nama Tagihan', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'METODE_BAYAR', 'name' => 'Metode Bayar', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'REMARK', 'name' => 'Remark', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'NOREFF', 'name' => 'No Ref', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'NOCUST', 'name' => 'NIS', 'searchable' => true, 'orderable' => true, 'exportable' => true],
        ];
    }

    public function index()
    {
        $schoolCodes = $this->resolveScopedSchoolCodes();

        $data['title'] = $this->title;
        $data['mainTitle'] = $this->mainTitle;
        $data['columnsUrl'] = route('admin.keuangan.penerimaan-siswa.rekap-penerimaan.get-column');
        $data['datasUrl'] = route('admin.keuangan.penerimaan-siswa.rekap-penerimaan.get-data');
        $data['post'] = u_akun::select(['KodeAkun', 'NamaAkun'])->orderBy('KodeAkun')->get();
        $data['nama_tagihan'] = mst_tagihan::select(['tagihan'])->whereNotNull('tagihan')->orderBy('urut')->get();
        $data['thn_aka'] = mst_thn_aka::select(['thn_aka'])->where('thn_aka', '!=', null)->get();
        $data['kelas'] = mst_kelas::dropdownQuery($this->sekolah)
            ->orderByRaw("CASE WHEN jenjang REGEXP '^[0-9]+$' THEN 0 ELSE 1 END, jenjang")
            ->orderByRaw("CASE WHEN kelas REGEXP '^[0-9]+$' THEN 0 ELSE 1 END, kelas")
            ->get();
        $data['unit'] = mst_sekolah::when(!empty($schoolCodes), function ($query) use ($schoolCodes) {
            $query->whereIn('CODE01', $schoolCodes);
        })->get();
        $data['bank'] = [
            '1140000' => 'Manual Cash',
            '1140002' => 'Manual SALDO',
            '1140001' => 'Manual BMI',
            '1140003' => 'Transfer Bank Lain',
            '6' => 'ANDROID',
        ];

        return view('admin.keuangan.penerimaan_siswa.rekap_penerimaan', $data);
    }

    public function cetakKartuSiswa(Request $request)
    {
        if (!$request['custid']) return response()->json(['error' => 'siswa tidak ditemukan']);
        $request['draw'] = 2;
        $request['start'] = 0;
        $request['length'] = "poll";

        $siswa = scctcust::where('custid', $request['custid'])
            ->where(function ($query) {
                $this->applyUnitScope($query);
            })
            ->first();
        if (!$siswa) return response()->json(['error' => 'siswa tidak ditemukan']);

        $request->merge([
            'filter' => array_merge($request->input('filter', []), [
                'custid' => $request['custid']
            ])
        ]);

        $filter = $request;
        $tagihans = $this->getData($filter);

        try {
            $tagihans = json_decode(json_encode($tagihans), true);
            $tagihans = $tagihans['original']['data'];
            if (!$tagihans) return response()->json(['message' => 'Tagihan Tidak Ditemukan'], 422);
//            dd($tagihans, $siswa);
            $pdf = Pdf::loadView('cetak.kartu-siswa', ['tagihans' => $tagihans, 'siswa' => $siswa]);
            return $pdf->download('kartu-siswa.pdf');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Tagihan Tidak Ditemukan', 'error' => $e], 422);
        }
    }

    public function cetakPerNis(Request $request)
    {
        $filter = $request->input('filter', []);
        if ($request->filled('custid')) {
            $filter['custid'] = $request->input('custid');
        }

        $request->merge([
            'filter' => $filter,
            'draw' => 2,
            'start' => 0,
            'length' => 'poll',
        ]);

        $records = $this->getData($request);
        $records = json_decode(json_encode($records), true);
        $records = $records['original']['data'] ?? [];
        if (empty($records)) {
            return response()->json(['message' => 'Data penerimaan tidak ditemukan'], 422);
        }

        $postColCount = PerNisMatrixPdf::countPostColumns($records);
        $paper = PerNisMatrixPdf::paperSize($postColCount);
        $orientation = PerNisMatrixPdf::paperOrientation($postColCount);

        $pdf = Pdf::loadView('cetak.per-nis-matrix', [
            'tagihans' => $records,
            'reportTitle' => 'REKAP PENERIMAAN - CETAK PER NIS',
            'useNamaAkunHeader' => true,
        ])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'dpi' => 96,
                'defaultFont' => 'DejaVu Serif',
            ]);

        if (is_array($paper)) {
            $pdf->setPaper($paper);
        } else {
            $pdf->setPaper($paper, $orientation);
        }

        return $pdf->download('rekap-penerimaan-per-nis.pdf');
    }

    public function getData(Request $request)
    {
        $metodeBayarMap = (new scctbill())->metodeBayar ?? [];
        $draw = $request->get('draw');
        if (true) {

            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order', []);
            $columnName_arr = $request->get('columns', []);
            $order_arr = $request->get('order', []);
            $search_arr = $request->get('search', []);
            $searchValue = $search_arr['value'] ?? '';

            $columnName = 'scctcust.NMCUST';
            $columnSortOrder = 'ASC';

            if (!empty($order_arr)) {
                $columnIndex = $columnIndex_arr[0]['column'] ?? null;
                if ($columnIndex !== null && !empty($columnName_arr[$columnIndex]['data']) && $columnName_arr[$columnIndex]['data'] !== 'no') {
                    $columnName = $this->resolveOrderColumn($columnName_arr[$columnIndex]['data']);
                    $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';
                }
            }

            $filters = [];
            $filterQuery = null;

            $filter = $request->input('filter', []);
            if ($filter) {
                foreach ($filter as $key => $val) {
                    if (is_array($val) || strtolower($val) != 'all' && $val !== null && $val !== '') {
                        $colName = match ($key) {
                            'dari_tanggal', 'sampai_tanggal' => 'scctbill.FTGLTagihan',
                            'tanggal-transaksi' => 'sccttran.TRXDATE',
                            'periode_mulai', 'periode_akhir' => 'scctbill.BILLAC',
                            'tahun_akademik' => 'scctbill.BTA',
                            'post' => 'scctbill.BILLNM',
                            'nama_tagihan' => 'scctbill.BILLNM',
                            'bank' => 'sccttran.FIDBANK',
                            'unit' => 'scctcust.CODE02',
                            'kelas' => 'scctcust.DESC02',
                            'siswa' => 'scctcust.nocust',
                            'custid' => 'sccttran.CUSTID',
                            default => null
                        };
                        if ($key == 'tanggal-transaksi') {
                            $dateRange = $this->parseDateRange((string) $val);
                            if ($dateRange) {
                                [$startDate, $endDate] = $dateRange;
                                ($colName) && $filters[] = [$colName, $startDate, $endDate, 'whereBetween'];
                            }
                        } elseif (in_array($key, ['periode_mulai', 'periode_akhir'])) {
                            $periodeVal = preg_replace('/[^0-9]/', '', (string) $val);
                            if (!preg_match('/^\d{6}$/', (string) $periodeVal)) {
                                continue;
                            }
                            if ($colName) {
                                $operator = $key === 'periode_mulai' ? '>=' : '<=';
                                $filters[] = [$colName, $operator, (string) $periodeVal];
                            }
                        } else if ($key == 'kelas') {
                            $kelasValues = is_array($val) ? $val : [$val];
                            $kelasPairs = [];
                            foreach ($kelasValues as $kelasValue) {
                                $kelasPart = explode("~", (string) $kelasValue);
                                if (count($kelasPart) == 3) {
                                    $kelasPairs[] = [
                                        'CODE01' => $kelasPart[0],
                                        'DESC02' => $kelasPart[1],
                                        'CODE03' => $kelasPart[2],
                                    ];
                                }
                            }
                            if (!empty($kelasPairs)) {
                                $filters[] = ['_kelas_multi', '=', $kelasPairs];
                            }
                        } else if ($key == 'post') {
                            $array = array_filter($val, function ($value) {
                                return $value !== 'all';
                            });
                            if (count($array) > 0) {
                                ($colName) && $filters[] = [$colName, 'in', $array];
                            }
                        } elseif ($key == 'siswa') {
                            $val = '%' . $val . '%';
                            ($colName) && $filters[] = [$colName, 'like', $val];
                        } elseif ($key == 'nama_tagihan') {
                            if (is_array($val)) {
                                $array = array_values(array_filter($val, fn($item) => !is_null($item) && $item !== '' && strtolower((string) $item) !== 'all'));
                                if (!empty($array)) {
                                    ($colName) && $filters[] = [$colName, 'in', $array];
                                }
                            } else {
                                $val = '%' . trim((string) $val) . '%';
                                ($colName) && $filters[] = [$colName, 'like', $val];
                            }
                        } else if ($key === 'unit') {
                            $filters[] = ['_sekolah', '=', $val];
                        } elseif ($key === 'bank') {
                            if ((string) $val === '6') {
                                $filters[] = ['_android_bill', '=', '1'];
                            } elseif ((string) $val === '1140003') {
                                // Transfer Bank Lain: ambil dari scctbill.FIDBANK
                                $filters[] = ['scctbill.FIDBANK', '=', '1140003'];
                                $filters[] = ['_exclude_mobile_bill', '=', '1'];
                            } else {
                                // Manual Cash/BMI/SALDO dari scctbill.FIDBANK
                                $filters[] = ['scctbill.FIDBANK', '=', $val];
                                $filters[] = ['_exclude_mobile_bill', '=', '1'];
                            }
                        } else {
                            ($colName) && $filters[] = [$colName, '=', $val];
                        }
                    }
                };

                if (!empty($filters)) {
                    $filterQuery = function ($query) use ($filters) {
                        foreach ($filters as $filter) {
                            if (($filter[0] ?? null) === '_sekolah') {
                                $value = $filter[2] ?? null;
                                if (!blank($value)) {
                                    $query->where(function ($q) use ($value) {
                                        $q->whereRaw('TRIM(CAST(scctcust.CODE02 AS CHAR)) = ?', [trim((string) $value)]);
                                    });
                                }
                                continue;
                            }
                            if (($filter[0] ?? null) === '_kelas_multi') {
                                $kelasPairs = $filter[2] ?? [];
                                if (!empty($kelasPairs)) {
                                    $query->where(function ($q) use ($kelasPairs) {
                                        foreach ($kelasPairs as $kelasPair) {
                                            $q->orWhere(function ($kelasQuery) use ($kelasPair) {
                                                $kelasQuery->where('scctcust.CODE02', '=', $kelasPair['CODE01'])
                                                    ->where('scctcust.DESC02', '=', $kelasPair['DESC02'])
                                                    ->where('scctcust.CODE03', '=', $kelasPair['CODE03']);
                                            });
                                        }
                                    });
                                }
                                continue;
                            }
                            if (($filter[0] ?? null) === '_android_bill') {
                                MetodeBayarHelper::applyAndroidBankFilter($query);
                                continue;
                            }
                            if (($filter[0] ?? null) === '_exclude_mobile_bill') {
                                MetodeBayarHelper::excludeMobileNoreff($query);
                                continue;
                            }
                            switch (count($filter)) {
                                case 3:
                                    $filter[1] === 'in'
                                        ? $query->whereIn($filter[0], $filter[2])
                                        : $query->where($filter[0], $filter[1], $filter[2]);
                                    break;

                                case 4:
                                    $filter[3] === 'whereBetween'
                                        ? $query->whereBetween($filter[0], [$filter[1], $filter[2]])
                                        : $query->{$filter[3]}($filter[0], $filter[1], $filter[2]);
                                    break;
                            }
                        }
                    };
                }
            }

            $whereAny = [
                'scctcust.NMCUST',
                'scctcust.NOCUST',
                'scctcust.DESC02',
                'scctcust.DESC03',
                'sccttran.METODE',
                'sccttran.NOREFF',
                'sccttran.BILLTARGET',
                'sccttran.TRANSNO',
                'scctbill.BILLNM',
            ];

            $select = array_unique([
                'sccttran.urut',
                'sccttran.CUSTID',
                'sccttran.METODE',
                'sccttran.NOREFF',
                'sccttran.TRXDATE',
                'sccttran.FIDBANK',
                'sccttran.DEBET',
                'sccttran.KREDIT',
                'sccttran.INSTALLMENT',
                'sccttran.BILLTARGET',
                'scctbill.AA',
                'scctbill.BILLNM',
                'scctbill.BILLAC',
                'scctbill.BILLAM',
                'scctbill.BILLPAID',
                'scctbill.PAIDST',
                'scctbill.BILLCD',
                'scctbill.BTA',
                'scctbill.FUrutan',
                'scctbill.NOREFF as BILL_NOREFF',
                'scctbill.FIDBANK as BILL_FIDBANK',
                'scctcust.NMCUST',
                'scctcust.NOCUST',
                'scctcust.DESC01',
                'scctcust.CODE02',
                'scctcust.DESC02',
                'scctcust.DESC03',
                'scctcust.DESC04',
                'scctcust.NUM2ND',
            ]);

            $query = $this->paymentBaseQuery()
                ->when(!blank($searchValue), function ($query) use ($whereAny, $searchValue) {
                $query->where(function ($q) use ($whereAny, $searchValue) {
                    $sanitizeSearch = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $searchValue);
                    foreach ($whereAny as $column) {
                        $q->orWhere($column, 'like', '%' .$sanitizeSearch . '%');
                    }
                    $q->orWhere('scctbill.BILLNM', 'like', '%' . $sanitizeSearch . '%')
                        ->orWhere('sccttran.BILLTARGET', 'like', '%' . $sanitizeSearch . '%');
                });
            })
                ->where(function ($query) use ($filterQuery) {
                    if ($filterQuery) {
                        $filterQuery($query);
                    }
                });

            $this->applyUnitScope($query);

            $unitCache = blank($this->sekolah) ? 'all' : md5((string) $this->sekolah);
            $totalRecords = Cache::remember(
                "{$this->cacheKey}:total_all_data:v4:{$unitCache}",
                now()->addMinutes(10),
                function () {
                    $baseQuery = $this->paymentBaseQuery();
                    $this->applyUnitScope($baseQuery);
                    return $baseQuery->count('sccttran.urut');
                }
            );

            $totalRecordswithFilter = (clone $query)
                ->count('sccttran.urut');

            $rowperpage = $rowperpage == "poll" ? $totalRecords : $rowperpage;
            $records = (clone $query)
                ->orderByRaw("CASE WHEN scctcust.NOCUST IS NULL OR TRIM(CAST(scctcust.NOCUST AS CHAR)) = '' OR scctcust.NOCUST = '-' THEN 1 ELSE 0 END ASC")
                ->orderBy('scctcust.NOCUST', 'asc')
                ->orderBy('scctbill.FUrutan', 'asc')
                ->orderBy('sccttran.INSTALLMENT', 'asc')
                ->orderBy($columnName, $columnSortOrder)
                ->select($select)
                ->skip($start)
                ->take($rowperpage)
                ->get();

            if ($request->get("length") != "poll") {
                $records = $records->map(function ($item) use ($metodeBayarMap) {
                    return $this->mapPaymentRecord($item, $metodeBayarMap);
                });
            } else {
                $records = $records->map(function ($item) use ($metodeBayarMap) {
                    return $this->mapPaymentRecord($item, $metodeBayarMap, false);
                });
            }

            $records->toArray();
        }

        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords ?? 0,
            "recordsFiltered" => $totalRecordswithFilter ?? 0,
            "data" => $records ?? [],
        );
        return response()->json($response);
    }

    public function cetakRekapPenerimaan(Request $request)
    {
        $filters = [];
        $filterQuery = null;
        $filter_scctbill = [];
        $post = false;
        $kelas = [];
        $unit = false;
        $tanggalMulai = null;
        $tanggalSelesai = null;
        $filter = $request->input('filter');
        if ($filter) {
            foreach ($filter as $key => $val) {
                if (is_array($val) || strtolower($val) != 'all' && $val !== null && $val !== '') {
                    $colName = match ($key) {
                        'dari_tanggal', 'sampai_tanggal' => 'scctbill.FTGLTagihan',
                        'tanggal-transaksi' => 'sccttran.TRXDATE',
                        'periode_mulai', 'periode_akhir' => 'scctbill.BILLAC',
                        'tahun_akademik' => 'scctbill.BTA',
                        'post' => 'scctbill.BILLNM',
                        'nama_tagihan' => 'scctbill.BILLNM',
                        'bank' => 'sccttran.FIDBANK',
                        'unit' => 'scctcust.CODE02',
                        'kelas' => 'scctcust.DESC02',
                        'siswa' => 'scctcust.nocust',
                        'custid' => 'sccttran.CUSTID',
                        default => null
                    };

                    if ($key == 'tanggal-transaksi') {
                        $dateRange = $this->parseDateRange((string) $val);
                        if ($dateRange) {
                            [$startDate, $endDate] = $dateRange;
                            $tanggalMulai = $startDate->locale('id')->isoFormat('dddd, D MMMM YYYY');
                            $tanggalSelesai = $endDate->locale('id')->isoFormat('dddd, D MMMM YYYY');
                            ($colName) && $filters[] = [$colName, $startDate, $endDate, 'whereBetween'];
                        }
                    } elseif (in_array($key, ['periode_mulai', 'periode_akhir'])) {
                        $periodeVal = preg_replace('/[^0-9]/', '', (string) $val);
                        if (!preg_match('/^\d{6}$/', (string) $periodeVal)) {
                            continue;
                        }
                        if ($colName) {
                            $operator = $key === 'periode_mulai' ? '>=' : '<=';
                            $filters[] = [$colName, $operator, (string) $periodeVal];
                        }
                    } else if ($key == 'kelas') {
                        $kelasValues = is_array($val) ? $val : [$val];
                        $kelas = $kelasValues;
                        $kelasPairs = [];
                        foreach ($kelasValues as $kelasValue) {
                            $kelasPart = explode("~", (string) $kelasValue);
                            if (count($kelasPart) == 3) {
                                $kelasPairs[] = [
                                    'CODE01' => $kelasPart[0],
                                    'DESC02' => $kelasPart[1],
                                    'CODE03' => $kelasPart[2],
                                ];
                            }
                        }
                        if (!empty($kelasPairs)) {
                            $filters[] = ['_kelas_multi', '=', $kelasPairs];
                        }
                    } else if ($key == 'post') {
                        $array = array_filter($val, function ($value) {
                            return $value !== 'all';
                        });
                        if (count($array) > 0) {
                            ($colName) && $filters[] = [$colName, 'in', $array];
                        }
                        $post = $array;
                    }else if($key === 'unit'){
                        $unit = mst_sekolah::where('CODE01', $val)
                            ->orWhere('CODE02', $val)
                            ->orWhere('DESC01', $val)
                            ->first();
                        $filters[] = ['_sekolah', '=', $val];
                    } elseif ($key === 'nama_tagihan') {
                        if (is_array($val)) {
                            $array = array_values(array_filter($val, fn($item) => !is_null($item) && $item !== '' && strtolower((string) $item) !== 'all'));
                            if (!empty($array)) {
                                ($colName) && $filters[] = [$colName, 'in', $array];
                            }
                        } else {
                            $val = '%' . trim((string) $val) . '%';
                            ($colName) && $filters[] = [$colName, 'like', $val];
                        }
                    } else if ($key == 'siswa') {
                        $val = '%' . $val . '%';
                        ($colName) && $filters[] = [$colName, 'like', $val];
                    } elseif ($key === 'bank') {
                        if ((string) $val === '6') {
                            $filters[] = ['_android_bill', '=', '1'];
                        } elseif ((string) $val === '1140003') {
                            // Transfer Bank Lain: ambil dari scctbill.FIDBANK
                            $filters[] = ['scctbill.FIDBANK', '=', '1140003'];
                            $filters[] = ['_exclude_mobile_bill', '=', '1'];
                        } else {
                            $filters[] = ['scctbill.FIDBANK', '=', $val];
                            $filters[] = ['_exclude_mobile_bill', '=', '1'];
                        }
                    } else {
                        ($colName) && $filters[] = [$colName, '=', $val];
                    }
                }
            };
        }

        $filter_main = [];

        foreach ($filters as $item) {
            if (($item[0] ?? null) === '_sekolah'
                || ($item[0] ?? null) === '_android_bill'
                || ($item[0] ?? null) === '_exclude_mobile_bill'
                || str_contains((string) ($item[0] ?? ''), 'scctbill')
                || str_contains((string) ($item[0] ?? ''), 'sccttran')) {
                $filter_scctbill[] = $item;
            } else {
                $filter_main[] = $item;
            }
        }

        try {
            $records = $this->paymentBaseQuery()
                ->where(function ($query) use ($filter_scctbill) {
                    foreach ($filter_scctbill as $filter) {
                        if (($filter[0] ?? null) === '_sekolah') {
                            $value = $filter[2] ?? null;
                            if (!blank($value)) {
                                $query->where(function ($q) use ($value) {
                                    $q->whereRaw('TRIM(CAST(scctcust.CODE02 AS CHAR)) = ?', [trim((string) $value)]);
                                });
                            }
                            continue;
                        }
                        if (($filter[0] ?? null) === '_android_bill') {
                            MetodeBayarHelper::applyAndroidBankFilter($query);
                            continue;
                        }
                        if (($filter[0] ?? null) === '_exclude_mobile_bill') {
                            MetodeBayarHelper::excludeMobileNoreff($query);
                            continue;
                        }
                        switch (count($filter)) {
                            case 3:
                                $filter[1] === 'in'
                                    ? $query->whereIn($filter[0], $filter[2])
                                    : $query->where($filter[0], $filter[1], $filter[2]);
                                break;
                            case 4:
                                $filter[3] === 'whereBetween'
                                    ? $query->whereBetween($filter[0], [$filter[1], $filter[2]])
                                    : $query->{$filter[3]}($filter[0], $filter[1], $filter[2]);
                                break;
                        }
                    }
                })
                ->where(function ($query) use ($filter_main) {
                    foreach ($filter_main as $filter) {
                        if (($filter[0] ?? null) === '_kelas_multi') {
                            $kelasPairs = $filter[2] ?? [];
                            if (!empty($kelasPairs)) {
                                $query->where(function ($q) use ($kelasPairs) {
                                    foreach ($kelasPairs as $kelasPair) {
                                        $q->orWhere(function ($kelasQuery) use ($kelasPair) {
                                            $kelasQuery->where('scctcust.CODE02', '=', $kelasPair['CODE01'])
                                                ->where('scctcust.DESC02', '=', $kelasPair['DESC02'])
                                                ->where('scctcust.CODE03', '=', $kelasPair['CODE03']);
                                        });
                                    }
                                });
                            }
                            continue;
                        }
                        switch (count($filter)) {
                            case 3:
                                $filter[1] === 'in'
                                    ? $query->whereIn($filter[0], $filter[2])
                                    : $query->where($filter[0], $filter[1], $filter[2]);
                                break;
                            case 4:
                                $filter[3] === 'whereBetween'
                                    ? $query->whereBetween($filter[0], [$filter[1], $filter[2]])
                                    : $query->{$filter[3]}($filter[0], $filter[1], $filter[2]);
                                break;
                        }
                    }
                })
                ->where(function ($query) {
                    $this->applyUnitScope($query);
                })
                ->select([
                    'scctbill.BTA',
                    'scctbill.BILLNM',
                    DB::raw('scctbill.BILLNM as NamaAkun'),
                    'scctcust.CODE02',
                    'scctcust.DESC03',
                    DB::raw('NULL as GetWisma'),
                    DB::raw('SUM(sccttran.DEBET) as BILLAM'),
                ])
                ->groupBy([
                    'scctbill.BTA',
                    'scctbill.BILLNM',
                    'scctcust.CODE02',
                    'scctcust.DESC03',
                ])
                ->orderBy('scctbill.BTA')
                ->orderBy('scctbill.BILLNM')
                ->get();

            if ($records->isEmpty()) throw new \Exception('Gagal mengambil data tagihan');

            return response()->json([
                    'tagihans' => $records,
                    'kelas' => $kelas,
                    'unit' => $unit,
                    'tanggalMulai' => $tanggalMulai,
                    'tanggalSelesai' => $tanggalSelesai,
                ], 200);

            $customPaper = [0, 0, 935.43, 595.28];

            $pdf = Pdf::loadView('cetak.rekap-penerimaan',
                [
                    'tagihans' => $records,
                    'mstTagihan' => $mstTagihan,
                    'kelas' => $kelas,
                    'unit' => $unit,
                    'tanggalMulai' => $tanggalMulai,
                    'tanggalSelesai' => $tanggalSelesai,
                ])
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
//                        'dpi' => 96,
                ])
//                    ->setPaper('a4', 'landscape');
                ->setPaper($customPaper);

            return $pdf->download('rekap-penerimaan.pdf');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Tidak dapat mencetak rekap penerimaan!<br> *Silahkan hubungi administrator', 'error' => $e], 422);
        }
    }
}
