@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css')}}">
    <style>
        .select2-container--default .select2-results__option[aria-disabled=true] {
            display: none;
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
                                <label class="form-label" for="filter[unit]">
                                    Tingkat
                                </label>
                                <select class="form-select" id="filter[unit]" name="filter[unit]"
                                        data-control="select2" data-placeholder="Pilih unit">
                                    <option value="all">Semua</option>
                                    @isset($unit)
                                        @foreach($unit as $item)
                                            <option
                                                value="{{$item->CODE01}}" data-group="{{$item->DESC01}}">{{$item->DESC01}}</option>
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
                                                value="{{$item->unit}}~{{$item->jenjang}}~{{$item->kelas}}" data-group="{{$item->unit}}">
                                                {{$item->unit}} - {{$item->jenjang}} {{$item->kelas}}
                                            </option>
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
                            <button type="button" class="btn btn-facebook btn-print-rekap" id="btn-print-rekap">
                                <span class="ri-file-text-line me-2"></span>
                                Cetak Rekap
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
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>
    <script src="{{asset('main/libs/moment/moment.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>

    <script type="text/javascript" defer>
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
            select: false,
            cache: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: ['copy', 'excel', 'pdf'],
        };

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

            $("[name='filter[unit]']").on('change', function () {
                const selectedGroup = $(this).find(':selected').data('group');
                if(!selectedGroup) return;
                const $kelasSelect = $("[name='filter[kelas]']");
                if($(this).val() === 'all'){
                    $kelasSelect.find('option').each(function () {
                        $(this).prop('disabled', false);
                    });
                }else{
                    $kelasSelect.find('option').each(function () {
                        const group = $(this).data('group');
                        if (!group) return;
                        $(this).prop('disabled', group.toLowerCase() !== selectedGroup.toLowerCase());
                    });
                }
                $kelasSelect.val('all').trigger('change.select2');
            });

            const $postInput = $('#post');
            $postInput.on('select2:select', function(e) {
                if (e.params.data.id === 'all') {
                    $('#post option').prop('selected', true);
                    $postInput.trigger('change');
                }
            });

            $postInput.on('select2:unselect', function (e) {
                if (e.params.data.id === 'all') {
                    let selected = $postInput.val() || [];
                    if (selected.length > 0) {
                        $postInput.val([selected[0]]).trigger('change');
                    } else {
                        $postInput.val(null).trigger('change');
                    }
                }
            });

            $postInput.on('change', function () {
                let selected = $postInput.val();
                if (!selected || selected.length === 0) {
                    let fallbackOption = $('#post option:not([value="all"])').first().val();
                    if (fallbackOption) {
                        $postInput.val([fallbackOption]).trigger('change');
                    }
                }
            });

            function generateTableRow(data, kelas) {
                return data.map(s => {
                    let row = {
                        "Kode": s.KodeAkun,
                        "Nama Post": s.NamaAkun,
                        "Nama Tagihan": s.bill_name,
                    };

                    kelas.forEach((it, i) => {
                        let id = it.id;
                        row[it.jenjang] = Number(s[id]);
                    });

                    return row;
                });
            }

            function parseDDMMYYYY(str) {
                if (!str) return null;

                const [dd, mm, yyyy] = str.split("-").map(Number);
                if (!dd || !mm || !yyyy) return null;

                return new Date(yyyy, mm, dd);
            }

            async function exportExcel(groupedData, params, kelas = []) {
                const rows = groupedData;
                if (!rows.length) return;

                const invalidValues = [null, '', 'undefined', 'all'];
                let kelasVal = params.get('filter[kelas]') ?? null;
                if (invalidValues.includes(kelasVal)) {
                  kelasVal = 'Semua';
                }

                let thnAkaVal = params.get('filter[angkatan]') ?? null;
                if (invalidValues.includes(thnAkaVal)) {
                  thnAkaVal = 'Semua';
                }

                const wbTitle = "REKAP TAGIHAN"
                const wb = new ExcelJS.Workbook();
                const ws = wb.addWorksheet(wbTitle);

                const header = Object.keys(rows[0]);

                ws.insertRow(1, [wbTitle]);
                ws.insertRow(2, ["Unit, Kelas", kelasVal.replace(/~/g, " - ")]);
                ws.insertRow(3, ["Tahun Akademik", thnAkaVal]);

                const boldRows = [1, 2, 3];

                boldRows.forEach(rowNumber => {
                    const row = ws.getRow(rowNumber);

                    row.eachCell({ includeEmpty: true }, cell => {
                        cell.font = { bold: true };
                    });

                    row.commit();
                });

                ws.insertRow(4, []);
                ws.insertRow(5, []);
                ws.insertRow(6, []);
                ws.insertRow(7, []);

                const headerRowNumber = 8;

                ws.getColumn(1).width = 35;
                ws.getColumn(2).width = 35;

                const headerRow = ws.insertRow(headerRowNumber, header);

                headerRow.eachCell({ includeEmpty: true }, (cell, colNumber) => {
                    cell.font = { bold: true };
                    cell.alignment = { horizontal: "center", vertical: "middle" };
                    cell.border = fullBorder();

                    if (colNumber <= 2) return;
                    const headerText = String(cell.value || "");
                    let width = Math.max(12, headerText.length + 4);
                    ws.getColumn(colNumber).width = width;
                    // console.log(width);
                });

                rows.forEach(r => {
                    const row = ws.addRow(Object.values(r));

                    row.eachCell({ includeEmpty: true }, cell => {
                        if (cell.value instanceof Date) {
                            if (typeof formatTanggalIndonesia === 'function') { cell.value = formatTanggalIndonesia(cell.value); } else { cell.numFmt = "dd-mm-yyyy"; }
                        }

                        if (typeof cell.value === "number") {
                            cell.numFmt = '"Rp "#,##0;\\("Rp "#,##0\\)';
                        }

                        cell.border = fullBorder();
                    });
                });

                const dataStartRow = headerRowNumber + 1;
                const dataEndRow = ws.lastRow.number;

                const totalRow = ws.addRow([]);
                totalRow.getCell(1).value = "TOTAL";
                totalRow.getCell(1).font = { bold: true };
                totalRow.getCell(1).border =  fullBorder();
                headerRow.eachCell((cell, colNumber) => {
                    const headerText = String(cell.value || "").toLowerCase();
                    const jenjangs = kelas.map(b => b.jenjang.toLowerCase());

                    const shouldSum = jenjangs.some(jenjang =>
                        headerText.includes(jenjang)
                    );

                    totalRow.getCell(colNumber).font = { bold: true };
                    totalRow.getCell(colNumber).border = fullBorder();

                    if (!shouldSum) return;

                    const colLetter = ws.getColumn(colNumber).letter;

                    totalRow.getCell(colNumber).value = {
                        formula: `SUM(${colLetter}${dataStartRow}:${colLetter}${dataEndRow})`
                    };

                    totalRow.getCell(colNumber).numFmt = '"Rp "#,##0;\\("Rp "#,##0\\)';
                });

                const buffer = await wb.xlsx.writeBuffer();
                const blob = new Blob([buffer], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                });

                const a = document.createElement("a");
                a.href = URL.createObjectURL(blob);
                a.download = wbTitle + " - " + kelasVal.replace(/~/g, " - ") + ".xlsx";
                a.click();
            }

            function fullBorder() {
              return {
                top: { style: "thin" },
                left: { style: "thin" },
                bottom: { style: "thin" },
                right: { style: "thin" }
              };
            }

            $(document).on('click', '.btn-print-rekap', async function (e) {
                loadingAlert(`Membuat Rekap ... <br> Proses ini membutuhkan waktu beberapa saat<br><hr>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan browser anda tidak memblokir <i>POP-UP</i>! </p>
                `);
                let data = $(`#${dtOptions.formId}`).serialize();
                let params;
                params = new URLSearchParams(data);

                if (params) {
                    let url = '{{route('admin.rekap-tagihan.get-data-rekap')}}';
                    const form = new FormData(document.getElementById('rekapForm'));
                    const params = new URLSearchParams();
                    for (const [key, value] of form.entries()) {
                        params.append(key, value);
                    }
                    const fullUrl = `${url}?${params.toString()}`;
                    const request = new Request(
                        fullUrl, {
                            method: "GET",
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': "application/json"
                            }
                        });

                    try {
                        const response = await fetch(request);

                        if (!response.ok) {
                            const status = response.status;
                            let message = `Request failed with status ${status}`;
                            let errors = null;

                            try {
                                const contentType = response.headers.get('content-type') || '';
                                if (contentType.includes('application/json')) {
                                    const errorData = await response.json();
                                    message = errorData.message || message;
                                    errors = errorData.errors || errorData.error || null;
                                } else {
                                    const text = await response.text();
                                    message = text || message;
                                }
                            } catch (_) {
                            }

                            const error = new Error(message);
                            error.status = status;
                            error.errors = errors;
                            throw error;
                        }

                        let result;
                        try {
                            result = await response.json();
                        } catch {
                            throw new Error('Response JSON tidak valid');
                        }

                        if (!result?.data || !result?.kelas) {
                            throw new Error('Data respon tidak lengkap');
                        }

                        const tableRow = generateTableRow(result.data, result.kelas);
                        await exportExcel(tableRow, params, result.kelas);
                        successAlert('Sukses, Rekap telah dicetak');
                    } catch (error) {
                        console.error(error);

                        if (error.status === 422) {
                            errorAlert(error.message);
                            if (error.errors) {
                                processErrors(error.errors);
                            }
                            return;
                        }

                        const errorMessages = {
                            401: '',
                            403: 'Anda tidak memiliki izin untuk mengakses halaman ini 😖',
                            404: 'Halaman yang dituju tidak ditemukan 🧐',
                            405: 'Metode tidak valid 🧐 <br>Silahkan muat ulang halaman dan coba lagi!',
                            419: '',
                            429: 'Terlalu banyak permintaan akses <br>Silahkan tunggu beberapa saat 🙏',
                        };

                        errorAlert(
                            errorMessages[error.status] ||
                            error.message ||
                            'Terjadi kesalahan, silahkan coba memuat ulang halaman'
                        );
                    }
                } else {
                    warningAlert('Silahkan pilih salah satu kelas terlebih dahulu!')
                }
            });
        });

    </script>

    {!! ($modalLink??'') !!}
@endsection
