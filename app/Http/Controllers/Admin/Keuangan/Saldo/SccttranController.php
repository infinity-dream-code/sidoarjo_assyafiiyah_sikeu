<?php

namespace App\Http\Controllers\Admin\Keuangan\Saldo;

use App\Http\Controllers\Controller;
use App\Models\mst_kelas;
use App\Models\mst_sekolah;
use App\Models\mst_thn_aka;
use App\Models\scctcust;
use App\Models\sccttran;
use App\Support\MetodeBayarHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SccttranController extends Controller
{
    public ?string $sekolah = null;

    private function topUpVaScope($query, string $tablePrefix = 'sccttran.')
    {
        $metodeColumn = $tablePrefix . 'METODE';
        $isReversalColumn = $tablePrefix . 'isreversal';

        return $query
            ->whereRaw("UPPER(TRIM(COALESCE({$metodeColumn}, ''))) = 'TOP UP'")
            ->where(function ($q) use ($isReversalColumn) {
                $q->whereNull($isReversalColumn)
                    ->orWhere($isReversalColumn, 0)
                    ->orWhere($isReversalColumn, '0');
            });
    }

    public function __construct()
    {
//        $this->middleware('CheckUserRoleOrPermission:pimpinan');

        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->sekolah = Auth::user()->sekolah;
            }

            return $next($request);
        });

        $this->title = 'Keuangan';
        $this->mainTitle = 'Data Transfer VA';
        $this->dataTitle = 'Data Transfer VA';
        $this->datasUrl = route('admin.keuangan.data-transfer-va.get-data');
        $this->columnsUrl = route('admin.keuangan.data-transfer-va.get-column');
    }

    public function index()
    {
        $data['title'] = $this->title;
        $data['mainTitle'] = $this->mainTitle;
        $data['dataTitle'] = $this->dataTitle;
        $data['columnsUrl'] = $this->columnsUrl;
        $data['datasUrl'] = $this->datasUrl;
        $data['thn_aka'] = mst_thn_aka::select(['thn_aka'])
            ->whereNotNull('thn_aka')
            ->distinct()
            ->orderBy('thn_aka', 'desc')
            ->get();
        $schoolCodes = blank($this->sekolah) ? [] : [trim((string) $this->sekolah)];
        $data['sekolah'] = mst_sekolah::select(['CODE01', 'DESC01'])
            ->when(!empty($schoolCodes), fn ($q) => $q->whereIn('CODE01', $schoolCodes))
            ->orderBy('DESC01')
            ->get();
        $data['kelas'] = mst_kelas::dropdownQuery($this->sekolah)
            ->orderBy('unit')
            ->orderByRaw("CASE WHEN jenjang REGEXP '^[0-9]+$' THEN 0 ELSE 1 END, jenjang")
            ->orderByRaw("CASE WHEN kelas REGEXP '^[0-9]+$' THEN 0 ELSE 1 END, kelas")
            ->get();
        $data['metodes'] = collect(['TOP UP']);

        return view('admin.keuangan.saldo.sccttran.index', $data);
    }

    public function getColumn()
    {
        return [
            ['data' => null, 'columnType' => 'row', 'name' => 'No', 'exportable' => true],
            ['data' => 'NOCUST', 'name' => 'NIS', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'NOVA', 'name' => 'NO VA', 'exportable' => true],
            ['data' => 'NMCUST', 'name' => 'NAMA', 'searchable' => true, 'orderable' => true, 'exportable' => true],
            ['data' => 'METODE', 'name' => 'Metode', 'orderable' => true, 'exportable' => true],
            ['data' => 'TRXDATE', 'name' => 'Tanggal Transaksi', 'orderable' => true, 'columnType' => 'timestamp', 'exportable' => true],
            ['data' => 'NOREFF', 'name' => 'No Ref', 'orderable' => true, 'exportable' => true],
            ['data' => 'NOMINAL', 'name' => 'Nominal', 'orderable' => true, 'className' => 'dt-right', 'columnType' => 'currency', 'exportable' => true],
        ];
    }

    public function getData(Request $request)
    {
        $custid = $request->CUSTID;
        $filters = [];
        $filterQuery = null;

        $draw = $request->get('draw');
        $start = (int) $request->get('start', 0);
        $rowperpage = (int) $request->get('length', 10);

        $columnName_arr = $request->get('columns', []);
        $search_arr = $request->get('search', []);
        $order_arr = $request->get('order', []);

        $defaultColumn = 'sccttran.TRXDATE';
        $defaultOrder = 'desc';
        $columnName = $defaultColumn;
        $columnSortOrder = $defaultOrder;

        if (!empty($order_arr)) {
            $columnIndex = $order_arr[0]['column'] ?? null;
            $columnSortOrder = $order_arr[0]['dir'] ?? $defaultOrder;
            $requestedColumn = ($columnIndex !== null && isset($columnName_arr[$columnIndex]['data']))
                ? $columnName_arr[$columnIndex]['data']
                : null;

            if (!$requestedColumn || $requestedColumn === 'no') {
                $columnName = $defaultColumn;
            } elseif ($requestedColumn === 'NOMINAL') {
                $columnName = 'sccttran.KREDIT';
            } elseif (!str_contains((string) $requestedColumn, '.')) {
                $columnName = in_array($requestedColumn, ['NOCUST', 'NMCUST', 'NUM2ND', 'NOVA'], true)
                    ? 'scctcust.' . ($requestedColumn === 'NOVA' ? 'NOCUST' : $requestedColumn)
                    : 'sccttran.' . $requestedColumn;
            } else {
                $columnName = $requestedColumn;
            }
        }

        $searchValue = $search_arr['value'] ?? '';

        $filter = $request->input('filter', []);
        if (is_array($filter)) {
            foreach ($filter as $key => $val) {
                if (is_array($val)) {
                    $val = collect($val)
                        ->filter(fn ($item) => !blank($item) && strtolower((string) $item) !== 'all')
                        ->values()
                        ->all();
                    if ($val === []) {
                        continue;
                    }
                } elseif (blank($val) || strtolower((string) $val) === 'all') {
                    continue;
                }

                $colName = match ($key) {
                    'dari_tanggal', 'sampai_tanggal' => 'sccttran.TRXDATE',
                    'kelas' => 'scctcust.CODE03',
                    'nama' => 'scctcust.NMCUST',
                    'nis' => 'scctcust.NOCUST',
                    'sekolah' => 'scctcust.CODE01',
                    'angkatan' => 'scctcust.DESC04',
                    'metode' => 'sccttran.METODE',
                    default => null
                };

                if (!$colName) {
                    continue;
                }

                if (in_array($key, ['dari_tanggal', 'sampai_tanggal'], true)
                    && is_string($val)
                    && preg_match('/^\d{2}-\d{2}-\d{4}$/', $val)
                ) {
                    $date = $key === 'dari_tanggal'
                        ? Carbon::createFromFormat('d-m-Y', $val)->startOfDay()
                        : Carbon::createFromFormat('d-m-Y', $val)->endOfDay();
                    if ($date) {
                        $operator = $key === 'dari_tanggal' ? '>=' : '<=';
                        $filters[] = [$colName, $operator, $date];
                    }
                    continue;
                }

                if (in_array($key, ['nama', 'nis', 'metode'], true) && is_string($val)) {
                    $filters[] = [$colName, 'like', '%' . $val . '%'];
                    continue;
                }

                if (is_array($val)) {
                    $filters[] = [$colName, 'in', $val];
                } else {
                    $filters[] = [$colName, '=', $val];
                }
            }
        }

        if ($custid) {
            $filters[] = ['sccttran.CUSTID', '=', $custid];
        }

        $schoolCodes = blank($this->sekolah) ? [] : [trim((string) $this->sekolah)];
        if (!empty($schoolCodes)) {
            $filters[] = ['scctcust.CODE01', 'in', $schoolCodes];
        }

        if (!empty($filters)) {
            $filterQuery = function ($query) use ($filters) {
                foreach ($filters as $filterItem) {
                    if (count($filterItem) !== 3) {
                        continue;
                    }
                    if (($filterItem[1] ?? null) === 'in' && is_array($filterItem[2] ?? null)) {
                        $query->whereIn($filterItem[0], $filterItem[2]);
                    } else {
                        $query->where($filterItem[0], $filterItem[1], $filterItem[2]);
                    }
                }
            };
        }

        $whereAny = [
            'scctcust.NMCUST',
            'scctcust.NOCUST',
            'scctcust.NUM2ND',
            'sccttran.METODE',
            'sccttran.NOREFF',
        ];

        $select = [
            'scctcust.NMCUST',
            'scctcust.NOCUST',
            'scctcust.NUM2ND',
            'sccttran.METODE',
            'sccttran.TRXDATE',
            'sccttran.NOREFF',
            'sccttran.FIDBANK',
            'sccttran.KDCHANNEL',
            'sccttran.DEBET',
            'sccttran.KREDIT',
            'sccttran.REFFBANK',
            'sccttran.TRANSNO',
        ];

        $query = $this->topUpVaScope(
            sccttran::query()
                ->leftJoin('scctcust', 'scctcust.CUSTID', 'sccttran.CUSTID')
        );

        if (!blank($searchValue)) {
            $query->where(function ($q) use ($whereAny, $searchValue) {
                $sanitizeSearch = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $searchValue);
                foreach ($whereAny as $column) {
                    $q->orWhere($column, 'like', '%' . $sanitizeSearch . '%');
                }
            });
        }

        $query->where(function ($q) use ($filterQuery) {
            if ($filterQuery) {
                $filterQuery($q);
            }
        });

        $totalRecords = $this->topUpVaScope(sccttran::query())->count();
        $totalRecordswithFilter = (clone $query)->count();
        $totalNominal = (int) (clone $query)->sum('sccttran.KREDIT');

        $records = (clone $query)->orderBy($columnName, $columnSortOrder)
            ->select($select)
            ->skip($start)
            ->take($rowperpage > 0 ? $rowperpage : 10)
            ->get()
            ->map(function ($item) {
                if ($item->NOCUST && $item->NOCUST != '-') {
                    $NOVA = scctcust::showVA($item->NOCUST);
                } else {
                    $NOVA = scctcust::showVA($item->NUM2ND);
                }
                $item->NOVA = $NOVA;
                $item->NOMINAL = (int) ($item->KREDIT ?? 0);
                $item->METODE = MetodeBayarHelper::translateMetodeLabel($item->METODE ?? null);
                unset($item->DEBET, $item->KREDIT);

                return $item;
            })->toArray();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordswithFilter,
            'data' => $records,
            'totals' => [
                'nominal' => [
                    'location' => 7,
                    'value' => $totalNominal,
                    'columnType' => 'currency',
                ],
            ],
        ]);
    }
}
