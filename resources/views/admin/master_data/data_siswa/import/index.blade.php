@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
@endsection
@section('content')
    <div class="row row-cols-1 row-cols-lg-2 pb-3">
        <div class="col">
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
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="d-flex justify-content-end">
                <a href="{{route('admin.master-data.data-siswa.index')}}" class="btn btn-outline-primary">
                    <span class="ri-arrow-left-s-line me-2"></span>
                    Kembali ke data siswa
                </a>
            </div>
        </div>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}" xmlns="http://www.w3.org/1999/html">

    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
            <div class="card-header-elements ms-auto">
                <div class="row">
                    <div class="w-100">
                        <div class="row">
                            <div class="d-flex justify-content-center justify-content-md-end gap-4">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modal-import" title="Buat Data">
                                    <span class="ri-file-excel-2-line me-2"></span>
                                    Import Siswa
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
                            <label class="form-label" for="filter[tahun_akademik]">
                                Angkatan
                            </label>
                            <select class="form-select" id="filter[tahun_akademik]"
                                    name="filter[tahun_akademik]"
                                    data-control="select2"
                                    data-placeholder="Pilih Tahun Akademik">
                                <option value="all">Semua</option>
                                @isset($thn_aka)
                                    @foreach($thn_aka as $item)
                                        <option
                                            value="{{$item->id}}">{{$item->thn_aka}}</option>
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
                                            value="{{$item->id}}">{{$item->unit}}  -  {{$item->kelas}} {{$item->kelompok}}</option>
                                    @endforeach
                                @else
                                    <option>data kosong</option>
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-100">
                            <div class="row">
                                <div class="d-flex justify-content-center justify-content-md-end gap-4">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <span class="ri-reset-left-line me-2"></span>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-outline-primary ">
                                        <span class="ri-search-line me-2"></span>
                                        Cari
                                    </button>
                                </div>
                            </div>
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
        <div class="card-footer">
            <div class="w-100">
                <div class="row">
                    <div class="col-auto ms-auto d-print-none">
                        <div class="d-flex justify-content-end gap-4">
                            <button class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#modal-delete">
                                <span class="ri-delete-bin-2-line me-2"></span>
                                Hapus Data
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modal-validate">
                                <span class="ri-save-line me-2"></span>
                                Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <form id="deleteForm" class="mainForm">
        <div class="modal modal-blur fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-header ">
                        <div class="modal-title">
                            Hapus Data
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="row mb-3 text-center">
                            <span class="ri-delete-bin-2-line ri-48px"></span>
                            <h3>Hapus Seluruh Import Data Siswa?</h3>
                            <div class="">
                                Anda yakin ingin menghapus seluruh data siswa yang telah diimport?
                            </div>
                        </div>
                        <input type="hidden" id="delete_id" name="delete_id" value="12">
                    </div>
                    <div class="modal-footer ">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <input type="reset" class="btn btn-outline-secondary w-100" value="Batal"
                                           data-bs-dismiss="modal">
                                </div>
                                <div class="col">
                                    <input type="submit" value="Hapus Data" class="btn btn-danger w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <form id="formValidate" class="mainForm" method="POST">
        <div class="modal modal-blur fade" id="modal-validate" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-header ">
                        <div class="modal-title">
                            Simpan Data Siswa
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="row mb-3 text-center">
                            <span class="ri-save-line ri-48px"></span>
                            <h3>Simpan Data Siswa?</h3>
                            <div class="">
                                Anda yakin ingin menyimpan data siswa yang telah diimport?
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label" for="metode">Metode Penyimpanan</label>
                                <select class="form-select" id="metode" name="metode" required>
                                    <option value="1">Simpan data siswa baru</option>
                                    <option value="2">Update data siswa dengan nis duplikat</option>
                                    <option value="3"> Simpan data siswa baru & Update data siswa dengan nis duplikat
                                    </option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="delete_id" name="delete_id" value="12">
                    </div>
                    <div class="modal-footer ">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <input type="reset" class="btn btn-outline-secondary w-100" value="Batal"
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

    <form id="formImport" enctype="multipart/form-data" class="mainForm"
          method="POST">
        <div class="modal modal-blur fade" id="modal-import" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Master Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                title="tutup"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-timeline mb-3">
                            <li class="list-group-item list-group-timeline-danger">File harus berformat <span class="fw-bold">XLS/XLSX</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Ukuran file tidak boleh lebih dari <span class="fw-bold">1024/1MB</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Kolom yang harus terisi: <span class="fw-bold">NIS, NAMA, KELAS, KELOMPOK, UNIT, ANGKATAN</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Jika NIS sudah ada pada data import, data tersebut akan diupdate</li>
                            <li class="list-group-item list-group-timeline-danger">Kolom opsional: <span class="fw-bold">Gender, Alamat, Ortu, Nominal</span>. Nominal diabaikan di sini; dipakai jika file yang sama diimport di Buat Tagihan Excel.</li>
                            <li class="list-group-item list-group-timeline-danger">Contoh file yang dapat diproses untuk import:
                                <a class="btn btn-sm btn-label-primary" href="{{ asset('TEMPLATE MENU UPLOAD DATA SISWA.xlsx') }}?v=20260923-syukro">
                                    <i class="ri ri-file-excel-line me-2"></i>Contoh File
                                </a>
                            </li>
                        </ul>
                        <fieldset class="form-fieldset">
                            <div class="mb-3">
                                <label class="form-label required" for="file">File (.XLS, .XLSX)</label>
                                <input type="file" id="file" class="form-control"
                                       name="fileImport"
                                       placeholder="file" required>
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
                                    <input type="submit" value="Import Data" class="btn btn-primary w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="{{asset('main/libs/select2/select2.js')}}"></script>


    <link rel="stylesheet" href="{{asset('libs/filepond/dist/filepond.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/filepond/dist/custom.css')}}">
    <script
        src="{{asset('libs/filepond/plugin/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js')}}"></script>
    <script
        src="{{asset('libs/filepond/plugin/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js')}}"></script>
    <script src="{{asset('libs/filepond/dist/filepond.min.js')}}"></script>
    <script src="{{asset('libs/filepond/dist/filepond.jquery.js')}}"></script>

    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>

    <script type="text/javascript">
        let dataColumns = [];
        let dataTableInit;
        let dataUrl = '{{($datasUrl??null)}}';
        let columnUrl = '{{($columnsUrl??null)}}';
        let formId = 'filterForm';
        let tableId = 'main_table';
        let id_action = '';
        let filePondElements = [];
        let select2 = $(`[data-control='select2']`);

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
        };


        function initializeFilePond(id) {
            let inputElement = document.querySelector('input#' + id);
            filePondElements[id] = FilePond.create(inputElement, {
                credits: null,
                allowFileEncode: false,
                required: false,
                storeAsFile: true,
                acceptedFileTypes: [
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/wps-office.xlsx',
                    'application/wps-office.xls'
                ],
                labelIdle: 'Klik untuk membuka file manager, atau seret file ke dalam box ini.',
                allowFileTypeValidation: true,
                allowFileSizeValidation: true,
                labelMaxFileSizeExceeded: 'File terlalu besar',
                labelMaxFileSize: 'Ukuran maksimal file: {filesize}',
                labelFileTypeNotAllowed: 'Format file salah!',
                fileValidateTypeLabelExpectedTypes: 'file harus berformat .xls atau .xlsx',
                maxFileSize: 1024000,
            });
        }

        function resetFilePond(id) {
            filePondElements[id].removeFiles();
        }

        function clearErrorMessages(formId) {
            const form = document.querySelector(`#${formId}`);
            const errorElements = form.querySelectorAll('.invalid-feedback');
            const errorClass = form.querySelectorAll('.is-invalid');

            errorElements.forEach(element => element.textContent = '');
            errorClass.forEach(element => element.classList.remove('is-invalid'));
        }

        function processErros(errors) {
            for (const [key, value] of Object.entries(errors)) {
                const field = $(`[name=${key}]`);
                const errorMessage = value[0];

                function applyInvalidClasses(element, container) {
                    element.addClass('is-invalid');
                    container.addClass('is-invalid');
                    let errorFeedback = container.siblings('.invalid-feedback');

                    if (errorFeedback.length === 0) {
                        $('<div>', {
                            class: 'invalid-feedback',
                            role: 'alert',
                            text: errorMessage
                        }).insertAfter(container);
                    } else {
                        errorFeedback.html(errorMessage);
                    }
                }

                if (field.hasClass('select2-hidden-accessible')) {
                    let nextField = field.siblings('.select2-container');
                    applyInvalidClasses(field, nextField);
                } else {
                    if (field.parent().hasClass('input-group')) {
                        applyInvalidClasses(field, field.parent());
                    } else {
                        applyInvalidClasses(field, field);
                    }
                }

                if (key === 'password') {
                    const confirmField = $(`[name=${key}_confirmation]`);
                    applyInvalidClasses(confirmField, confirmField);
                }
            }
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

            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
            )

            $('.mainForm').on('submit', function (e) {
                e.preventDefault()
                let url
                let tipe
                const formId = $(this).attr('id');
                let data = $(this).serialize();

                if (formId === "formImport") {
                    loadingAlert('Meng-Import data siswa');
                    url = '{{route('admin.master-data.data-siswa.import.store')}}'
                    tipe = 'POST';
                    data = new FormData(this);
                } else if (formId === 'formValidate') {
                    loadingAlert('Menyimpan data siswa');
                    url = '{{route('admin.master-data.data-siswa.import.validate-import')}}'
                    tipe = 'POST';
                } else if (formId === 'deleteForm') {
                    loadingAlert('Menghapus data siswa');
                    url = '{{route('admin.master-data.data-siswa.import.destroy-all')}}'
                    tipe = 'POST';
                }

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                let ajaxOptions = {
                    url: url,
                    type: tipe,
                    data: data,
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }

                if (formId === "formImport") {
                    ajaxOptions.contentType = false;
                    ajaxOptions.processData = false;
                }

                // console.log(ajaxOptions)
                clearErrorMessages(formId)
                $.ajax(ajaxOptions).done(function (responses) {
                    document.getElementById(formId).reset();
                    successAlert(responses.message);
                    dataReload('main_table');
                    $("#" + $(e.target).attr('id')).find('[data-bs-dismiss="modal"]').trigger('click')
                }).fail(function (xhr) {
                    if (xhr.status === 422) {
                        const response = JSON.parse(xhr.responseText);
                        const error = response.error;
                        const errors = response.errors;
                        const errMessage = response.message || xhr.responseJSON.message;
                        errorAlert(errMessage);
                        if (error) {
                            processErros(error);
                        } else if (errors) {
                            processErros(errors);
                        }
                    } else {
                        const errMessages = {
                            401: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
                            403: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
                            404: 'Halaman yang dituju tidak ditemukan 🧐',
                            405: 'Metode tidak valid 🧐 <br>silahkan muat ulang halaman dan coba lagi!',
                            419: '',
                            429: 'Terlalu banyak permintaan akses <br>silahkan tunggu beberapa saat 🙏',
                            '5xx': 'Terjadi kesalahan saat memproses permintaan 😵‍💫. <br> silahkan muat ulang halaman'
                        };
                        const errMessage =
                            errMessages[xhr.status] ||
                            (xhr.status >= 500 && xhr.status <= 504 ? errMessages['5xx'] :
                                'Tidak dapat terhubung ke server <br> Silahkan coba muat ulang halaman atau periksa koneksi internet anda.');
                        errorAlert(errMessage);
                    }
                })
            })

            $('#modal-import').on('hide.bs.modal', function (e) {
                resetFilePond('file')
            })

            if (select2.length) {
                select2.each(function () {
                    let $this = $(this);
                    // select2Focus($this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }

            initializeFilePond('file');

        });

    </script>

    {!! ($modalLink??'') !!}
@endsection
