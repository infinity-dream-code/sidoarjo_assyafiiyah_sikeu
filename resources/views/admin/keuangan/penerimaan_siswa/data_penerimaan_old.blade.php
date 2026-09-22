@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')

        <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
        <link rel="stylesheet" href="{{asset('main/libs/select2/select2-bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">
@endsection
@section('content')
    <h3 class="page-heading d-flex text-gray-900 fw-bold flex-column justify-content-center my-0">
        @if(isset($dataTitle) && isset($mainTitle) && $mainTitle != $dataTitle)
            {{$mainTitle .' - '.$dataTitle}}
        @else
            {{$mainTitle??$title??''}}
        @endif
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
        <div class="card-header">
            <div class="row mb-3">
                <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle??$title)}}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row px-5 mb-2">
                <ul class="list-group list-group-timeline">
                    <li class="list-group-item list-group-timeline-danger">
                        <strong>Pastikan telah mengisi Tanggal Transaksi!</strong>
                    </li>
                    <li class="list-group-item list-group-timeline-danger">
                        <strong>Pastikan browser anda tidak memblokir <i>POP-UP</i>!</strong>
                    </li>
                </ul>
            </div>
            <form id="rekapForm">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3 row">
                                <label for="filter[tahun_akademik]" class="col-sm-4 col-form-label form-label-sm">Tahun
                                    Akademik</label>
                                <div class="col">
                                    <select class="form-select select2--small" id="filter[tahun_akademik]"
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
                            </div>

                            <div class="mb-3 row">
                                <label for="filter[nis]" class="col-sm-4 col-form-label form-label-sm">Nis</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="Masukkan nis siswa" id="filter[nis]" name="filter[nis]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter[nama]" class="col-sm-4 col-form-label text-capitalize form-label-sm">nama</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="Masukkan nama siswa" id="filter[nama]" name="filter[nama]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter_dari_tanggal"
                                       class="col-sm-4 col-form-label text-capitalize form-label-sm">dari
                                    tanggal</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="dari tanggal" id="filter_dari_tanggal"
                                           name="filter[dari_tanggal]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter_sampai_tanggal"
                                       class="col-sm-4 col-form-label text-capitalize form-label-sm">sampai
                                    tanggal</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="sampai tanggal" id="filter_sampai_tanggal"
                                           name="filter[sampai_tanggal]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter[sekolah]"
                                       class="col-sm-4 col-form-label text-capitalize form-label-sm">sekolah</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="sekolah" id="filter[sekolah]" name="filter[sekolah]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter[periode_mulai]"
                                       class="col-sm-4 col-form-label text-capitalize form-label-sm">periode
                                    mulai</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="periode mulai" id="filter[periode_mulai]"
                                           name="filter[periode_mulai]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter[periode_akhir]"
                                       class="col-sm-4 col-form-label text-capitalize form-label-sm">periode
                                    akhir</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control-sm"
                                           placeholder="periode akhir" id="filter[periode_akhir]"
                                           name="filter[periode_akhir]">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label form-label-sm" for="filter[angkatan]">
                                    Angkatan Siswa
                                </label>
                                <div class="col">
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

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label form-label-sm" for="filter[kelas]">
                                    Kelas
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[kelas]" name="filter[kelas]"
                                            data-control="select2" data-placeholder="Pilih Kelas">
                                        <option value="all">Semua</option>
                                        @isset($kelas)
                                            @foreach($kelas as $item)
                                                <option
                                                    value="{{$item->jenjang}}">{{$item->unit}}
                                                    - {{$item->kelas}} {{$item->jenjang}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-capitalize form-label-sm" for="filter[bank]">
                                    bank
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[bank]" name="filter[bank]"
                                            data-control="select2" data-placeholder="Pilih bank">
                                        <option value="all">Semua</option>
                                        @isset($bank)
                                            @foreach($bank as $item)
                                                <option
                                                    value="{{$item->jenjang}}">{{$item->unit}}
                                                    - {{$item->bank}} {{$item->jenjang}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex justify-content-center flex-column flex-md-row justify-content-md-end gap-4">
                            <button type="button" class="btn btn-google-plus btn-print-rekap" disabled>
                                <span class="ri-file-pdf-2-line me-2"></span>
                                Cetak PDF
                            </button>
                            <button type="button" class="btn btn-google-plus download-pdf-new" id="download-pdf">
                                <span class="ri-file-pdf-2-line me-2"></span>
                                Cetak PDF Baru
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@3.0.1/dist/jspdf.umd.js" integrity="sha256-BmBQ5nHAaInyhYhkGc4YTXmG+AMJ0NPRFFyGlmS8jI8=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@5.0.2/dist/jspdf.plugin.autotable.min.js" integrity="sha256-BJIwyD1uOrOOT+LE+ZSym3vbjbgS+9ermOoMhE4IUcg=" crossorigin="anonymous"></script>

    {{--    <script src="{{asset('main/libs/select2/select2.full.min.js')}}"></script>--}}
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>
    <script src="{{asset('main/libs/moment/moment.js')}}"></script>
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>

    <form id="form-delete" class="mainForm">
        <div class="modal modal-blur fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-header ">
                        <div class="modal-title">
                            Batalkan Pembayaran
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-capitalize text-center py-4">
                        <span class="ri-delete-bin-line ri-3x"></span>
                        <h4>Batalkan pembayaran tagihan?</h4>
                        <div class="">
                            anda yakin akan Batalkan pembayaran tagihan?
                        </div>
                    </div>
                    <div class="modal-body py-4">
                        <fieldset class="form-fieldset">
                            <div class="mb-3 row">
                                <label for="nocust" class="col-sm-4 col-form-label form-label-sm">NIS</label>
                                <div class="col">
                                    <input type="text" readonly class="form-control  form-control-sm" id="nocust" name="nocust">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="nmcust" class="col-sm-4 col-form-label form-label-sm">Nama Siswa</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control form-control-sm" id="nmcust" name="nmcust">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="billnm" class="col-sm-4 col-form-label form-label-sm">Nama Tagihan</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control form-control-sm" id="billnm" name="billnm">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="billam" class="col-sm-4 col-form-label form-label-sm">Nominal</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control form-control-sm" id="billam" name="billam">
                                </div>
                            </div>
                        </fieldset>
                        <input type="hidden" id="delete_id" name="item_id" value="">
                        <input type="hidden" id="user_delete_id" name="custid" value="">
                    </div>
                    <div class="modal-footer ">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <input type="reset" class="btn btn-outline-secondary w-100" value="Batal"
                                           data-bs-dismiss="modal">
                                </div>
                                <div class="col">
                                    <input type="submit" value="Hapus" class="btn btn-danger w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let dtOptions = {
            tableId: 'main_table',
            formId: 'rekapForm',
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
        };

        const modalDeleteElement = document.getElementById('modal-delete');
        const modalDelete = new bootstrap.Modal(document.getElementById('modal-delete'));

        modalDeleteElement.addEventListener('hide.bs.modal', function () {
            const form = document.getElementById('form-delete');
            form.reset();
        });

        document.querySelector('#main_table tbody').addEventListener('click', function (e) {
            if (e.target.closest('.btn-batal-bayar')) {
                const rowEl = e.target.closest('tr');

                if (rowEl) {
                    const rowData = DT[`${dtOptions.tableId}`].row(rowEl).data();
                    Object.entries(rowData).forEach(([key, value]) => {
                        let input = document.querySelector(`#form-delete [name="${key.toLowerCase()}"]`);
                        if (input) {
                            input.value = value;
                        }
                    });
                    modalDelete.show();
                }
            }
        });

        document.getElementById('form-delete').addEventListener('submit', function (e) {
            e.preventDefault();
            submitForm('delete');
        })


        function submitForm(form) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let request = null;
            switch (form) {
                case 'delete':
                    loadingAlert('Menghapus tagihan....');
                    const item_id = document.getElementById('delete_id').value;
                    const user_id = document.getElementById('user_delete_id').value;
                    let url = '{{route('admin.keuangan.penerimaan-siswa.data-penerimaan.destroy',':id')}}'
                    url = url.replace(':id', item_id)

                    if(!item_id || !user_id){
                        errorAlert('Tagihan tidak ditemukan!');
                        return;
                    }
                    request = new Request(
                        url, {
                            method: "DELETE",
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            }, body: JSON.stringify({
                                user_id: user_id
                            })
                        });
                    break;
                default:
                    errorAlert('Data tidak valid!');
                    return;
            }

            fetch(request)
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw {status: response.status, message: data.message || response.statusText};
                    }
                    return data;
                })
                .then(data => {
                    dataReload(dtOptions.tableId);
                    successAlert(data.message);
                    modalDelete.hide();
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

        document.addEventListener("DOMContentLoaded", function () {
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

            document.querySelector('.btn-print-rekap').addEventListener('click', function (e) {
                e.preventDefault();
                const form = new FormData(document.getElementById('rekapForm'));
                const params = new URLSearchParams();
                for (const [key, value] of form.entries()) {
                    params.append(key, value);
                }
                const mulaiTanggalValue = params.get('filter[dari_tanggal]');
                const akhirTanggalValue = params.get('filter[sampai_tanggal]');
                const invalidValues = [null, '', 'undefined', 'all'];

                if (invalidValues.includes(mulaiTanggalValue) || invalidValues.includes(akhirTanggalValue)){
                    warningAlert('Silahkan isikan tanggal terlebih dahulu!');
                    return;
                }

                loadingAlert(`Membuat Rekap ... <br> Proses ini membutuhkan waktu beberapa saat<br><hr>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan telah mengisi Tanggal Transaksi! </p>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan browser anda tidak memblokir <i>POP-UP</i>! </p>
                `);

                let url = '{{route('admin.keuangan.penerimaan-siswa.data-penerimaan.cetak-rekap')}}';

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
                        if (!res.ok) {
                            const errorBody = await res.json().catch(() => ({}));
                            throw {
                                status: res.status,
                                message: errorBody.message || 'Terjadi kesalahan',
                                error: errorBody.error,
                                errors: errorBody.errors
                            };
                        }
                        return res.blob();
                    })
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        window.open(url, '_blank');
                        successAlert('Sukses, Rekap terbuka pada tab baru');
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
            });

            if (select2.length) {
                select2.each(function () {
                    let $this = $(this);
                    // select2Focus($this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select value',
                        dropdownParent: $this.parent(),
                    });
                });
            }

            const dariTanggal = $('#filter_dari_tanggal');
            const sampaiTanggal = $('#filter_sampai_tanggal');
            const periodeMulai = $('input[name="filter[periode_mulai]"]');
            const periodeAkhir = $('input[name="filter[periode_akhir]"]');

            dariTanggal.datepicker({
                format: "dd-mm-yyyy",
                autoclose: true
            }).on('changeDate', function (e) {
                sampaiTanggal.datepicker('setStartDate', e.date);
            });

            sampaiTanggal.datepicker({
                format: "dd-mm-yyyy",
                autoclose: true
            }).on('changeDate', function (e) {
                dariTanggal.datepicker('setEndDate', e.date);
            });

            periodeMulai.datepicker({
                format: "yyyymm",
                startView: "months",
                minViewMode: "months",
                autoclose: true
            }).on('changeDate', function (e) {
                periodeAkhir.datepicker('setStartDate', e.date);
            });

            periodeAkhir.datepicker({
                format: "yyyymm",
                startView: "months",
                minViewMode: "months",
                autoclose: true
            }).on('changeDate', function (e) {
                periodeMulai.datepicker('setEndDate', e.date);
            });
        });

        const instansi = {
            nama_instansi: "{{ config('app.nama_instansi') }}",
            nama_sub_1: "{{ config('app.nama_sub_instansi_1') }}",
            nama_sub_2: "{{ config('app.nama_sub_instansi_2') }}",
            akreditasi: "{{ config('app.akreditasi') }}",
            alamat: "{{ config('app.alamat') }}",
            kontak: {
                telepon: "{{ config('app.telepon') }}",
                email: "{{ config('app.email') }}",
                website: "{{ config('app.website') }}"
            }
        };
        const headerLogo = "{{ base64_encode(file_get_contents(public_path(config('app.logo')))) }}";
        const tandaTangan = "{{ Auth::user()->tanda_tangan ? base64_encode(file_get_contents(public_path('storage/photos/tanda_tangan/'.Auth::user()->tanda_tangan))) : '' }}";
        const userName = "{{ Auth::user()->name }}";
        const domisili = "{{ config('app.domisili') }}";
        const tanggalSekarang = "{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}";

        document.getElementById('download-pdf').addEventListener('click', function (e) {
            e.preventDefault();
            const form = new FormData(document.getElementById('rekapForm'));
            const params = new URLSearchParams();
            for (const [key, value] of form.entries()) {
                params.append(key, value);
            }
            const mulaiTanggalValue = params.get('filter[dari_tanggal]');
            const akhirTanggalValue = params.get('filter[sampai_tanggal]');
            const invalidValues = [null, '', 'undefined', 'all'];

            if (invalidValues.includes(mulaiTanggalValue) || invalidValues.includes(akhirTanggalValue)){
                warningAlert('Silahkan isikan tanggal terlebih dahulu!');
                return;
            }

            loadingAlert(`Membuat Rekap ... <br> Proses ini membutuhkan waktu beberapa saat<br><hr>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan telah mengisi Tanggal Transaksi! </p>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan browser anda tidak memblokir <i>POP-UP</i>! </p>
                `);

            let url = '{{route('admin.keuangan.penerimaan-siswa.data-penerimaan.cetak-rekap-new')}}';

            const fullUrl = `${url}?${params.toString()}`;
            const request = new Request(
                fullUrl, {
                    method: "GET",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });

            fetch(request)
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw {status: response.status, message: data.message || response.statusText};
                    }
                    console.log(data)
                    return data;
                })
                .then(data => {
                    generatePdfRekapTagihan(data.data)
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
        });

        function generatePdfRekapTagihan(posts){

            const { jsPDF } = window.jspdf;

            // ✅ Choose orientation dynamically
            const orientation = 'portrait'; // or 'landscape'
            const pageSize = 'a4'; // or 'letter', 'legal', etc.
            const doc = new jsPDF({ orientation, unit: 'mm', format: pageSize });

            // Define initial Y position (adjust as needed)
            let y = 20;

// Add logo (aligned to the left)
            if (headerLogo) {
                doc.addImage('data:image/jpeg;base64,' + headerLogo, 'JPEG', 15, y, 30, 30);
            }

// Calculate center position (adjust based on page width)
            const centerX = doc.internal.pageSize.width / 2;

// Add institution text (centered, aligned with logo)
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(12);
            doc.text(instansi.nama_sub_1 || '', centerX, y + 10, { align: 'center' });
            doc.text(instansi.nama_sub_2 || '', centerX, y + 18, { align: 'center' });
            doc.setFontSize(16);
            doc.text(instansi.nama_instansi, centerX, y + 30, { align: 'center' });

// Add accreditation (if exists)
            if (instansi.akreditasi) {
                doc.setFontSize(12);
                doc.text(instansi.akreditasi, centerX, y + 40, { align: 'center' });
            }

// Update Y position for the next section
            y += 50;

// Add address and contact info (centered)
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.text(instansi.alamat, centerX, y, { align: 'center' });
            y += 5;
            doc.text(
                `Telp: ${instansi.kontak.telepon} | Email: ${instansi.kontak.email} | Web: ${instansi.kontak.website}`,
                centerX, y, { align: 'center' }
            );
            y += 10; // Extra space before content

            // Separator lines
            doc.setDrawColor(0);
            doc.setLineWidth(1.5);
            doc.line(10, y, 200, y);
            y += 2;
            doc.setLineWidth(0.5);
            doc.line(10, y, 200, y);
            y += 10;

            // ---- TITLE ----
            doc.setFontSize(14);
            doc.text('REKAP DATA PENERIMAAN', 105, y, { align: 'center' });
            y += 10;

            // ---- CONTENT ----
            posts.forEach(post => {
                if (post.tagihans.length > 0) {
                    doc.setFontSize(12);
                    doc.text(`${post.kode} - ${post.tagihan}`, 14, y);
                    y += 6;

                    // ✅ Prepare table data with merged logic
                    const rows = [];
                    let currentNIS = null;

                    post.tagihans.forEach((item, index) => {
                        const dateObj = new Date(item.PAIDDT);
                        const formattedDate = dateObj.toLocaleDateString('id-ID', {
                            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                        });
                        const formattedTime = dateObj.toLocaleTimeString('id-ID', {
                            hour: '2-digit', minute: '2-digit'
                        });

                        // Check if NIS is same as previous row
                        let nisCell = item.nocust;
                        let namaCell = item.nmcust;

                        if (currentNIS === item.nocust) {
                            nisCell = ''; // empty for merged effect
                            namaCell = '';
                        } else {
                            currentNIS = item.nocust;
                        }

                        rows.push([
                            nisCell,
                            namaCell,
                            item.BILLNM,
                            item.BTA,
                            `${formattedDate} pukul ${formattedTime}`,
                            formatRupiah(item.BILLAM)
                        ]);
                    });

                    doc.autoTable({
                        startY: y,
                        head: [['NIS', 'Nama', 'Nama Tagihan', 'Tahun Akademik', 'Tanggal Bayar', 'Tagihan']],
                        body: rows,
                        theme: 'grid',
                        styles: { fontSize: 8, cellPadding: 3 },
                        headStyles: { fillColor: [237, 237, 237], textColor: 0 },
                        columnStyles: {
                            0: { cellWidth: 20 },
                            1: { cellWidth: 30 },
                            2: { cellWidth: 40 },
                            3: { cellWidth: 25 },
                            4: { cellWidth: 45 },
                            5: { cellWidth: 25, halign: 'right' }
                        }
                    });

                    y = doc.lastAutoTable.finalY + 10;
                }
            });

            // ---- FOOTER ----
            if (y + 40 > doc.internal.pageSize.getHeight() - 20) doc.addPage();
            y = doc.internal.pageSize.getHeight() - 60;

            doc.setFontSize(10);
            doc.text(`${domisili}, ${tanggalSekarang}`, 140, y);
            y += 5;

            if (tandaTangan) {
                doc.addImage('data:image/png;base64,' + tandaTangan, 'PNG', 140, y, 40, 20);
                y += 25;
            } else {
                y += 25;
            }

            doc.text(userName, 150, y);

            doc.save('rekap-penerimaan.pdf');

            function formatRupiah(amount) {
                if (!amount) return 'Rp 0';
                return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        }
    </script>

    {!! ($modalLink??'') !!}
@endsection
