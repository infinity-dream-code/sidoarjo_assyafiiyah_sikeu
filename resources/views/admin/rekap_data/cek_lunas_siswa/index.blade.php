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

    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2-bootstrap.css')}}">
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
                <form id="filterForm">
                    <h5>Filter</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3 row">
                                <label for="filter[tahun_pelajaran]" class="col-sm-4 col-form-label form-label">
                                    Tahun Pelajaran
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[tahun_pelajaran]"
                                            name="filter[tahun_pelajaran]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
                                        <option value="all" selected>Semua</option>
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
                            <div class="mb-3 row">
                                <label for="filter[nis]"
                                       class="col-sm-4 col-form-label text-capitalize form-label">NIS</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="nis" id="filter[nis]" name="filter[nis]">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter[nama]"
                                       class="col-sm-4 col-form-label text-capitalize form-label">Nama</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="nama" id="filter[nama]" name="filter[nama]">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row">
                                <label for="filter[kelas]" class="col-sm-4 col-form-label form-label">
                                    Kelas
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[kelas]"
                                            name="filter[kelas]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
                                        <option value="all">Semua</option>
                                        @isset($kelas)
                                            @foreach($kelas as $item)
                                                <option value="{{$item->id}}">
                                                    {{$item->unit}} - {{$item->jenjang}} {{$item->kelas}} </option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter[thn_aka]" class="col-sm-4 col-form-label form-label">
                                    Tahun Angkatan
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[thn_aka]"
                                            name="filter[thn_aka]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
                                        <option value="all">Semua</option>
                                        @isset($thn_aka)
                                            @foreach($thn_aka as $item)
                                                <option value="{{$item->thn_aka}}">
                                                    {{$item->thn_aka}} </option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter[nama_tagihan]" class="col-sm-4 col-form-label form-label">
                                    Nama Tagihan
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[nama_tagihan]"
                                            name="filter[nama_tagihan]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
                                        <option value="all">Semua</option>
                                        @isset($tagihan)
                                            @foreach($tagihan as $item)
                                                <option
                                                    value="{{$item->tagihan}}">{{$item->tagihan}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="w-100 py-5">
                    <div class="row">
                        <div class="d-flex justify-content-center justify-content-md-end gap-4">
                            <button type="button" class="btn btn-facebook" id="cetak-kartu-siswa">
                                <span class="ri-info-card-line me-2"></span>
                                Cetak Kartu Siswa
                            </button>
                            <button type="button" class="btn btn-facebook" id="cetak-pelaporan">
                                <span class="ri-printer-line me-2"></span>
                                Cetak Pelaporan
                            </button>
                            <button type="reset" class="btn btn-secondary button_reset_cari">
                                <span class="ri-reset-left-line me-2"></span>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary button_cari_cari">
                                <span class="ri-search-line me-2"></span>
                                Cari
                            </button>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            buttons: ['excel', 'pdf'],
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

        const cetakPelaporan = document.getElementById('cetak-pelaporan');
        if (cetakPelaporan) {
            cetakPelaporan.addEventListener('click', function (e) {
                e.preventDefault();
                loadingAlert('Membuat Laporan');
                let url = '{{route('admin.rekap-data.cek-lunas-siswa.cetak-pelaporan')}}';
                const form = new FormData(document.getElementById('filter-form'));
                const params = new URLSearchParams();
                for (const [key, value] of form.entries()) {
                    params.append(key, value);
                }
                const fullUrl = `${url}?${params.toString()}`;
                const request = new Request(fullUrl, {
                    method: "GET",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/pdf'
                    }
                });
                fetch(request)
                    .then(res => res.blob())
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        window.open(url, '_blank');
                        Swal.close();
                    })
                    .catch(error => {
                        errorAlert('Gagal mencetak laporan');
                        console.log(error)
                    });
            })
        }

        document.getElementById('cetak-kartu-siswa').addEventListener('click', function (e) {
            e.preventDefault();
            loadingAlert('Membuat Kartu Siswa');
            let url = '{{route('admin.rekap-data.cek-lunas-siswa.cetak-kartu-siswa')}}';
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
                .then(res => res.blob())
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    window.open(url, '_blank');
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
                        console.log(error)
                    }
                });
        })
    </script>
@endsection
