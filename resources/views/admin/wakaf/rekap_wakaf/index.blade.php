@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">
    <style>
        #main_table tfoot th {
            background-color: #f5f5f9;
        }
    </style>
@endsection

@section('content')
    <h3 class="page-heading d-flex text-gray-900 fw-bold flex-column justify-content-center my-0">
        {{($dataTitle??($mainTitle??($title??'')))}}
    </h3>
    <ul class="breadcrumb breadcrumb-style2">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.index') }}" class="text-hover-primary">Beranda</a>
        </li>
        <li class="breadcrumb-item">{{ $title ?? 'Wakaf' }}</li>
        <li class="breadcrumb-item">{{ $mainTitle ?? 'Wakaf' }}</li>
        <li class="breadcrumb-item active">{{ $dataTitle ?? 'Rekap Wakaf' }}</li>
    </ul>

    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <fieldset class="form-fieldset">
                    <h5>Filter</h5>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="filter[wakaf_id]">Nama Wakaf</label>
                            <select class="form-select" id="filter[wakaf_id]" name="filter[wakaf_id]"
                                    data-control="select2" data-placeholder="Pilih nama wakaf">
                                <option value="all" @selected(request('filter.wakaf_id', 'all') === 'all')>Semua</option>
                                @foreach($wakafList ?? [] as $wakaf)
                                    <option value="{{ $wakaf->idincrement }}" @selected((string) request('filter.wakaf_id') === (string) $wakaf->idincrement)>
                                        {{ $wakaf->namaSumbangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="filter[nama]">Cari Nama Wakaf</label>
                            <input type="text" class="form-control" id="filter[nama]" name="filter[nama]"
                                   placeholder="Ketik nama wakaf" value="{{ request('filter.nama', '') }}">
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="filter[status]">Status Wakaf</label>
                            <select class="form-select" id="filter[status]" name="filter[status]">
                                @php($filterStatus = (string) request('filter.status', 'all'))
                                <option value="all" @selected($filterStatus === 'all')>Semua</option>
                                <option value="1" @selected($filterStatus === '1')>Aktif</option>
                                <option value="0" @selected($filterStatus === '0')>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="filter_dari_tanggal">Dari Tanggal</label>
                            <input type="text" class="form-control" id="filter_dari_tanggal"
                                   name="filter[dari_tanggal]" placeholder="dd-mm-yyyy"
                                   value="{{ request('filter.dari_tanggal', '') }}">
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="filter_sampai_tanggal">Sampai Tanggal</label>
                            <input type="text" class="form-control" id="filter_sampai_tanggal"
                                   name="filter[sampai_tanggal]" placeholder="dd-mm-yyyy"
                                   value="{{ request('filter.sampai_tanggal', '') }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <button type="reset" class="btn btn-secondary">
                            <span class="ri-reset-left-line me-2"></span>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="ri-search-line me-2"></span>Cari
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="card-datatable table-responsive text-nowrap">
            <table class="table table-sm table-bordered table-hover" id="main_table">
                <thead class="table-light"></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.js?v=20260924-id3')}}?v=20260605-pdf-margin"></script>
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>

    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);

        const dtOptions = {
            tableId: 'main_table',
            formId: 'filterForm',
            columnUrl: '{{($columnsUrl??null)}}',
            dataUrl: '{{($datasUrl??null)}}',
            dataColumns: [],
            thead: true,
            tfoot: true,
            paging: true,
            searching: true,
            fixedHeader: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: ['copy', 'excel', 'pdf', 'print'],
            pdfOrientation: 'landscape',
            pdfPageSize: 'A4',
            pdfMargins: [28, 20, 44, 20],
            pdfFontSize: 8,
            pdfHeaderFontSize: 9,
            pdfWidths: [28, '*', 92, 52, 78, 68],
        };

        document.addEventListener('DOMContentLoaded', function () {
            if (select2.length) {
                select2.each(function () {
                    const $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Pilih nama wakaf',
                        dropdownParent: $this.parent(),
                        minimumResultsForSearch: 0,
                        allowClear: true,
                    });
                });
            }

            const dariTanggal = $('#filter_dari_tanggal');
            const sampaiTanggal = $('#filter_sampai_tanggal');

            dariTanggal.datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
            }).on('changeDate', function (e) {
                sampaiTanggal.datepicker('setStartDate', e.date);
            });

            sampaiTanggal.datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
            }).on('changeDate', function (e) {
                dariTanggal.datepicker('setEndDate', e.date);
            });

            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);

                const filterForm = $(`#${dtOptions.formId}`);
                filterForm.on('submit', function (e) {
                    e.preventDefault();
                    dataReFilter(dtOptions.tableId);
                });
                filterForm.on('reset', function () {
                    setTimeout(function () {
                        const select2InForm = select2.filter(`#${dtOptions.formId} [data-control='select2']`);
                        if (select2InForm.length) {
                            select2InForm.val('all').trigger('change');
                        }
                        dataReFilter(dtOptions.tableId);
                    }, 0);
                });
            }
        });
    </script>
@endsection
