@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
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

    <form class="mainForm" id="form-pindah-kelas" action="#">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row mb-3">
                    <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
                </div>
                <form id="rekapForm">
                    <div class="row">
                        <div class="mb-5">
                            <label class="required form-label" for="dari_kelas">
                                Kelas Asal
                            </label>
                            <select class="form-select" id="dari_kelas" name="dari_kelas"
                                    data-control="select2" data-placeholder="Dari Kelas">
                                <option></option>
                                @isset($kelas)
                                    @foreach($kelas as $item)
                                        <option value="{{$item->id}}" {{$loop->index == 1 ? 'selected' : ''}}>
                                            {{$item->unit}} - {{$item->jenjang}} {{$item->kelas}} </option>
                                    @endforeach
                                @else
                                    <option>data kosong</option>
                                @endisset
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="required form-label" for="ke_kelas">
                                Kelas Tujuan
                            </label>
                            <select class="form-select" id="ke_kelas" name="ke_kelas"
                                    data-control="select2" data-placeholder="Ke Kelas" required>
                                <option></option>
                                @isset($kelas)
                                    @foreach($kelas as $item)
                                        <option value="{{$item->id}}" {{$loop->index == 1 ? 'selected' : ''}}>
                                            {{$item->unit}} - {{$item->jenjang}} {{$item->kelas}} </option>
                                    @endforeach
                                @else
                                    <option>data kosong</option>
                                @endisset
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label" for="cari_siswa">
                                Nis / Nama Siswa
                            </label>
                            <input class="form-control" id="cari_siswa" name="cari_siswa"
                                   placeholder="Nis / Nama Siswa">
                        </div>
                        <div class="mb-5">
                            <label class="form-label required" for="pindah">
                                Pemindahan
                            </label>
                            <select class="form-select" id="pindah" name="pindah"
                                    data-control="select2"
                                    data-placeholder="Pilih Pemindahan Tagihan" required>
                                <option value="all">Pindahkan semua Anak Pada Kelas</option>
                                <option value="satuan" selected>Pindahkan Hanya Anak Yang Dipilih</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-0 pt-0">
                <div class="row">
                    <div class="w-100">
                        <div class="row">
                            <div class="d-flex justify-content-center justify-content-md-end gap-4">
                                <button type="reset" class="btn btn-secondary">
                                    <span class="ri-reset-left-line me-2"></span>
                                    Reset
                                </button>
                                <button type="button" class="btn btn-primary button_cari_cari">
                                    <span class="ri-search-line me-2"></span>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive text-nowrap">
                <table class="table table-sm table-bordered table-hover"
                       id="table-siswa">
                    <thead class="table-light">

                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="card-footer border-0 pt-0">
                <div class="row">
                    <div class="w-100">
                        <div class="row">
                            <div class="d-flex justify-content-center justify-content-md-end gap-4">
                                <button type="submit" class="btn btn-warning">
                                    <span class="ri-drag-move-line me-2"></span>
                                    Pindah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/helper/errorInputHelper.min.js')}}"></script>

    <script type="text/javascript">
        let dataColumns = [];
        let dataTableInit;
        let formClass = '.mainForm';
        let formPage = $('#form-pindah-kelas');
        let tableId = 'main_table';
        const select2 = $(`[data-control='select2']`);
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let tableSiswa;

        const languageKey = 'datatables_id_language';
        const languageUrl = 'https://cdn.datatables.net/plug-ins/2.0.6/i18n/id.json';

        async function fetchLanguageFile() {
            try {
                const response = await fetch(languageUrl);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                localStorage.setItem(languageKey, JSON.stringify(data)); // Save to localStorage
                return data;
            } catch (error) {
                console.error('Error fetching language file:', error);
                return null;
            }
        }

        function refreshDataTable(newData = []) {
            tableSiswa.rows().deselect();
            tableSiswa.clear();
            tableSiswa.rows.add(newData);
            tableSiswa.draw();
            // newData.length === 0 ? '' : $('.select-all').prop('checked', true);
        }

        function getSiswa(Per, Kelas, siswa = null) {
            let url = '{{route('admin.master-data.data-siswa.get-siswa')}}';
            let ajaxOptions = {
                url: url,
                type: 'get',
                datatype: 'json',
                data: {
                    'per': Per,
                    'kelas': Kelas,
                    'siswa': siswa,
                    'nis': true
                },
            }
            $.ajax(ajaxOptions).done(function (response) {
                refreshDataTable(response);
            }).fail(function (xhr) {
                if (xhr.status === 422) {
                    errorAlert('Gagal mendapat data siswa')
                } else if (xhr.status === 419) {
                    typeof window.pingKeepAlive === 'function' && window.pingKeepAlive()
                } else if (xhr.status === 500) {
                    errorAlert('Tidak dapat terhubung ke server, Silahkan periksa koneksi internet anda')
                } else if (xhr.status === 403) {
                    errorAlert('Anda tidak memiliki izin untuk mengakses halaman ini')
                } else if (xhr.status === 404) {
                    errorAlert('Halaman tidak ditemukan')
                } else {
                    errorAlert('Terjadi kesalahan, silahkan coba memuat ulang halaman')
                }
            })
        }

        document.addEventListener("DOMContentLoaded", function () {
            if (select2.length) {
                select2.each(function () {
                    let $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Pilih satu',
                        dropdownParent: $this.parent()
                    });
                });
            }

            $(formPage).on('click', '.button_cari_cari', function (e) {
                let per = 'kelas';
                let kelas = $('#dari_kelas').val()
                let siswa = $('#cari_siswa').val()
                if (per === 'siswa' || per === 'kelas') {
                    getSiswa(per, kelas, siswa)
                }
            });

            $(formPage).on('reset', function (e) {
                setTimeout(function () {
                    refreshDataTable();
                    const select2InForm = select2.filter(formClass + ` [data-control='select2']`);
                    $('#main_table tbody tr:not(:first)').remove();
                    if (select2InForm.length) {
                        select2InForm.each(function () {
                            let $this = $(this);
                            $this.trigger('change');
                        });
                    }
                }, 0)
            });

            document.getElementById("form-pindah-kelas")
                .addEventListener("submit", function (e) {
                    e.preventDefault();
                    loadingAlert('Memindah siswa ... <br><span class="text-danger"> *</span>Jangan menutup atau memuat ulang halaman!');
                    let url = "";
                    let method = "";
                    const formId = this.id;
                    let formData = new FormData(this);
                    let selectedRows = [];

                    if (formId === "form-pindah-kelas") {
                        url = '{{route('admin.master-data.pindah-kelas.store')}}';
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

                    tableSiswa.rows().every(function() {
                        let rowData = this.data();
                        let rowNode = this.node();
                        let selectedValue = $(rowNode).find('input[type="checkbox"]').prop('checked');
                        if (selectedValue) {
                            rowData.selectedValue = selectedValue;
                            formData.append('siswa[]', rowData.id);
                        }
                    });

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
                            document.getElementById(formId).reset();
                            successAlert(data.message);
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

            let languageData = localStorage.getItem(languageKey);

            if (!languageData) {
                languageData = fetchLanguageFile();
            } else {
                languageData = JSON.parse(languageData);
            }

            tableSiswa = $('#table-siswa').DataTable({
                columns: [
                    {data: 'id'},
                    {data: 'nis', title: 'NIS'},
                    {data: 'nama', title: 'NAMA'},
                    {data: 'no_daftar', title: 'No Daftar'},
                    {data: 'kelas', title: 'Kelas'},
                    {data: 'thn_aka', title: 'Angkatan'},
                ],
                columnDefs: [
                    {
                        targets: 0,
                        searchable: false,
                        orderable: false,
                        render: function (data) {
                            return `<input type="checkbox" id="siswa-checkbox-${data}" class="dt-checkboxes form-check-input" value="${data}">`;
                        },
                        checkboxes: {
                            selectRow: true,
                            selectAllRender: '<input id="siswa-checkbox" name="siswa-checkbox" type="checkbox" class="form-check-input select-all">'
                        },
                        className: 'text-center',
                    },
                ],
                language: {
                    ...languageData,
                    emptyTable: "Tidak ada siswa yang sesuai kriteria pencarian"
                },

                paging: true,
                serverSide: false,
                searching: false,
                lengthChange: true,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pageLength: 10,
                order: [[1, 'desc']],
                select: {
                    style: 'multi'
                },
                scrollX: true,
            });
        });

        // tableSiswa.on('select.dt', function (e, dt, type, indexes) {
        //     let selectedData = tableSiswa.table().rows(indexes).data().toArray();
        //     selectedData.forEach(row => {
        //         if (!selectedRows[row.CUSTID]) {
        //             selectedRows[row.CUSTID] = [];
        //         }
        //         if (!selectedRows[row.CUSTID].includes(row.id)) {
        //             selectedRows[row.CUSTID].push(row.id);
        //         }
        //     });
        // });
        //
        // tableSiswa.on('deselect.dt', function (e, dt, type, indexes) {
        //     let deselectedData = tableSiswa.DataTable().table().rows(indexes).data().toArray();
        //     deselectedData.forEach(row => {
        //         if (selectedRows[row.CUSTID]) {
        //             selectedRows[row.CUSTID] = selectedRows[row.CUSTID].filter(id => id !== row.id);
        //             if (selectedRows[row.CUSTID].length === 0) {
        //                 delete selectedRows[row.CUSTID];
        //             }
        //         }
        //     });
        // });
    </script>
@endsection
