@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.min.css')}}">
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
        <div class="card-header header-elements">
            <div class="card-title">
                <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
            </div>
            <div class="card-header-elements ms-auto">
                <div class="d-flex justify-content-center justify-content-md-end gap-4">
                    <div class="w-100">
                        <div class="row">
                            <div class="d-flex justify-content-center justify-content-md-end gap-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modal-create" title="Buat Data">
                                    <span class="ri-add-line me-2"></span>
                                    Buat Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="filterForm">
                <fieldset class="form-fieldset">
                    <h5>Filter</h5>
                    <div class="row row-cols-lg-2 row-cols-1">
                        <div class="col mb-5">
                            <label class="form-label" for="filter[tahun_akademik]">Tahun Angkatan</label>
                            <select class="form-select" id="filter[tahun_akademik]" name="filter[tahun_akademik]"
                                    data-control="select2" data-placeholder="Pilih Tahun Akademik">
                                <option value="all" {{ request('filter.tahun_akademik') == 'all' ? 'selected' : '' }}>Semua</option>
                                @isset($thn_aka)
                                    @foreach($thn_aka as $item)
                                        <option value="{{$item->thn_aka}}"
                                            {{ request('filter.tahun_akademik') == $item->urut ? 'selected' : '' }}>
                                            {{$item->thn_aka}}
                                        </option>
                                    @endforeach
                                @else
                                    <option>data kosong</option>
                                @endisset
                            </select>
                        </div>

                        <div class="col mb-5">
                            <label class="form-label" for="filter[kelas]">Kelas</label>
                            <select class="form-select" name="filter[kelas]" id="filter[kelas]" data-control='select2'>
                                @isset($kelas)
                                    <option value="all">Semua</option>
                                    @foreach($kelas as $item)
                                        <option
                                            value="{{$item->id}}">{{$item->unit}} - {{$item->jenjang}} {{$item->kelas}}</option>
                                    @endforeach
                                @else
                                    <option>data kosong</option>
                                @endisset
                            </select>
                        </div>

                        <div class="col mb-5">
                            <label class="form-label" for="filter[kode_akun]">Kode Akun</label>
                            <select class="form-select" id="filter[kode_akun]" name="filter[kode_akun]" data-control="select2" data-placeholder="Pilih Kode Akun">
                                <option value="all" {{ request('filter.kode_akun') == 'all' ? 'selected' : '' }}>Semua</option>
                                @isset($tagihan)
                                    @foreach($tagihan as $item)
                                        <option value="{{$item->KodeAkun}}"
                                            {{ request('filter.kode_akun') == $item->KodeAkun ? 'selected' : '' }}>
                                            {{$item->KodeAkun}} - {{$item->NamaAkun}}
                                        </option>
                                    @endforeach
                                @else
                                    <option>data kosong</option>
                                @endisset
                            </select>
                        </div>

                        <div class="col mb-5">
                            <label class="form-label" for="filter[nominal]">Nominal</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" id="filter[nominal]" name="filter[nominal]"
                                       placeholder="Nominal"
                                       class="form-control formattedNumber"/>
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
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>
    <script src="{{asset('js/helper/errorInputHelper.min.js')}}"></script>
    <script src="{{asset('js/helper/formattedNumber.min.js')}}"></script>
    <script src="{{asset('main/libs/select2/select2.min.js')}}"></script>

    <script type="text/javascript">
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
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            info: false,
            scrollX: false,
            serverSide: true,
            select: false,
            scrollY: false,
        };

        function updateFilterWindowLocation(form){
            let baseUrl = window.location.origin + window.location.pathname;
            let queryParams = $.param($(`#${form}`).serializeArray().reduce(function (acc, curr) {
                if (curr.value !== '') {
                    acc[curr.name] = curr.value;
                }
                return acc;
            }, {}));
            let newUrl = baseUrl + '?' + queryParams;
            window.history.pushState(null, '', newUrl);
        }


        document.addEventListener("DOMContentLoaded", function () {
            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
                if (dtOptions.formId) {
                    let filterForm = document.getElementById(`${dtOptions.formId}`);
                    filterForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        dataReload(dtOptions.tableId);
                    });
                    filterForm.addEventListener('reset', function (e) {
                        setTimeout(function () {
                            dataReload(dtOptions.tableId);
                            const select2InForm = select2.filter(`#${dtOptions.formId} [data-control='select2']`);
                            if (select2InForm.length) {
                                select2InForm.each(function () {
                                    let $this = $(this);
                                    $this.trigger('change');
                                });
                            }
                            updateFilterWindowLocation(dtOptions.formId);
                            dataReFilter(dtOptions.tableId);
                        }, 0)
                    });
                }
            }

            document.querySelectorAll("[data-control='select2']").forEach(select => {
                let wrapper = document.createElement("div");
                wrapper.classList.add("position-relative");
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);
                $(select).select2({
                    placeholder: "Pilih satu",
                    language: "id",
                    dropdownParent: $(wrapper)
                });
            });
        });

    </script>

    <form id="addForm" class="mainForm">
        <div class="modal modal-blur fade" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Tambah Data Beban Post
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <fieldset class="form-fieldset">
                            <div class="mb-3">
                                <label class="form-label required" for="tahun_aka">Tahun Angkatan</label>
                                <select class="form-select" name="tahun_aka" id="tahun_aka" data-control='select2'>
                                    @isset($thn_aka)
                                        @foreach($thn_aka as $item)
                                            <option value="{{$item->thn_aka}}">
                                                {{$item->thn_aka}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>data kosong</option>
                                    @endisset
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="kelas">Kelas</label>
                                <select class="form-select" name="kelas" id="kelas" data-control='select2'>
                                    @isset($kelas)
                                        @foreach($kelas as $item)
                                            <option
                                                value="{{$item->id}}">{{$item->unit}} - {{$item->jenjang}} {{$item->kelas}}</option>
                                        @endforeach
                                    @else
                                        <option>data kosong</option>
                                    @endisset
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="kode_akun">Kode Akun</label>
                                <select class="form-select" name="kode_akun" id="kode_akun" data-control='select2' required>
                                    @isset($tagihan)
                                        @foreach($tagihan as $item)
                                            <option value="{{$item->KodeAkun}}">
                                                {{$item->KodeAkun}} - {{$item->NamaAkun}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option>data kosong</option>
                                    @endisset
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="nominal">Nominal</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">Rp. </span>
                                    <input type="text" id="nominal" name="nominal"
                                           placeholder="Nominal" required
                                           class="form-control formattedNumber"/>
                                </div>
                                <div class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <input type="reset" value="Batal" class="btn btn-outline-secondary w-100"
                                           data-bs-dismiss="modal">
                                </div>
                                <div class="col">
                                    <input type="submit" value="Simpan Data" class="btn btn-primary w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll(".mainForm").forEach(form => {
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    loadingAlert();
                    let url = "";
                    let method = "";
                    const formId = this.id;
                    let formData = new FormData(this);

                    if (formId === "addForm") {
                        url = "{{route('admin.master-data.beban-post.store')}}";
                        method = "POST";
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    formData.append("_token", csrfToken);

                    let fetchOptions = {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    };

                    // if (formId === "formImport") {
                    //     fetchOptions.headers["Content-Type"] = "multipart/form-data";
                    // }

                    clearErrorMessages(formId);
                    fetch(url, fetchOptions)
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw {status: response.status, error: err};
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            successAlert(data.message);
                            dataReload("main_table");
                            if (formId === "addForm") {
                                clearErrorMessages(formId);
                                const nominalEl = document.getElementById("nominal");
                                if (nominalEl) {
                                    nominalEl.value = "";
                                    nominalEl.dispatchEvent(new Event("input", {bubbles: true}));
                                    nominalEl.focus();
                                }
                            } else {
                                document.getElementById(formId).reset();
                                document.querySelector(`#${formId} [data-bs-dismiss="modal"]`)?.click();
                            }
                        })
                        .catch(error => {
                            if (error.status === 422) {
                                const errors = error.error.error || error.error.errors;
                                errorAlert(error.error.message);
                                if (errors) {
                                    processErrors(errors)
                                }
                            } else {
                                const errorMessages = {
                                    419: '',
                                    500: "Tidak dapat terhubung ke server, Silahkan periksa koneksi internet anda",
                                    403: "Anda tidak memiliki izin untuk mengakses halaman ini",
                                    404: "Halaman tidak ditemukan"
                                };
                                errorAlert(errorMessages[error.status] || "Terjadi kesalahan, silahkan coba memuat ulang halaman");
                            }
                        });
                });
            });

            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function (e) {
                    const form = modal.closest("form");
                    if (!form) return;
                    form.reset();
                    clearErrorMessages(form.id);
                    setTimeout(() => {
                        modal.querySelectorAll("[data-control='select2']").forEach(select => {
                            $(select).trigger("change");
                        });
                    }, 0);
                });
            });
            // $('#modal-create').on('show.bs.modal', function (e) {
            //     const formId = this.id
            //     $(this).parent().trigger('reset')
            //     // initTomSelect('tipe')
            //     clearErrorMessages(formId)
            // })
        });
    </script>
@endsection
