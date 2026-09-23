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
                <button type="button" class="btn btn-whatsapp" data-bs-toggle="modal"
                        data-bs-target="#modal-import" title="Buat Data">
                    <span class="ri-file-excel-2-line me-2"></span>
                    Import Data Siswa
                </button>
            </div>
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
                            <button type="button" class="btn btn-danger" id="btn-clear-data">
                                <span class="ri-delete-bin-2-line me-2"></span>
                                Bersihkan Data
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
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>
    <script src="{{asset('main/libs/select2/select2.min.js')}}"></script>

    <form id="formImport" enctype="multipart/form-data" class="mainForm"
          method="POST">
        <div class="modal modal-blur fade" id="modal-import" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                title="tutup"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-timeline mb-3">
                            <li class="list-group-item list-group-timeline-danger">File harus berformat <span class="fw-bold">XLS/XLSX</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Ukuran file tidak boleh lebih dari <span class="fw-bold">1024KB/1MB</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Kolom identitas: isi <span class="fw-bold">NIS</span> atau <span class="fw-bold">NODAFTAR</span> (salah satu). Kolom lain wajib: <span class="fw-bold">Nama, Unit, Kelas, Kelompok, Angkatan</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Konversi NIS → nomor daftar: kosongkan NIS, isi NODAFTAR, lalu simpan dengan metode <span class="fw-bold">SIMPAN / konversi ke Nomor Pendaftaran</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Kolom opsional: <span class="fw-bold">Gender, Alamat, Ortu, Nominal</span> (bukan Ayah/Ibu). Kolom Nominal diabaikan di sini, dipakai jika file yang sama diimport di Buat Tagihan Excel.</li>
                            <li class="list-group-item list-group-timeline-danger">Contoh file yang dapat diproses untuk import:
                                <a class="btn btn-sm btn-outline-primary fw-bolder"
                                   href="{{asset('TEMPLATE MENU UPLOAD DATA SISWA.xlsx')}}?v=20260923-syukro"
                                   download>
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
                        <fieldset class="form-fieldset">
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label" for="metode">Metode Penyimpanan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="metode" name="metode" required>
                                        <option value="1">SIMPAN data siswa dengan NIS</option>
                                        <option value="2">SIMPAN / konversi ke Nomor Pendaftaran</option>
                                        <option value="3">UPDATE Kelas Siswa</option>
                                        <option value="4">UPGRADE Nomor Pendaftaran ke NIS</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
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

    <link rel="stylesheet" href="{{asset('libs/filepond/dist/filepond.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/filepond/dist/custom.css')}}">
    <script
        src="{{asset('libs/filepond/plugin/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js')}}"></script>
    <script
        src="{{asset('libs/filepond/plugin/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js')}}"></script>
    <script src="{{asset('libs/filepond/dist/filepond.min.js')}}"></script>
    <script src="{{asset('libs/filepond/dist/filepond.jquery.js')}}"></script>

    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        let filePondElements = [];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let dtOptions = {
            tableId: 'main_table',
            formId: 'filterForm',
            columnUrl: '{{($columnsUrl??null)}}?v=20260604',
            dataUrl: '{{($datasUrl??null)}}?v=20260604',
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

        function initializeFilePond(id) {
            let inputElement = document.querySelector('input#' + id);
            filePondElements[id] = FilePond.create(inputElement, {
                credits: null,
                allowFileEncode: false,
                acceptedFileTypes: [
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/wps-office.xlsx',
                    'application/wps-office.xls'
                ],
                // fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                //     console.log(source, type);
                //     resolve(type);
                // }),
                required: false,
                storeAsFile: true,
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
            FilePond.registerPlugin(
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize,
            )

            initializeFilePond('file');

            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
                if (dtOptions.formId) {
                    let filterForm = $(`#${dtOptions.formId}`);
                    filterForm.on('submit', function (e) {
                        e.preventDefault();
                        dataReload(dtOptions.tableId);
                    });
                    filterForm.on('reset', function (e) {
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
            if (select2.length) {
                select2.each(function () {
                    let $this = $(this);
                    // select2Focus($this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        language: 'id',
                        dropdownParent: $this.parent()
                    });
                });
            }

            document.querySelectorAll(".mainForm").forEach(form => {
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    let url = "";
                    let method = "";
                    const formId = this.id;
                    let formData = new FormData(this);

                    if (formId === "formImport") {
                        loadingAlert('Meng-unggah data siswa');
                        url = '{{route('admin.master-data.export-import-data.store')}}';
                        method = 'POST';
                    }else if (formId === "formValidate"){
                        loadingAlert('Menyimpan data siswa');
                        url = '{{route('admin.master-data.export-import-data.validate-data')}}';
                        method = 'POST';
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
                            if (typeof Swal !== 'undefined') {
                                Swal.close();
                            }
                            document.getElementById(formId).reset();
                            if (formId === 'formImport' && resetFilePond) {
                                resetFilePond('file');
                            }
                            successAlert(data.message);
                            dataReload("main_table");
                            document.querySelector(`#${formId} [data-bs-dismiss="modal"]`)?.click();
                        })
                        .catch(error => {
                            if (typeof Swal !== 'undefined') {
                                Swal.close();
                            }
                            if (error.status === 422) {
                                const errors = error.error.error || error.error.errors;
                                errorAlert(error.error.message);
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
            });

            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function (e) {
                    const form = modal.closest("form");
                    if (!form) return;
                    form.reset();

                    if(form.id === "formImport"){
                        resetFilePond('file')
                    }

                    clearErrorMessages(form.id);
                    setTimeout(() => {
                        modal.querySelectorAll("[data-control='select2']").forEach(select => {
                            $(select).trigger("change");
                        });
                    }, 0);
                });
            });

            document.getElementById('btn-clear-data').addEventListener('click', function (e) {
                e.preventDefault();
                loadingAlert(`Membersihkan data yang sudah diimport ......`);

                let url = '{{route('admin.master-data.export-import-data.clear-data')}}';
                const request = new Request(
                    url, {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/pdf'
                        }
                    });

                fetch(request)
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
        });

    </script>
@endsection
