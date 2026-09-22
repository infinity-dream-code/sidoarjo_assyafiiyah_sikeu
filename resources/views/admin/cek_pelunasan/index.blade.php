@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">

    <style>
        .input-tagihan {
            min-width: 200px;
        }
    </style>
    <style>
        table.dataTable tr.selected {
            border-top: 2px solid var(--bs-primary);
            border-bottom: 2px solid var(--bs-primary);
            border-left: none;
            border-right: none;
        }
    </style>

    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">

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

    <form id="filter-form" class="mainForm">
        @csrf
        <div class="card mb-6">
            <div class="card-header header-elements">
                <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
                <div class="card-header-elements ms-auto">
                    <div class="w-100">
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body py-0">
                <div class="row px-5 mb-2">
                    <ul class="list-group list-group-timeline">
                        <li class="list-group-item list-group-timeline-danger">
                            <strong>Pastikan browser anda tidak memblokir <i>POP-UP</i>!</strong>
                        </li>
                    </ul>
                </div>
                <form id="filterForm">
                    <fieldset class="form-fieldset">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-5">
                                    <label class="form-label" for="tahun_akademik">
                                        Tahun Akademik
                                    </label>
                                    <select class="form-select" id="tahun_akademik"
                                            name="filter[tahun_akademik]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
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
                                <div class="mb-5">
                                    <label class="form-label" for="post">
                                        Nama Tagihan
                                    </label>
                                    <select class="form-select" id="post"
                                            name="filter[post][]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tagihan"
                                            multiple="multiple">
                                        <option value="all">Semua</option>
                                        @isset($post)
                                            @foreach($post as $item)
                                                <option
                                                    value="{{$item->tagihan}}">{{$item->tagihan}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label" for="status_bayar">
                                        Status bayar
                                    </label>
                                    <select class="form-select" id="status_bayar"
                                            name="filter[status_bayar]"
                                            data-control="select2"
                                            data-placeholder="Pilih Status Bayar">
                                        <option value="all">Semua</option>
                                        <option value="0" selected>Belum Lunas</option>
                                        <option value="1">Lunas</option>
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="form-label" for="metode_bayar">
                                        Metode Pembayaran
                                    </label>
                                    <select class="form-select" id="metode_bayar"
                                            name="filter[metode_bayar]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tagihan">
                                        @isset($metode_bayar)
                                            <option value="all">Semua</option>
                                            @foreach($metode_bayar as $key => $item)
                                                <option
                                                    value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="col mb-5">
                                    <label class="form-label" for="filter[angkatan]]">
                                        Angkatan Siswa
                                    </label>
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
                                <div class="col mb-5">
                                    <label class="form-label" for="filter[kelas]">
                                        Kelas
                                    </label>
                                    <select class="form-select" id="filter[kelas]" name="filter[kelas]"
                                            data-control="select2" data-placeholder="Pilih Kelas">
                                        <option value="all">Semua</option>
                                        @isset($kelas)
                                            @foreach($kelas as $item)
                                                <option
                                                    value="{{$item->unit}}~{{$item->jenjang}}~{{$item->kelas}}">{{$item->unit}}
                                                    - {{$item->jenjang}} {{$item->kelas}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                                <div class="col mb-5">
                                    <label class="form-label" for="filter[siswa]">
                                        Siswa
                                    </label>
                                    <input class="form-control" id="filter[siswa]" name="filter[siswa]"
                                           placeholder="Masukkan NIS/NAMA Siswa" data-placeholder="Pilih siswa">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-center flex-column flex-md-row justify-content-md-end gap-4">
{{--                                <button type="button" class="btn btn-facebook btn-print-rekap" id="cetak-rekap">--}}
{{--                                    <span class="ri-file-text-line me-2"></span>--}}
{{--                                    Cetak Rekap--}}
{{--                                </button>--}}
                                <button type="button" class="btn btn-facebook" id="cetak-kartu-siswa">
                                    <span class="ri-profile-line me-2"></span>
                                    Cetak Kartu Siswa
                                </button>
                                <button type="reset" class="btn btn-secondary" disabled>
                                    <span class="ri-reset-left-line me-2"></span>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary" disabled>
                                    <span class="ri-search-line me-2"></span>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </fieldset>
                </form>
                <div class="row px-5 mb-2">
                    <ul class="list-group list-group-timeline">
                        <li class="list-group-item list-group-timeline-warning">
                            Untuk mencetak kartu siswa, silahkan pilih siswa terlebih dahulu!
                        </li>
                        <li class="list-group-item list-group-timeline-warning">
                            Cetak kartu siswa, hanya bisa dilakukan per siswa!
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-datatable table-responsive text-nowrap px-5card-siswa">
                <table class="table table-sm table-bordered table-hover"
                       id="main_table">
                    <thead class="table-light">
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>

    <script>
        const select2 = $(`[data-control='select2']`);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let dtOptions = {
            tableId: 'main_table',
            formId: 'filter-form',
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
            select: true,
        };


        function getData() {
            if (dataUrl) {
                loadingAlert('Memuat Data....');
                let form = new FormData(filterForm)
                const params = new URLSearchParams();
                for (const [key, value] of form.entries()) {
                    params.append(key, value);
                }
                params.append('draw', 2);
                const fullUrl = `${dataUrl}?${params.toString()}`;

                let request = new Request(
                    fullUrl, {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    }
                )

                fetch(request)
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw {status: response.status, message: data.message || response.statusText};
                        }
                        return data;
                    })
                    .then(data => {
                        tableData.rows().deselect();
                        tableData.clear();
                        tableData.rows.add(data.data);
                        tableData.draw();

                        Swal.close();
                    })
                    .catch(error => {
                        if (error.status === 422) {
                            const errors = error.error || error.errors;
                            errorAlert(error.message);
                            if (errors) {
                                processErrors(errors)
                            }
                        } else {
                            const errorMessages = {
                                401: '',
                                403: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
                                404: 'Halaman yang dituju tidak ditemukan 🧐',
                                405: 'Metode tidak valid 🧐 <br>silahkan muat ulang halaman dan coba lagi!',
                                419: '',
                                429: 'Terlalu banyak permintaan akses <br>silahkan tunggu beberapa saat 🙏',
                            };
                            errorAlert(errorMessages[error.status] || "Terjadi kesalahan, silahkan coba memuat ulang halaman");
                        }
                    });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
                if (dtOptions.formId) {
                    let filterForm = $(`#${dtOptions.formId}`);
                    filterForm.on('submit', function (e) {
                        e.preventDefault();
                        dataReFilter(dtOptions.tableId);
                    });
                    filterForm.on('reset', function (e) {
                        setTimeout(function () {
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
                    // select2Focus($this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent(),
                    });
                });
            }
        })

        // document.getElementById('cetak-pelaporan').addEventListener('click', function (e) {
        //     e.preventDefault();
        //     infoAlert('Testing cetak pelaporan');
        // })

        document.getElementById('cetak-kartu-siswa').addEventListener('click', function (e) {
            e.preventDefault();
            loadingAlert('Membuat Kartu Siswa');
            let url = '{{route('admin.cek-pelunasan.cetak-kartu-siswa')}}';
            const form = new FormData(document.getElementById('filter-form'));
            const params = new URLSearchParams();
            for (const [key, value] of form.entries()) {
                params.append(key, value);
            }
            let data = DT[`${dtOptions.tableId}`].rows({selected: true}).data();

            if (!data[0]) {
                warningAlert('silahkan pilih siswa!')
                return;
            }
            params.append('custid', data[0].CUSTID)
            const fullUrl = `${url}?${params.toString()}`;
            const request = new Request(
                fullUrl, {
                    method: "GET",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/pdf'
                    }
                });
            fetch(request)
                .then(async res => {
                    const contentType = res.headers.get('content-type') || '';
                    if (!res.ok || contentType.includes('application/json')) {
                        const payload = contentType.includes('application/json')
                            ? await res.json().catch(() => ({}))
                            : {};
                        throw {
                            status: res.status,
                            message: payload.message || payload.error || 'Gagal membuat kartu siswa',
                        };
                    }
                    return res.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    window.open(url, '_blank');
                    Swal.close();
                })
                .catch(error => {
                    if (error.status === 422 || error.message) {
                        errorAlert(error.message || 'Gagal membuat kartu siswa');
                    } else {
                        const errorMessages = {
                            401: '',
                            403: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
                            404: 'Halaman yang dituju tidak ditemukan 🧐',
                            405: 'Metode tidak valid 🧐 <br>silahkan muat ulang halaman dan coba lagi!',
                            419: '',
                            429: 'Terlalu banyak permintaan akses <br>silahkan tunggu beberapa saat 🙏',
                        };
                        errorAlert(errorMessages[error.status] || "Terjadi kesalahan, silahkan coba memuat ulang halaman");
                        console.log(error)
                    }
                });
        })
    </script>
@endsection
