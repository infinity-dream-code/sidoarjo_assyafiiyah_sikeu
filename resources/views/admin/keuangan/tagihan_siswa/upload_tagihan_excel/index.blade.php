@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.min.css')}}">
    <style>
        .upload-tagihan-toolbar {
            background: #f8f9fb;
        }

        .upload-tagihan-toolbar .form-label {
            margin-bottom: 0.4rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #566a7f;
        }

        .upload-tagihan-toolbar .select2-container {
            width: 100% !important;
        }

        .upload-tagihan-toolbar .form-select,
        .upload-tagihan-toolbar .select2-container .select2-selection {
            min-height: 38px;
        }

        .upload-tagihan-bta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.65rem;
            padding: 0.35rem 0.65rem;
            border-radius: 0.375rem;
            background: #fff;
            border: 1px dashed #d9dee3;
            font-size: 0.8125rem;
            color: #697a8d;
        }

        .upload-tagihan-bta .badge {
            font-size: 0.8125rem;
            letter-spacing: 0.04em;
        }

        #main_table_wrapper .dataTables_length,
        #main_table_wrapper .dataTables_filter {
            padding: 0.75rem 1.25rem 0;
        }

        #main_table_wrapper .dataTables_info,
        #main_table_wrapper .dataTables_paginate {
            padding: 0.75rem 1.25rem 1rem;
        }
    </style>
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

    @php
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    @endphp

    <div class="card">
        <div class="card-header header-elements">
            <div class="card-title">
                <h5 class="mb-0">{{ $dataTitle ?? $mainTitle }}</h5>
            </div>
            <div class="card-header-elements ms-auto d-flex gap-2">
                <button type="button" class="btn btn-outline-danger js-clear-import" title="Clear data import">
                    <span class="ri-delete-bin-line me-2"></span>
                    Clear
                </button>
                <button type="button" class="btn btn-whatsapp" data-bs-toggle="modal"
                        data-bs-target="#modal-import" title="Import Excel">
                    <span class="ri-file-excel-2-line me-2"></span>
                    Import Excel
                </button>
            </div>
        </div>

        <div class="card-body border-bottom upload-tagihan-toolbar py-4">
            <form id="filterForm">
                <div class="row g-4">
                    <div class="col-md-6 col-xl-5">
                        <label class="required form-label" for="tagihan">Jenis Tagihan</label>
                        <select class="form-select" id="tagihan" name="tagihan" required
                                data-control="select2" data-placeholder="Pilih jenis tagihan">
                            @isset($tagihan)
                                @foreach($tagihan as $item)
                                    <option value="{{ $item->urut }}">{{ $item->tagihan }}</option>
                                @endforeach
                            @else
                                <option value="">Data kosong</option>
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-6 col-xl-7">
                        <label class="required form-label" for="periode_tahun">Periode Tagihan</label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <select class="form-select" id="periode_tahun" name="periode_tahun"
                                        required data-control="select2" data-placeholder="Pilih tahun">
                                    @foreach(($periode_tahun_list ?? []) as $tahun)
                                        <option value="{{ $tahun }}"
                                            @selected(($periode_tahun_default ?? date('Y')) == $tahun)>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select class="form-select" id="periode_bulan" name="periode_bulan"
                                        data-control="select2" data-placeholder="Pilih bulan" data-allow-clear="true">
                                    <option value="">Tanpa bulan</option>
                                    @foreach($bulanList as $bulan => $label)
                                        <option value="{{ $bulan }}"
                                            @selected(($periode_bulan_default ?? date('n')) == $bulan)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="upload-tagihan-bta">
                            <i class="ri-calendar-line"></i>
                            <span>Kode BTA</span>
                            <span class="badge bg-primary" id="periode_preview">-</span>
                        </div>
                        <div class="form-text">Kosongkan bulan untuk tagihan tanpa periode bulanan (PSB). Kode BTA jadi tahun saja, misalnya <strong>2026</strong>.</div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive text-nowrap">
            <table class="table table-sm table-bordered table-hover mb-0" id="main_table">
                <thead class="table-light"></thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2 border-top">
            <button type="button" class="btn btn-outline-danger js-clear-import">
                <span class="ri-delete-bin-line me-2"></span>
                Clear
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-validate">
                <span class="ri-save-line me-2"></span>
                Simpan Data
            </button>
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
                        <h5 class="modal-title">Import Data Tagihan Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                title="tutup"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-timeline mb-3">
                            <li class="list-group-item list-group-timeline-danger">File harus berformat <span class="fw-bold">XLS/XLSX</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Ukuran file tidak boleh lebih dari <span class="fw-bold">1024KB/1MB</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Kolom wajib: <span class="fw-bold">NIS (atau NIK) dan Nominal</span>.</li>
                            <li class="list-group-item list-group-timeline-danger">Kolom lain (<span class="fw-bold">Nama, Unit, Kelas, Kelompok, Angkatan, Gender, Alamat, Ortu</span>) opsional — boleh kosong.</li>
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
                                <label class="form-label text-capitalize required" for="file">File (.XLS, .XLSX)</label>
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
                    <div class="modal-status bg-primary"></div>
                    <div class="modal-header">
                        <div class="modal-title">
                            Simpan Data Tagihan
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="row mb-3 text-center">
                            <span class="ri-save-line ri-48px"></span>
                            <h3>Simpan Data Tagihan Siswa?</h3>
                            <div class="">
                                Anda yakin ingin menyimpan data tagihan yang telah diimport?
                            </div>
                        </div>
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
    <script src="{{asset('js/helper/errorInputHelper.min.js')}}"></script>

    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        let filePondElements = [];

        let dtOptions = {
            tableId: 'main_table',
            formId: 'filterForm',
            columnUrl: '{{($columnsUrl??null)}}',
            dataUrl: '{{($datasUrl??null)}}',
            dataColumns: [],
            thead: true,
            tfoot: false,
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

        async function parseJsonResponse(response) {
            const contentType = response.headers.get('content-type') || '';
            const raw = await response.text();

            if (contentType.includes('application/json')) {
                try {
                    return JSON.parse(raw);
                } catch (error) {
                    throw {status: response.status, message: 'Respons server tidak valid (JSON rusak).'};
                }
            }

            throw {
                status: response.status,
                message: raw?.trim()
                    ? 'Server mengembalikan respons non-JSON. Periksa log Laravel.'
                    : 'Server tidak mengembalikan data.',
            };
        }

        function appendImportFileToFormData(formData) {
            const pond = filePondElements['file'];
            if (!pond) {
                return formData;
            }

            const pondFile = pond.getFiles()[0]?.file;
            if (pondFile) {
                formData.delete('fileImport');
                formData.append('fileImport', pondFile, pondFile.name);
            }

            return formData;
        }

        function syncPeriodePreview() {
            const tahun = $('#periode_tahun').val();
            const bulanVal = $('#periode_bulan').val();
            if (tahun && bulanVal) {
                $('#periode_preview').text(`${tahun}${String(bulanVal).padStart(2, '0')}`);
            } else if (tahun) {
                $('#periode_preview').text(tahun);
            } else {
                $('#periode_preview').text('-');
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
                        placeholder: $this.data('placeholder') || 'Pilih',
                        language: 'id',
                        dropdownParent: $this.parent(),
                        width: '100%',
                        allowClear: $this.data('allow-clear') === true,
                    });
                });
            }

            document.querySelectorAll(".mainForm").forEach(form => {
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    loadingAlert();
                    let url = "";
                    let method = "";
                    const formId = this.id;
                    let formData = new FormData(this);

                    if (formId === "formImport") {
                        loadingAlert('Mengunggah data tagihan');
                        url = '{{route('admin.keuangan.tagihan-siswa.upload-tagihan-excel.store')}}';
                        method = 'POST';
                        formData = appendImportFileToFormData(formData);
                    } else if (formId === "formValidate") {
                        let form = document.getElementById('filterForm');
                        formData = new FormData(form);
                        loadingAlert('Menyimpan data tagihan');
                        url = '{{route('admin.keuangan.tagihan-siswa.upload-tagihan-excel.validate-excel')}}';
                        method = 'POST';
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    formData.append("_token", csrfToken);

                    let fetchOptions = {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    };

                    clearErrorMessages(formId);
                    fetch(url, fetchOptions)
                        .then(async response => {
                            const payload = await parseJsonResponse(response);
                            if (!response.ok) {
                                throw {status: response.status, payload};
                            }
                            return payload;
                        })
                        .then(data => {
                            document.getElementById(formId).reset();
                            successAlert(data.message);
                            dataReload("main_table");
                            document.querySelector(`#${formId} [data-bs-dismiss="modal"]`)?.click();
                        })
                        .catch(error => {
                            const payload = error.payload || {};
                            const errors = payload.error || payload.errors;
                            const message = payload.message || error.message;

                            if (error.status === 422) {
                                errorAlert(message || 'Data tidak valid.');
                                if (errors) {
                                    processErrors(errors);
                                }
                                return;
                            }

                            const errorMessages = {
                                401: 'Sesi anda sudah habis 🙏 <br>Silahkan muat ulang halaman untuk melanjutkan! <br> jika masalah masih terjadi silahkan login kembali!',
                                403: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
                                404: 'Halaman yang dituju tidak ditemukan 🧐',
                                405: 'Metode tidak valid 🧐 <br>silahkan muat ulang halaman dan coba lagi!',
                                419: 'Sesi anda sudah habis 🙏 <br>Silahkan muat ulang halaman untuk melanjutkan! <br> jika masalah masih terjadi silahkan login kembali!',
                                429: 'Terlalu banyak permintaan akses <br>silahkan tunggu beberapa saat 🙏',
                            };

                            errorAlert(
                                message ||
                                errorMessages[error.status] ||
                                'Terjadi kesalahan, silahkan coba memuat ulang halaman'
                            );
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

            $('#periode_tahun, #periode_bulan').on('change', syncPeriodePreview);
            syncPeriodePreview();

            document.querySelectorAll('.js-clear-import').forEach(function (button) {
                button.addEventListener('click', function () {
                    loadingAlert('Membersihkan data import');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const formData = new FormData();
                    formData.append('_token', csrfToken);

                    fetch('{{ route('admin.keuangan.tagihan-siswa.upload-tagihan-excel.clear') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                        .then(async function (response) {
                            const payload = await parseJsonResponse(response);
                            if (!response.ok) {
                                throw {status: response.status, payload};
                            }
                            return payload;
                        })
                        .then(function (data) {
                            successAlert(data.message || 'Data import telah dibersihkan');
                            dataReload('main_table');
                        })
                        .catch(function (error) {
                            errorAlert((error.payload && error.payload.message) || error.message || 'Gagal membersihkan data import');
                        });
                });
            });
        });

    </script>
@endsection
