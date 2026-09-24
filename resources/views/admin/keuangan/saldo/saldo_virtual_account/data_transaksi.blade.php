@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">
@endsection
@section('content')
    <h3 class="page-heading d-flex text-gray-900 fw-bold flex-column justify-content-center my-0">
        Data Transaksi
    </h3>
    <ul class="breadcrumb breadcrumb-style2">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.index') }}" class="text-hover-primary">Beranda</a>
        </li>
        @isset($title)
            <li class="breadcrumb-item">{{ $title }}</li>
        @endisset
        @isset($mainTitle)
            <li class="breadcrumb-item">
                <a href="{{ route('admin.keuangan.saldo.saldo-virtual-account.index') }}" class="text-hover-primary">{{ $mainTitle }}</a>
            </li>
        @endisset
        <li class="breadcrumb-item active">Data Transaksi</li>
    </ul>

    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">Data Transaksi</h5>
            <div class="card-header-elements ms-auto">
                <a href="{{ route('admin.keuangan.saldo.saldo-virtual-account.index') }}" class="btn btn-outline-primary btn-sm">
                    <span class="ri-arrow-left-s-line me-1"></span>
                    Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 row">
                                <label for="filter_dari_tanggal" class="col-sm-4 col-form-label form-label">Dari Tanggal</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="filter_dari_tanggal"
                                           name="filter[dari_tanggal]" placeholder="dd-mm-yyyy" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 row">
                                <label for="filter_sampai_tanggal" class="col-sm-4 col-form-label form-label">Sampai Tanggal</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="filter_sampai_tanggal"
                                           name="filter[sampai_tanggal]" placeholder="dd-mm-yyyy" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <button type="reset" class="btn btn-secondary">
                            <span class="ri-reset-left-line me-2"></span>
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="ri-search-line me-2"></span>
                            Cari
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
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.js?v=20260924-id3')}}"></script>
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>

    <script type="text/javascript">
        let dtOptions = {
            tableId: 'main_table',
            formId: 'filterForm',
            columnUrl: '{{ $columnsUrl }}',
            dataUrl: '{{ $datasUrl }}',
            dataColumns: [],
            thead: true,
            tfoot: false,
            paging: true,
            searching: true,
            fixedHeader: false,
            scrollX: true,
            pageLength: 25,
            lengthMenu: [25, 100],
            buttons: ['excel', 'pdf', 'print'],
            pdfOrientation: 'landscape',
            pdfPageSize: 'A4',
            pdfMargins: [16, 20, 16, 20],
            pdfFontSize: 8,
        };

        document.addEventListener('DOMContentLoaded', function () {
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
                        dariTanggal.datepicker('setEndDate', null);
                        sampaiTanggal.datepicker('setStartDate', null);
                        dataReFilter(dtOptions.tableId);
                    }, 0);
                });
            }
        });
    </script>
@endsection
