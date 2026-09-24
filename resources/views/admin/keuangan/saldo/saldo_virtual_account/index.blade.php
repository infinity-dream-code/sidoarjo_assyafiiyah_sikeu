@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2-bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css')}}">
@endsection
@section('content')
    <h3 class="page-heading d-flex text-gray-900 fw-bold flex-column justify-content-center my-0">
        {{($dataTitle??($mainTitle??($title??'')))}}
    </h3>
    <ul class="breadcrumb breadcrumb-style2">
        <li class="breadcrumb-item">
            <a href="{{route('admin.index')}}" class="text-hover-primary">Beranda</a>
        </li>
        @if(isset($title))
            <li class="breadcrumb-item">
                {{$title}}
            </li>
        @endif
        @if(isset($mainTitle))
            <li class="breadcrumb-item">
                {{$mainTitle}}
            </li>
        @endif
        @if(isset($dataTitle) && isset($mainTitle) && $mainTitle != $dataTitle)
            <li class="breadcrumb-item active">
                {{$dataTitle}}
            </li>
        @endif
    </ul>

    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <input type="hidden" id="filter[saldo_positif]" name="filter[saldo_positif]" value="">
                <fieldset class="form-fieldset">
                    <h5>Filter</h5>
                    <div class="row">
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="filter[angkatan]">
                                Angkatan Siswa
                            </label>
                            <div class="col-sm-10">
                                <select class="form-select" id="filter[angkatan]"
                                        name="filter[angkatan]"
                                        data-control="select2"
                                        data-placeholder="Pilih Angkatan Siswa">
                                    <option value="all">Semua</option>
                                    @isset($thn_aka)
                                        @foreach($thn_aka as $item)
                                            <option
                                                value="{{$item->thn_aka}}">{{$item->thn_aka}}</option>
                                        @endforeach
                                    @else
                                        <option>data kosong</option>
                                    @endisset
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="filter[sekolah]">
                                Sekolah
                            </label>
                            <div class="col-sm-10">
                                <select class="form-select" id="filter[sekolah]" name="filter[sekolah]"
                                        data-control="select2" data-placeholder="Pilih Sekolah">
                                    <option value="all">Semua</option>
                                    @isset($sekolah)
                                        @foreach($sekolah as $item)
                                            <option
                                                    value="{{$item->CODE01}}">{{$item->DESC01}}</option>
                                        @endforeach
                                    @else
                                        <option>data kosong</option>
                                    @endisset
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="filter[kelas]">
                                Kelas
                            </label>
                            <div class="col-sm-10">
                                <select class="form-select" id="filter[kelas]" name="filter[kelas]"
                                        data-control="select2" data-placeholder="Pilih Kelas">
                                    <option value="all">Semua</option>
                                    @isset($kelas)
                                        @foreach($kelas as $item)
                                            <option
                                                    value="{{$item->id}}">{{$item->unit}}
                                                - {{$item->jenjang}} {{$item->kelas}}</option>
                                        @endforeach
                                    @else
                                        <option>data kosong</option>
                                    @endisset
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-sm-2 col-form-label" for="filter[siswa]">
                                Siswa
                            </label>
                            <div class="col-sm-10">
                                <input class="form-control" id="filter[siswa]" name="filter[siswa]"
                                       placeholder="Masukkan NIS/NAMA Siswa" data-placeholder="Pilih siswa">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex justify-content-center justify-content-md-end gap-4">
                            <button type="reset" class="btn btn-secondary">
                                <span class="ri-reset-left-line me-2"></span>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <span class="ri-search-line me-2"></span>
                                Cari
                            </button>
                            <button type="button" class="btn btn-success" id="btn-cari-saldo">
                                <span class="ri-wallet-3-line me-2"></span>
                                Cari Saldo
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <div id="saldo-va-action-toolbar" class="d-none">
            <a href="{{ $dataTransaksiUrl ?? route('admin.keuangan.saldo.saldo-virtual-account.data-transaksi.index') }}"
               class="btn btn-primary btn-sm me-2"
               id="btn-data-transaksi">
                <span class="ri-list-check-2 me-1"></span>
                Data Transaksi
            </a>
        </div>
        <div class="card-datatable table-responsive text-nowrap">
            <table class="table table-sm table-bordered table-hover"
                   id="main_table">
                <thead class="table-light">

                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.js?v=20260924-id3')}}"></script>
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>
    <script src="{{asset('main/libs/select2/id.min.js')}}"></script>

    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        let dtOptions = {
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
            scrollX: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: ['copy', 'excel', 'pdf', 'print'],
            pdfOrientation: 'landscape',
            pdfPageSize: 'A4',
            pdfMargins: [16, 20, 16, 20],
            pdfFontSize: 8,
            pdfHeaderFontSize: 9,
        };

        document.addEventListener("DOMContentLoaded", function () {
            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
                mountSaldoVaActionButtons();
                if (dtOptions.formId) {
                    let filterForm = $(`#${dtOptions.formId}`);
                    filterForm.on('submit', function (e) {
                        e.preventDefault();
                        dataReFilter(dtOptions.tableId);
                    });
                    $('#btn-cari-saldo').on('click', function () {
                        $(`#${dtOptions.formId} [name="filter[saldo_positif]"]`).val('1');
                        dataReFilter(dtOptions.tableId);
                    });
                    filterForm.on('reset', function (e) {
                        setTimeout(function () {
                            $(`#${dtOptions.formId} [name="filter[saldo_positif]"]`).val('');
                            dataReFilter(dtOptions.tableId);
                            const select2InForm = select2.filter(`#${dtOptions.formId} [data-control='select2']`);
                            if (select2InForm.length) {
                                select2InForm.each(function () {
                                    let $this = $(this);
                                    $this.trigger('change');
                                });
                            }
                        }, 0)
                    });
                }
            }

            if (select2.length) {
                select2.each(function () {
                    let $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent(),
                        minimumResultsForSearch: 0
                    });
                });
            }
        });

        function mountSaldoVaActionButtons(attempt = 0) {
            const actionBar = document.querySelector(`#${dtOptions.tableId}_wrapper .dt-action-buttons`);
            const dtButtons = actionBar?.querySelector('.dt-buttons');
            const dataTransaksiBtn = document.getElementById('btn-data-transaksi');
            const toolbar = document.getElementById('saldo-va-action-toolbar');

            if (!actionBar || !dtButtons || !dataTransaksiBtn) {
                if (attempt < 40) {
                    setTimeout(() => mountSaldoVaActionButtons(attempt + 1), 250);
                }
                return;
            }

            if (dataTransaksiBtn.closest('.dt-action-buttons') !== actionBar) {
                actionBar.insertBefore(dataTransaksiBtn, dtButtons);
            }

            if (toolbar && !toolbar.children.length) {
                toolbar.remove();
            }
        }

    </script>

    {!! ($modalLink??'') !!}
@endsection
