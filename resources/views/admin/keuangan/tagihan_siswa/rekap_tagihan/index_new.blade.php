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
                                            data-placeholder="Pilih Kelas">
                                        @isset($thn_aka)
                                            <option value="all">Semua</option>

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
                                <label for="filter[sekolah]"
                                       class="col-sm-4 col-form-label text-capitalize form-label">sekolah</label>
                                <div class="col">
                                    <select class="form-select" id="filter[sekolah]"
                                            name="filter[sekolah]"
                                            data-control="select2"
                                            data-placeholder="Pilih Sekolah">
                                        @isset($sekolah)
                                            <option value="all">Semua</option>
                                            @foreach($sekolah as $item)
                                                <option
                                                    value="{{$item->CODE01 ?? $item->DESC01}}">{{$item->DESC01}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter[kelas]" class="col-sm-4 col-form-label form-label">
                                    Kelas
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[kelas]"
                                            name="filter[kelas][]"
                                            multiple
                                            data-control="select2"
                                            data-placeholder="Pilih Kelas">
                                        @isset($kelas)
                                            <option value="all">Semua</option>
                                            @foreach($kelas as $item)
                                                <option value="{{$item->unit}},{{$item->jenjang}},{{$item->kelas}}">
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
                                <label for="filter[nis]"
                                       class="col-sm-4 col-form-label text-capitalize form-label">NIS</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="nis" id="filter[nis]" name="filter[nis]">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row">
                                <label for="filter_dari_tanggal"
                                       class="col-sm-4 col-form-label text-capitalize form-label">dari
                                    tanggal buat</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="dari tanggal" id="filter_dari_tanggal"
                                           name="filter[dari_tanggal]">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="filter_sampai_tanggal"
                                       class="col-sm-4 col-form-label text-capitalize form-label">sampai
                                    tanggal buat</label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="sampai tanggal" id="filter_sampai_tanggal"
                                           name="filter[sampai_tanggal]">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter[rek]" class="col-sm-4 col-form-label form-label">
                                    Kode Post Tagihan
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[kode_rek]"
                                            name="filter[kode_rek]"
                                            data-control="select2"
                                            data-placeholder="Pilih Tahun Akademik">
                                        <option value="all">Semua</option>
                                        @isset($akun)
                                            @foreach($akun as $item)
                                                <option
                                                    value="{{$item->KodeAkun}}">{{$item->KodeAkun}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter_periode_mulai" class="col-sm-4 col-form-label form-label">
                                    Periode Mulai
                                </label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="periode mulai (yyyymm)" id="filter_periode_mulai"
                                           name="filter[periode_mulai]">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter_periode_akhir" class="col-sm-4 col-form-label form-label">
                                    Periode Akhir
                                </label>
                                <div class="col">
                                    <input type="text" class="form-control form-control"
                                           placeholder="periode akhir (yyyymm)" id="filter_periode_akhir"
                                           name="filter[periode_akhir]">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="filter[nama_tagihan]" class="col-sm-4 col-form-label form-label">
                                    Nama Tagihan
                                </label>
                                <div class="col">
                                    <select class="form-select" id="filter[nama_tagihan]"
                                            name="filter[nama_tagihan][]"
                                            multiple
                                            data-control="select2"
                                            data-placeholder="Pilih Nama Tagihan">
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
                        <div class="d-flex justify-content-center flex-column flex-md-row justify-content-md-end gap-4">
                            <button type="button" class="btn btn-facebook" id="cetak-rekap">
                                <span class="ri-file-excel-line me-2"></span>
                                Cetak Rekap
                            </button>
                            <button type="button" class="btn btn-facebook" id="cetak-per-nis">
                                <span class="ri-user-line me-2"></span>
                                Cetak Per Nis
                            </button>
                            <button type="button" class="btn btn-facebook" id="cetak-kartu-siswa">
                                <span class="ri-profile-line me-2"></span>
                                Cetak Kartu Siswa
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
            <div class="card-datatable table-responsive text-nowrap px-5 card-siswa">
                <table class="table table-sm table-bordered table-hover"
                       id="main_table">
                    <thead class="table-light">
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="15" class="text-end">Total</th>
                        <th id="total-tagihan-excel" class="text-end">Rp. 0</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
@endsection

@section('script')
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
     <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>
    <script src="{{asset('main/libs/moment/moment.js')}}"></script>
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>

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
            rowId: 'AA',
            cache: false,
            buttons: ["excel", "pdf", "print"],
            pdfPageSize: 'A3',
            pdfOrientation: 'landscape',
            pdfFontSize: 6,
            pdfHeaderFontSize: 7,
            pdfMargins: [8, 12, 8, 12],
        };

        let columnsUrl = '{{($columnsUrl??null)}}';
        let dataUrl = '{{($datasUrl??null)}}';
        let column;
        let filterForm = document.getElementById('filter-form');


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
            const formatRupiah = (value) => {
                const amount = Number(value || 0);
                return 'Rp. ' + amount.toLocaleString('id-ID');
            };

            const updateTotalFooter = () => {
                const dt = DT?.[dtOptions.tableId];
                if (!dt) return;

                const rows = dt.rows({search: 'applied'}).data().toArray();
                const total = rows.reduce((acc, row) => acc + Number(row?.BILLAM || 0), 0);
                $('#total-tagihan-excel').text(formatRupiah(total));
            };

            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
                const tryBindFooterTotal = () => {
                    const dt = DT?.[dtOptions.tableId];
                    if (!dt) {
                        setTimeout(tryBindFooterTotal, 250);
                        return;
                    }
                    dt.on('draw', updateTotalFooter);
                    updateTotalFooter();
                };
                tryBindFooterTotal();
                if (dtOptions.formId) {
                    let filterForm = $(`#${dtOptions.formId}`);
                    filterForm.on('submit', function (e) {
                        e.preventDefault();
                        dataReFilter(dtOptions.tableId);
                        setTimeout(updateTotalFooter, 200);
                    });
                    filterForm.on('reset', function (e) {
                        setTimeout(function () {
                            dataReFilter(dtOptions.tableId);
                            updateTotalFooter();
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

            // filterForm.addEventListener('submit', function (e) {
            //     e.preventDefault();
            //     getData();
            // });

            // filterForm.addEventListener('reset', function (e) {
            //     setTimeout(function () {
            //         console.log('testing')
            //         tableData.rows().deselect();
            //         tableData.clear();
            //         tableData.draw();
            //
            //         const select2InForm = select2.filter(`#filter-form [data-control='select2']`);
            //         if (select2InForm.length) {
            //             select2InForm.each(function () {
            //                 let $this = $(this);
            //                 $this.trigger('change');
            //             });
            //         }
            //     }, 0)
            // });
            //
            // const languageKey = 'datatables_id_language';
            // const languageUrl = '/js/datatableCustom/id.json';
            //
            // async function fetchLanguageFile() {
            //     try {
            //         const response = await fetch(languageUrl);
            //         if (!response.ok) throw new Error('Network response was not ok');
            //         const data = await response.json();
            //         localStorage.setItem(languageKey, JSON.stringify(data)); // Save to localStorage
            //         return data;
            //     } catch (error) {
            //         console.error('Error fetching language file:', error);
            //         return null;
            //     }
            // }
            //
            // let languageData = localStorage.getItem(languageKey);
            //
            // async function getDTLang() {
            //     if (!languageData) {
            //         languageData = await fetchLanguageFile();
            //     } else {
            //         languageData = JSON.parse(languageData);
            //     }
            // }
            //
            // getDTLang();
            //
            // function initDataTable() {
            //     tableData = $('#table-siswa').DataTable({
            //         columns: column,
            //         columnDefs: [
            //             {
            //                 targets: 0,
            //                 searchable: false,
            //                 orderable: false,
            //                 render: function (data) {
            //                     return `<input type="checkbox" id="siswa-checkbox-${data}" class="dt-checkboxes form-check-input checkbox-siswa" value="${data}">`;
            //                 },
            //                 checkboxes: {
            //                     selectRow: true,
            //                     selectAll: false,
            //                 },
            //                 className: 'text-center',
            //             },
            //         ],
            //         language: {
            //             ...languageData,
            //             emptyTable: "Tidak ada siswa yang sesuai kriteria pencarian"
            //         },
            //
            //         paging: true,
            //         serverSide: false,
            //         searching: false,
            //         lengthChange: false,
            //         pageLength: 10,
            //         order: [[1, 'desc']],
            //         select: {
            //             style: 'single'
            //         },
            //         scrollX: true,
            //     });
            // }

            // if (columnsUrl) {
            //     let request = new Request(
            //         columnsUrl, {
            //             method: "GET",
            //             headers: {
            //                 'X-CSRF-TOKEN': csrfToken,
            //             }
            //         }
            //     )
            //
            //     fetch(request)
            //         .then(async response => {
            //             const data = await response.json().catch(() => ({}));
            //             if (!response.ok) {
            //                 throw {status: response.status, message: data.message || response.statusText};
            //             }
            //             return data;
            //         })
            //         .then(data => {
            //             column = data;
            //             initDataTable();
            //         })
            //         .catch(error => {
            //             if (error.status === 422) {
            //                 const errors = error.error || error.errors;
            //                 errorAlert(error.message);
            //                 if (errors) {
            //                     processErrors(errors)
            //                 }
            //             } else {
            //                 const errorMessages = {
            //                     401: 'Sesi anda sudah habis 🙏 <br>Silahkan muat ulang halaman untuk melanjutkan! <br> jika masalah masih terjadi silahkan login kembali!',
            //                     403: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
            //                     404: 'Halaman yang dituju tidak ditemukan 🧐',
            //                     405: 'Metode tidak valid 🧐 <br>silahkan muat ulang halaman dan coba lagi!',
            //                     419: 'Sesi anda sudah habis 🙏 <br>Silahkan muat ulang halaman untuk melanjutkan! <br> jika masalah masih terjadi silahkan login kembali!',
            //                     429: 'Terlalu banyak permintaan akses <br>silahkan tunggu beberapa saat 🙏',
            //                 };
            //                 errorAlert(errorMessages[error.status] || "Terjadi kesalahan, silahkan coba memuat ulang halaman");
            //             }
            //         });
            // }

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

            const $namaTagihan = $('[name="filter[nama_tagihan][]"]');
            $namaTagihan.on('select2:select', function (e) {
                if (e.params?.data?.id === 'all') {
                    // "Semua" berdiri sendiri agar bisa dihapus dengan tombol x.
                    $namaTagihan.val(['all']).trigger('change.select2');
                    return;
                }

                const selected = $namaTagihan.val() || [];
                if (selected.includes('all') && selected.length > 1) {
                    // Jika user pilih item spesifik, keluarkan "Semua".
                    $namaTagihan.val(selected.filter(item => item !== 'all')).trigger('change.select2');
                }
            });

            $namaTagihan.on('select2:unselect', function (e) {
                if (e.params?.data?.id === 'all') {
                    // Biarkan kosong saat "Semua" dihapus.
                    $namaTagihan.val(null).trigger('change.select2');
                }
            });

            const dariTanggal = $('#filter_dari_tanggal');
            const sampaiTanggal = $('#filter_sampai_tanggal');
            const periodeMulai = $('#filter_periode_mulai');
            const periodeAkhir = $('#filter_periode_akhir');

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
        })

        function parseDDMMYYYY(str) {
            if (!str) return null;
            const [dd, mm, yyyy] = str.split("-").map(Number);
            if (!dd || !mm || !yyyy) return null;
            return new Date(yyyy, mm - 1, dd, 12);
        }

        function fullBorder() {
            return {
                top: {style: "thin"},
                left: {style: "thin"},
                bottom: {style: "thin"},
                right: {style: "thin"}
            };
        }

        function cellBGColor(){
            return {
                type: 'pattern',
                pattern: 'solid',
                fgColor: { argb: 'FFEBE1FF' }
            }
        }

        function generateTableRekapTagihan(data) {
            if (!Array.isArray(data) || data.length === 0) return null;

            const kelasOrder = [];
            const kelasSet = new Set();
            const kelompokOrder = [];
            const kelompokSet = new Set();

            data.forEach((row) => {
                const kelasLabel = `${row.CODE02 ?? '-'}`.trim();
                const kelompok = `${row.DESC03 ?? ''}`.trim() || 'Reguler';
                if (!kelasSet.has(kelasLabel)) {
                    kelasSet.add(kelasLabel);
                    kelasOrder.push(kelasLabel);
                }
                if (!kelompokSet.has(kelompok)) {
                    kelompokSet.add(kelompok);
                    kelompokOrder.push(kelompok);
                }
            });

            const rowMap = new Map();
            data.forEach((row) => {
                const kelasLabel = `${row.CODE02 ?? '-'}`.trim();
                const kelompok = `${row.DESC03 ?? ''}`.trim() || 'Reguler';
                const tahun = row.BTA ?? '-';
                const kode = row.KodePost ?? '-';
                const nama = row.BILLNM ?? row.NamaAkun ?? '-';
                const val = Number(row.BILLAM ?? 0);
                if (!val) return;

                const mapKey = `${tahun}||${kode}||${nama}`;
                if (!rowMap.has(mapKey)) {
                    rowMap.set(mapKey, { tahun, kode, nama, byClass: {}, total: 0 });
                }
                const target = rowMap.get(mapKey);
                if (!target.byClass[kelasLabel]) target.byClass[kelasLabel] = {};
                target.byClass[kelasLabel][kelompok] = (target.byClass[kelasLabel][kelompok] ?? 0) + val;
                target.total += val;
            });

            const rows = Array.from(rowMap.values()).sort((a, b) => {
                if (a.tahun !== b.tahun) return String(a.tahun).localeCompare(String(b.tahun));
                return String(a.kode).localeCompare(String(b.kode));
            });

            return { kelasOrder, kelompokOrder, rows };
        }

        async function exportRekapTagihanExcel(matrix, params) {
            if (!matrix || !matrix.rows || matrix.rows.length === 0) return;
            const wbTitle = "REKAP TAGIHAN";
            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet(wbTitle);
            const rows = matrix.rows;
            const kelasOrder = matrix.kelasOrder;
            const kelompokOrder = matrix.kelompokOrder;

            ws.insertRow(1, [wbTitle]);
            const sekolahLabel = $('#filter\\[sekolah\\] option:selected').text()?.trim() || 'Semua';
            ws.insertRow(2, ["Sekolah", sekolahLabel]);
            ws.insertRow(3, ["Tahun Pelajaran", params.get('filter[tahun_pelajaran]') || 'Semua']);
            ws.insertRow(4, ["Periode Mulai", params.get('filter[periode_mulai]') || '-']);
            ws.insertRow(5, ["Periode Akhir", params.get('filter[periode_akhir]') || '-']);
            ws.insertRow(6, ["Dari Tanggal", parseDDMMYYYY(params.get('filter[dari_tanggal]') || '') || '-']);
            ws.insertRow(7, ["Sampai Tanggal", parseDDMMYYYY(params.get('filter[sampai_tanggal]') || '') || '-']);

            [6, 7].forEach(rowNumber => {
                const cell = ws.getRow(rowNumber).getCell(2);
                if (cell.value instanceof Date) if (typeof formatTanggalIndonesia === 'function') { cell.value = formatTanggalIndonesia(cell.value); } else { cell.numFmt = "dd-mm-yyyy"; }
            });
            [1,2,3,4,5,6,7].forEach(rowNumber => {
                ws.getRow(rowNumber).eachCell({includeEmpty: true}, cell => cell.font = {bold: true});
            });

            ws.insertRow(9, []);
            const headerRow1Number = 10;
            const headerRow1 = ws.getRow(headerRow1Number);
            const headerRow2 = ws.getRow(headerRow1Number + 1);

            let col = 1;
            headerRow1.getCell(col).value = 'Thn Akademik'; ws.mergeCells(headerRow1Number, col, headerRow1Number + 1, col); col++;
            headerRow1.getCell(col).value = 'Kode'; ws.mergeCells(headerRow1Number, col, headerRow1Number + 1, col); col++;
            headerRow1.getCell(col).value = 'Nama'; ws.mergeCells(headerRow1Number, col, headerRow1Number + 1, col); col++;

            kelasOrder.forEach((kelas) => {
                const startCol = col;
                kelompokOrder.forEach((k) => {
                    headerRow2.getCell(col).value = k;
                    col++;
                });
                headerRow2.getCell(col).value = 'Sum';
                const endCol = col;
                ws.mergeCells(headerRow1Number, startCol, headerRow1Number, endCol);
                headerRow1.getCell(startCol).value = kelas;
                col++;
            });

            headerRow1.getCell(col).value = 'Total';
            ws.mergeCells(headerRow1Number, col, headerRow1Number + 1, col);
            const lastCol = col;

            for (let i = 1; i <= lastCol; i++) {
                ws.getColumn(i).width = i <= 3 ? [12, 10, 26][i - 1] : 14;
                [headerRow1, headerRow2].forEach((r) => {
                    const cell = r.getCell(i);
                    cell.font = {bold: true};
                    cell.alignment = {horizontal: "center", vertical: "middle"};
                    cell.border = fullBorder();
                    cell.fill = cellBGColor();
                });
            }

            const dataStartRow = headerRow1Number + 2;
            let currentRow = dataStartRow;
            rows.forEach((r, idx) => {
                const row = ws.getRow(currentRow);
                row.getCell(1).value = idx === 0 ? (r.tahun || '') : '';
                row.getCell(2).value = r.kode;
                row.getCell(3).value = r.nama;
                let c = 4;
                kelasOrder.forEach((kelas) => {
                    let subtotalKelas = 0;
                    kelompokOrder.forEach((k) => {
                        const val = Number(r.byClass?.[kelas]?.[k] ?? 0);
                        subtotalKelas += val;
                        row.getCell(c).value = val;
                        row.getCell(c).numFmt = '#,##0';
                        row.getCell(c).alignment = {horizontal: 'right'};
                        c++;
                    });
                    row.getCell(c).value = subtotalKelas;
                    row.getCell(c).numFmt = '#,##0';
                    row.getCell(c).alignment = {horizontal: 'right'};
                    c++;
                });
                row.getCell(c).value = Number(r.total ?? 0);
                row.getCell(c).numFmt = '#,##0';
                row.getCell(c).alignment = {horizontal: 'right'};
                for (let i = 1; i <= lastCol; i++) row.getCell(i).border = fullBorder();
                currentRow++;
            });

            const totalRow = ws.getRow(currentRow);
            totalRow.getCell(3).value = 'Total';
            totalRow.getCell(3).font = {bold: true};
            for (let i = 4; i <= lastCol; i++) {
                const colLetter = ws.getColumn(i).letter;
                totalRow.getCell(i).value = {formula: `SUM(${colLetter}${dataStartRow}:${colLetter}${currentRow - 1})`};
                totalRow.getCell(i).numFmt = '#,##0';
                totalRow.getCell(i).font = {bold: true};
                totalRow.getCell(i).alignment = {horizontal: 'right'};
            }
            for (let i = 1; i <= lastCol; i++) {
                totalRow.getCell(i).border = fullBorder();
                totalRow.getCell(i).fill = cellBGColor();
            }

            const buffer = await wb.xlsx.writeBuffer();
            const blob = new Blob([buffer], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
            const a = document.createElement("a");
            a.href = URL.createObjectURL(blob);
            a.download = `${wbTitle}.xlsx`;
            a.click();
        }

        document.getElementById('cetak-rekap').addEventListener('click', async function (e) {
            e.preventDefault();
            let url = '{{route('admin.keuangan.tagihan-siswa.rekap-tagihan.get-data')}}';
            const form = new FormData(document.getElementById('filter-form'));
            const params = new URLSearchParams();
            for (const [key, value] of form.entries()) {
                params.append(key, value);
            }
            params.append('draw', '2');
            params.append('start', '0');
            params.append('length', 'poll');
            const fullUrl = `${url}?${params.toString()}`;
            const request = new Request(
                fullUrl, {
                    method: "GET",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

            try {
                const res = await fetch(request);
                if (!res.ok) {
                    const errorBody = await res.json().catch(() => ({}));
                    throw {
                        status: res.status,
                        message: errorBody.message || 'Terjadi kesalahan',
                        error: errorBody.error,
                        errors: errorBody.errors
                    };
                }
                const result = await res.json();
                const rows = result.data || [];
                if (!rows.length) {
                    warningAlert('Data tagihan tidak ditemukan');
                    return;
                }
                const matrix = generateTableRekapTagihan(rows);
                await exportRekapTagihanExcel(matrix, params);
                successAlert('Sukses, file rekap tagihan berhasil diunduh');
            } catch (error) {
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
            }
        });

        document.getElementById('cetak-kartu-siswa').addEventListener('click', function (e) {
            e.preventDefault();
            let url = '{{route('admin.keuangan.tagihan-siswa.rekap-tagihan.cetak-kartu-siswa')}}';
            const form = new FormData(document.getElementById('filter-form'));
            const params = new URLSearchParams();
            for (const [key, value] of form.entries()) {
                params.append(key, value);
            }
            let data = DT[`${dtOptions.tableId}`].rows({selected: true}).data();

            if(!data[0]){
                warningAlert('silahkan pilih siswa!')
                return;
            }
            params.append('custid', data[0].CUSTID ?? data[0].custid ?? '')
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
                            message: errorBody.message || errorBody.error || 'Terjadi kesalahan',
                            error: errorBody.error,
                            errors: errorBody.errors
                        };
                    }
                    return res.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    window.open(url, '_blank');
                    successAlert('Sukses, Kartu siswa terbuka pada tab baru');
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
        })

        document.getElementById('cetak-per-nis').addEventListener('click', function (e) {
            e.preventDefault();
            const selected = DT[`${dtOptions.tableId}`].rows({selected: true}).data();
            if (!selected[0]) {
                warningAlert('Silahkan pilih baris siswa pada tabel terlebih dahulu!');
                return;
            }

            loadingAlert('Membuat rekap per NIS...');
            let url = '{{route('admin.keuangan.tagihan-siswa.rekap-tagihan.cetak-per-nis')}}';
            const form = new FormData(document.getElementById('filter-form'));
            const params = new URLSearchParams();
            for (const [key, value] of form.entries()) {
                params.append(key, value);
            }
            params.append('custid', selected[0].CUSTID ?? selected[0].custid ?? '');

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
                        const errorBody = contentType.includes('application/json')
                            ? await res.json().catch(() => ({}))
                            : {};
                        throw {
                            status: res.status,
                            message: errorBody.message || 'Gagal membuat rekap per NIS',
                            error: errorBody.error,
                            errors: errorBody.errors
                        };
                    }
                    return res.blob();
                })
                .then(blob => {
                    const blobUrl = URL.createObjectURL(blob);
                    window.open(blobUrl, '_blank');
                    Swal.close();
                    successAlert('Sukses, rekap per NIS terbuka pada tab baru');
                })
                .catch(error => {
                    if (error.status === 422 || error.message) {
                        errorAlert(error.message || 'Gagal membuat rekap per NIS');
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
    </script>
@endsection
