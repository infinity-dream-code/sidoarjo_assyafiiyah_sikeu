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
    <link rel="stylesheet" href="{{asset('main/libs/datatables-select-bs5/select.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
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

    <form id="create-form" class="mainForm">
        @csrf
        <div class="card mb-6">
            <div class="card-header header-elements">
                <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
                <div class="card-header-elements ms-auto">
                    <div class="w-100">
                        <div class="row">
                            <div class="d-flex justify-content-center justify-content-md-end gap-4">
                                {{--                                <a type="button" class="btn btn-success"--}}
                                {{--                                   href="{{ route('admin.keuangan.tagihan-siswa.buat-tagihan.import.index') }}"--}}
                                {{--                                   title="Import Tagihan">--}}
                                {{--                                    <span class="ri-file-excel-2-line me-2"></span>--}}
                                {{--                                    Import Tagihan--}}
                                {{--                                </a>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body py-0">
                <form id="filterForm">
                    <h5>Filter</h5>
                    <div class="row row-cols-lg-2 row-cols-1 row-gap-5">
                        <div class="col order-0">
                            <div class="row d-flex align-items-center">
                                <div class="col-3">
                                    <label class="required form-label" for="tahun_pelajaran">
                                        Tahun Pelajaran
                                    </label>
                                </div>
                                <div class="col">
                                    <select class="form-select form-select-sm" id="tahun_pelajaran"
                                            name="tahun_pelajaran" data-width="100%"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Pelajaran">
                                        <option value=""></option>
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
                        </div>
                        <div class="col order-2">
                            <div class="row d-flex align-items-center">
                                <div class="col-3">
                                    <label class="form-label" for="cari_siswa">
                                        Nis / Nama
                                    </label>
                                </div>
                                <div class="col">
                                    <input class="form-control" id="cari_siswa" name="cari_siswa"
                                           placeholder="Nis / Nama">
                                </div>
                            </div>
                        </div>
                        <div class="col order-1">
                            <div class="row d-flex align-items-center">
                                <div class="col-3">
                                    <label class="required form-label" for="tahun_angkatan">
                                        Tahun Angkatan
                                    </label>
                                </div>
                                <div class="col">
                                    <select class="form-select form-select-sm" id="tahun_angkatan"
                                            name="tahun_angkatan" data-width="100%"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
                                        <option value=""></option>
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
                        </div>
                        <div class="col order-3">
                            <div class="row d-flex align-items-center">
                                <div class="col-3">
                                    <label class="form-label" for="kelas">
                                        Kelas
                                    </label>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="kelas" name="kelas"
                                            data-control="select2" data-placeholder="">
                                        <option></option>
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
                        </div>
                        <div class="col order-4">
                            <div class="row d-flex align-items-center">
                                <div class="col-3">
                                    <label class="form-label" for="fungsi">
                                        Fungsi
                                    </label>
                                </div>
                                <div class="col">
                                    <input class="form-control" id="fungsi" name="fungsi"
                                           placeholder="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col order-5">
                            <div class="row d-flex align-items-center">
                                <div class="col-3">
                                    <label class="form-label" for="nama_tagihan">
                                        Nama Tagihan
                                    </label>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="nama_tagihan" name="nama_tagihan"
                                            data-control="select2" data-placeholder="Pilih tagihan">
                                        <option value=""></option>
                                        @isset($tagihan)
                                            @foreach($tagihan as $item)
                                                <option
                                                    value="{{ $item->NamaAkun }}"
                                                    data-kode-akun="{{ $item->KodeAkun }}">{{ $item->NamaAkun }}</option>
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
                            <button type="reset" class="btn btn-outline-secondary button_reset_cari">
                                <span class="ri-reset-left-line me-2"></span>
                                Reset
                            </button>
                            <button type="button" class="btn btn-outline-primary button_cari_cari">
                                <span class="ri-search-line me-2"></span>
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive text-nowrap px-5card-siswa">
                <table class="table table-sm table-bordered table-hover"
                       id="table-siswa">
                    <thead class="table-light">
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="card-datatable table-responsive text-nowrap px-5card-siswa">
                <table class="table table-sm table-bordered table-hover"
                       id="table-post">
                    <thead class="table-light">
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-0">
                <div class="w-100">
                    <div class="row">
                        <div class="d-flex justify-content-center justify-content-md-end gap-4">
                            <button type="submit" value="Buat Tagihan" class="btn btn-primary">
                                <span class="ri-add-line me-2"></span>
                                Buat Tagihan
                            </button>
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

    <script>
        // const date = new Date();
        // const year = date.getFullYear();
        // const month = String(date.getMonth() + 1).padStart(2, '0');
        // const formattedDate = `${year}${month}`;
        // const fungsiInput = document.getElementById('fungsi');
        // fungsiInput.value = formattedDate;


        let dataColumns = [];
        let dataTableInit;
        let formId = '';
        let tableId = 'main_table';
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const select2 = $(`[data-control='select2']`);
        let htmlTable = '';
        let tableSiswa;
        let tablePost;
        let cardSiswa = $('.card-siswa');
        let mainElementId = 'tagihan[0][post]';

        let fileIdCOunt = 1;

        function clearErrorMessages(formId) {
            const form = document.querySelector(`#${formId}`);
            const errorElements = form.querySelectorAll('.invalid-feedback');
            const errorClass = form.querySelectorAll('.is-invalid');

            errorElements.forEach(element => element.textContent = '');
            errorClass.forEach(element => element.classList.remove('is-invalid'));
        }

        function resetSelect2(elementId) {
            $('#' + elementId).val(null).trigger('change');
        }

        function resetTextInput(elementId) {
            $('#' + elementId).val('');
        }

        function detailForm(data) {
            let input = [
                $('#id_thn_aka'),
                $('#kelas'),
                $('#cari_siswa'),
            ];
            input.forEach(function (item, key) {
                if (!data[key]) {
                    item.parent().parent().parent().parent().addClass('d-none');
                    item.prop('required', false);
                } else {
                    item.parent().parent().parent().parent().removeClass('d-none');
                    item.prop('required', true);
                }
                resetMainForm();
                // console.log(key, data[key], item);
            })
        }

        function resetMainForm() {
            resetSelect2('id_thn_aka');
            resetTextInput('id_thn_aka');
            resetSelect2('kelas');
            resetTextInput('kelas');
        }

        function refreshDataTable(newData = []) {
            tableSiswa.rows().deselect();
            tableSiswa.clear();
            tableSiswa.rows.add(newData);
            tableSiswa.draw();
            // newData.length === 0 ? '' : $('.select-all').prop('checked', true);
        }

        function refreshDataTableMasterHarga(newData = []) {
            tablePost.rows().deselect();
            tablePost.clear();
            tablePost.rows.add(newData);
            tablePost.draw();
        }

        function syncNamaTagihanFromAkun(namaAkun) {
            if (!namaAkun) {
                return;
            }
            const $select = $('#nama_tagihan');
            let matched = false;
            $select.find('option').each(function () {
                const val = String($(this).val() || '').trim();
                const text = String($(this).text() || '').trim();
                if (val === namaAkun || text === namaAkun) {
                    $select.val(val).trigger('change');
                    matched = true;
                    return false;
                }
            });
            if (!matched) {
                const option = new Option(namaAkun, namaAkun, true, true);
                $select.append(option).trigger('change');
            }
            createPeriode();
        }


        async function getSiswa(Angkatan, jenjang, Kelas, siswa = null) {
            let url = '{{route('admin.keuangan.tagihan-siswa.buat-tagihan.get-siswa')}}';
            let ajaxOptions = {
                url: url,
                type: 'get',
                datatype: 'json',
                data: {
                    'angkatan': Angkatan,
                    'jenjang': jenjang,
                    'kelas': Kelas,
                    'cari_siswa': siswa
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            }

            $.ajax(ajaxOptions).done(function (response) {
                refreshDataTable(response.data);
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

        async function getMasterHarga(Angkatan, kelas) {
            let url = '{{route('admin.keuangan.tagihan-siswa.buat-tagihan.get-master-harga')}}';
            let ajaxOptions = {
                url: url,
                type: 'get',
                datatype: 'json',
                data: {
                    'thn_aka': Angkatan,
                    'kelas': kelas,
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            }

            $.ajax(ajaxOptions).done(function (response) {
                refreshDataTableMasterHarga(response.data);
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const createForm = $('#create-form');
            const languageKey = 'datatables_id_language';
            const languageUrl = '/js/datatableCustom/id.json';

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

            let languageData = localStorage.getItem(languageKey);

            async function getDTLang() {
                if (!languageData) {
                    languageData = await fetchLanguageFile();
                } else {
                    languageData = JSON.parse(languageData);
                }
            }

            getDTLang();

            tableSiswa = $('#table-siswa').DataTable({
                columns: [
                    {data: 'CUSTID'},
                    {data: 'nis', title: 'NIS'},
                    {data: 'nama', title: 'NAMA'},
                    {data: 'kelas', title: 'Kelas'},
                    {data: 'jenjang', title: 'Jenjang'},
                    {data: 'angkatan', title: 'Angkatan'},
                ],
                columnDefs: [
                    {
                        targets: 0,
                        searchable: false,
                        orderable: false,
                        render: function (data) {
                            return `<input type="checkbox" id="siswa-checkbox-${data}" class="dt-checkboxes form-check-input checkbox-siswa" value="${data}">`;
                        },
                        checkboxes: {
                            selectRow: true,
                            selectAllRender: '<input id="siswa-checkbox-all" name="siswa-checkbox" type="checkbox" class="form-check-input select-all">'
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
                lengthChange: false,
                pageLength: 10,
                order: [[1, 'desc']],
                select: {
                    style: 'multi'
                },
                scrollX: true,
            });

            tablePost = $('#table-post').DataTable({
                columns: [
                    {data: 'kode_akun', title: ''},
                    {data: 'kode_akun', title: 'KODE'},
                    {data: 'nama_akun', title: 'NAMA AKUN'},
                    {data: 'nominal', title: 'NOMINAL'},
                ],
                columnDefs: [
                    {
                        targets: 0,
                        searchable: false,
                        orderable: false,
                        render: function (data) {
                            return `<input type="checkbox" id="table-post-checkbox-${data}" class="dt-checkboxes form-check-input checkbox-tagihan" value="${data}">`;
                        },
                        className: 'text-center',
                    }, {
                        targets: 3,
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row) {
                            let val = parseInt(data);
                            val = val.toLocaleString('id-ID');
                            return `
                            <div class="input-group input-tagihan">
                                <span class="input-group-text bg-body">Rp</span>
                                <input type="text" class="form-control bg-body rounded-end nominal-input formattedNumber"
                                    id="tagihan[${row.kode_akun}][nominal]" autocomplete="off" placeholder="Nominal Tagihan" value="${val}" readonly>
                                <div class="invalid-feedback" role="alert"></div>
                            </div>
                            `;
                        },
                        className: 'text-center',
                    },
                ],
                language: {
                    ...languageData,
                    emptyTable: "Klik tombol cari"
                },

                paging: true,
                serverSide: false,
                searching: false,
                lengthChange: false,
                pageLength: 10,
                order: [[1, 'desc']],
                select: {style: 'single'},
                scrollX: true,
            });

            createForm.on('reset', function (e) {
                setTimeout(function () {
                    refreshDataTable();
                    refreshDataTableMasterHarga();
                    cardSiswa.addClass('d-none');
                    cardSiswa.prev().addClass('d-none');
                    const select2InForm = select2.filter(`#create-form [data-control='select2']`);
                    $('#main_table tbody tr:not(:first)').remove();
                    let element = $('#' + mainElementId.replace(/([[\]])/g, '\\$1'));
                    if (element) {
                        element.trigger('change');
                    }
                    if (select2InForm.length) {
                        select2InForm.each(function () {
                            let $this = $(this);
                            $this.trigger('change');
                        });
                    }
                }, 0)
            });

            createForm.on('submit', function (e) {
                e.preventDefault();
                loadingAlert();

                let url = '{{route('admin.keuangan.tagihan-siswa.buat-tagihan.store')}}';
                let tipe = 'POST';
                const formId = createForm.attr('id');
                let data = createForm.serialize();
                let selectedRows = tableSiswa.rows({selected: true});
                let selectedTagihanRows = tablePost.rows({selected: true});

                selectedRows.every(function () {
                    let row = this.node();
                    let checkbox = $(row).find('.checkbox-siswa');

                    if (checkbox.is(':checked')) {
                        data += '&siswa[]=' + encodeURIComponent(checkbox.val());
                    }
                });

                let firstNamaAkun = null;
                selectedTagihanRows.every(function () {
                    let row = this.node();
                    let checkbox = $(row).find('.checkbox-tagihan');
                    let rowData = tablePost.row(row).data();

                    if (checkbox.is(':checked')) {
                        let checkVal = encodeURIComponent(checkbox.val());
                        let nominalInput = $(row).find('.nominal-input');
                        let nominalVal = encodeURIComponent(nominalInput.val());
                        let namaAkun = encodeURIComponent(rowData?.nama_akun || '');

                        if (!firstNamaAkun && rowData?.nama_akun) {
                            firstNamaAkun = rowData.nama_akun;
                        }

                        data += `&tagihan[${checkVal}][tagihan]=` + checkVal;
                        data += `&tagihan[${checkVal}][nominal]=` + nominalVal;
                        data += `&tagihan[${checkVal}][nama_akun]=` + namaAkun;
                    }
                });

                if (!$('#nama_tagihan').val() && firstNamaAkun) {
                    syncNamaTagihanFromAkun(firstNamaAkun);
                }
                if ($('#nama_tagihan').val()) {
                    data += '&nama_tagihan=' + encodeURIComponent($('#nama_tagihan').val());
                }
                if ($('#fungsi').val()) {
                    data += '&fungsi=' + encodeURIComponent($('#fungsi').val());
                }

                data += "&_token=" + csrfToken;

                let ajaxOptions = {
                    url: url,
                    type: tipe,
                    data: data,
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }
                clearErrorMessages(formId)
                $.ajax(ajaxOptions).done(function (responses) {
                    document.getElementById(formId).reset();
                    successAlert(responses.message);
                }).fail(function (xhr) {
                    if (xhr.status === 422) {
                        const errors = JSON.parse(xhr.responseText).error
                        const errMessage = xhr.responseJSON.message
                        errorAlert(errMessage);
                        if (errors) {
                            for (const [key, value] of Object.entries(errors)) {
                                console.log(key + ': ', value[0]);
                                const field = $(`[name="${key}"]`);
                                field.addClass('is-invalid');
                                field.next('.invalid-feedback').html(value[0]);
                            }
                        }
                    } else if (xhr.status === 419) {
                        typeof window.pingKeepAlive === 'function' && window.pingKeepAlive();
                    } else if (xhr.status === 500) {
                        errorAlert('Tidak dapat terhubung ke server, Silahkan periksa koneksi internet anda');
                    } else if (xhr.status === 403) {
                        errorAlert('Anda tidak memiliki izin untuk mengakses halaman ini');
                    } else if (xhr.status === 404) {
                        errorAlert('Halaman tidak ditemukan');
                    } else {
                        errorAlert('Terjadi kesalahan, silahkan coba memuat ulang halaman');
                    }
                })
            })

            let jenis = $('#jenis');
            jenis.on('change', function (e) {
                let isChecked = $(this).is(':checked');
                if (jenis.val() === 1) {
                    row.find('tagihan[*]cicilan').prop('readonly', isChecked);
                    row.find('tagihan[*]cicilan').prop('required', !isChecked);
                }
            })

            document.querySelector('#table-post tbody').addEventListener('click', function (e) {
                if ((e.target.tagName.toLowerCase() === 'input' || e.target.closest('input')) && e.target.type === 'text') {
                    e.stopPropagation();
                } else {
                    if (!e.target.classList.contains('checkbox-tagihan')) {
                        const row = e.target.closest('tr');
                        if (row) {
                            const checkbox = row.querySelector('.checkbox-tagihan');
                            if (checkbox) {
                                checkbox.checked = !checkbox.checked;
                                checkbox.dispatchEvent(new Event('change', {bubbles: true}));
                            }
                        }
                    }
                }
            });

            $(createForm).on('change', '.checkbox-tagihan', function () {
                let isChecked = $(this).is(':checked');
                let row = $(this).closest('tr');
                let rowData = tablePost.row(row).data();

                if (isChecked) {
                    $('#table-post tbody .checkbox-tagihan').not(this).prop('checked', false);
                    $('#table-post tbody tr').not(row).find('input.form-control')
                        .prop('readonly', true).prop('required', false);
                    if (rowData?.nama_akun) {
                        syncNamaTagihanFromAkun(rowData.nama_akun);
                    }
                }

                row.find('input.form-control').prop('readonly', !isChecked);
                row.find('input.form-control').prop('required', isChecked);
                if (jenis.val() === 1) {
                    row.find('tagihan[*]cicilan').prop('readonly', isChecked);
                    row.find('tagihan[*]cicilan').prop('required', !isChecked);
                }
            });

            $(createForm).on('click', '.button_cari_cari', function (e) {
                let angkatan = $('#tahun_angkatan').val();
                let thn_aka = $('#tahun_pelajaran').val();
                let kelas = $('#kelas').val();
                let jenjang = $('#jenjang').val();
                let cariSiswa = $('#cari_siswa').val()
                refreshDataTable();
                refreshDataTableMasterHarga();

                if (angkatan && kelas && thn_aka) {
                    getSiswa(angkatan, jenjang, kelas, cariSiswa)
                    getMasterHarga(angkatan, kelas)
                } else {
                    warningAlert(`Pastikan telah memilih Tahun Pelajaran, Angkatan dan kelas`)
                }
            });

            $(document).on('keypress', '.formattedNumber', function (e) {
                const charCode = e.which ? e.which : e.keyCode;
                if (charCode < 48 || charCode > 57) {
                    e.preventDefault();
                }
            });

            $(document).on('input', '.formattedNumber', function (e) {
                const formattedValue = $(this).val();
                const parsedNumber = parseInt(formattedValue.replace(/\./g, ''));

                if (!isNaN(parsedNumber)) {
                    const formattedString = parsedNumber.toLocaleString('id-ID');
                    $(this).val(formattedString);
                } else {

                }
            });

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

            $(document).on('change', '.select-post', function (e) {
                const selectedOption = $(this).find('option:selected');
                if (!selectedOption.val()) {
                    return;
                }
                const nominal = selectedOption.data('nominal');
                const nama = selectedOption.data('nama');
                const parentTd = this.closest('td');
                const siblingNominalInput = parentTd.nextElementSibling.nextElementSibling.querySelector('.nominal-input');
                const siblingNamaInput = parentTd.nextElementSibling.querySelector('.nama-tagihan-input');
                siblingNominalInput.value = nominal.toLocaleString('id-ID');
                siblingNamaInput.value = nama;
            });

            $('#' + mainElementId.replace(/([[\]])/g, '\\$1')).select2();

            $('#tahun_pelajaran').on('change', function (e) {
                createPeriode()
            })

            $('#nama_tagihan').on('change', function (e) {
                createPeriode()
            })

            createPeriode()
        });

        function createPeriode() {
            let tahun_pelajaran = $('#tahun_pelajaran');
            let fungsi = $('#fungsi');
            const tahunPelajaranVal = tahun_pelajaran.val();

            if (!tahunPelajaranVal) {
                fungsi.val('');
                return;
            }

            const partTahunPelajaran = tahunPelajaranVal.match(/\d{4}\/\d{4}/);
            if (!partTahunPelajaran) {
                fungsi.val('');
                return;
            }
            let partedTahunPelajaram = partTahunPelajaran[0].split("/");

            const bulanDariNama = {
                JANUARI: '01', FEBRUARI: '02', MARET: '03', APRIL: '04',
                MEI: '05', JUNI: '06', JULI: '07', AGUSTUS: '08',
                SEPTEMBER: '09', OKTOBER: '10', NOVEMBER: '11', DESEMBER: '12',
            };
            let periodeBulan = '{{ date('m') }}';
            const namaTagihan = String($('#nama_tagihan option:selected').val() || '').toUpperCase();
            for (const [bulan, kode] of Object.entries(bulanDariNama)) {
                if (namaTagihan.includes(bulan)) {
                    periodeBulan = kode;
                    break;
                }
            }

            if (periodeBulan < 7) {
                fungsi.val(partedTahunPelajaram[1] + periodeBulan)
            } else {
                fungsi.val(partedTahunPelajaram[0] + periodeBulan)
            }
        }
    </script>
@endsection
