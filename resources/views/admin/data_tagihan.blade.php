@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">
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
                        <strong>Pastikan browser anda tidak memblokir <i>POP-UP</i>!</strong>
                    </li>
                </ul>
            </div>
            <form id="filterForm">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="col mb-5">
                                <label class="form-label" for="filter[tahun_akademik]">
                                    Tahun Akademik
                                </label>
                                <select class="form-select" id="filter[tahun_akademik]"
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
                                <label class="form-label" for="dari-tanggal">Periode</label>
                                <input type="text" class="form-control form-control"
                                       placeholder="periode" id="filter[periode]"
                                       name="filter[periode]">
                            </div>
                            <div class="col mb-5">
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
                                <label class="form-label" for="filter[angkatan]">
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
                                                value="{{$item->CODE01}}"
                                                data-group="{{$item->DESC01}}">{{$item->DESC01}}</option>
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
                                                value="{{$item->unit}}~{{$item->jenjang}}~{{$item->kelas}}"
                                                data-group="{{$item->unit}}">
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
                                    Nis/Nama Siswa
                                </label>
                                <input class="form-control" id="filter[siswa]" name="filter[siswa]"
                                       placeholder="Masukkan NIS/NAMA Siswa" data-placeholder="Pilih siswa">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-flex justify-content-center flex-column flex-md-row justify-content-md-end gap-4">
                            <button type="button" class="btn btn-facebook btn-print-rekap" id="cetak-rekap">
                                <span class="ri-file-text-line me-2"></span>
                                Cetak Rekap
                            </button>
                            {{--                            <button type="button" class="btn btn-facebook" id="cetak-kartu-siswa">--}}
                            {{--                                <span class="ri-profile-line me-2"></span>--}}
                            {{--                                Cetak Kartu Siswa--}}
                            {{--                            </button>--}}
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
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('main/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
            // select: true,
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

            const periode = $('input[name="filter[periode]"]');
            periode.datepicker({
                format: "yyyymm",
                startView: "months",
                minViewMode: "months",
                autoclose: true
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
            $postInput.on('select2:select', function (e) {
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
                    console.log(fallbackOption)
                    if (fallbackOption) {
                        $postInput.val([fallbackOption]).trigger('change');
                    }
                }
            });

            function generateTableRow(data, tagihan) {
                return data.map(s => {
                    let row = {
                        "NIS": s.nocust,
                        "Nama": s.nmcust
                    };

                    let total = 0;
                    Object.keys(tagihan).forEach(function (key) {
                        row[tagihan[key]] = Number(s[tagihan[key]] ?? 0);
                        total += Number(s[tagihan[key]] ?? 0);
                    });
                    // row['TOTAL'] = Number(total);
                    return row;
                });
            }

            function parseDDMMYYYY(str) {
                if (!str) return null;

                const [dd, mm, yyyy] = str.split("-").map(Number);
                if (!dd || !mm || !yyyy) return null;

                return new Date(yyyy, mm, dd);
            }

            async function exportExcel(groupedData, params, tagihans = []) {
                const rows = groupedData;
                if (!rows.length) return;

                try {
                    const invalidValues = [null, '', 'undefined', 'all'];
                    let statusBayarVal = params.get('filter[status_bayar]') ?? null;
                    if (invalidValues.includes(statusBayarVal)) {
                        statusBayarVal = false;
                    }
                    let kelasVal = params.get('filter[kelas]') ?? null;
                    if (invalidValues.includes(kelasVal)) {
                        kelasVal = 'Semua';
                    }

                    let thnAkaVal = params.get('filter[tahun_akademik]') ?? null;
                    if (invalidValues.includes(thnAkaVal)) {
                        thnAkaVal = 'Semua';
                    }

                    let angkatanVal = params.get('filter[angkatan]') ?? null;
                    if (invalidValues.includes(angkatanVal)) {
                        angkatanVal = 'Semua';
                    }

                    let periodeVal = params.get('filter[periode]') ?? null;
                    if (invalidValues.includes(periodeVal)) {
                        periodeVal = 'Semua';
                    }

                    const wbTitle = "REKAP DATA TAGIHAN"
                    const wb = new ExcelJS.Workbook();
                    const ws = wb.addWorksheet(wbTitle);

                    const header = Object.keys(rows[0]);

                    ws.insertRow(1, [wbTitle]);
                    ws.insertRow(2, ["Unit, Kelas", kelasVal.replace(/~/g, " - ")]);
                    ws.insertRow(3, ["Angkatan", angkatanVal]);
                    ws.insertRow(4, ["Tahun Akademik", thnAkaVal]);
                    ws.insertRow(5, ["Periode", periodeVal]);

                    [5, 6].forEach(rowNumber => {
                        const cell = ws.getRow(rowNumber).getCell(2);

                        if (typeof formatTanggalIndonesia === 'function') { cell.value = formatTanggalIndonesia(cell.value); } else { cell.numFmt = "dd-mm-yyyy"; }
                    });

                    const boldRows = [1, 2, 3, 4, 5, 6];

                    boldRows.forEach(rowNumber => {
                        const row = ws.getRow(rowNumber);

                        row.eachCell({includeEmpty: true}, cell => {
                            cell.font = {bold: true};
                        });

                        row.commit();
                    });

                    ws.insertRow(7, []);

                    const headerRowNumber = 8;

                    ws.getColumn(1).width = 35;
                    ws.getColumn(2).width = 35;

                    const headerRow = ws.insertRow(headerRowNumber, header);

                    let totalColIndex = null;

                    headerRow.eachCell((cell, colNumber) => {
                        if (String(cell.value || "").toLowerCase().includes("total")) {
                            totalColIndex = colNumber;
                        }
                    });

                    if (!totalColIndex) {
                        totalColIndex = headerRow.cellCount + 1;

                        const totalCell = headerRow.getCell(totalColIndex);
                        totalCell.value = "TOTAL";
                        totalCell.font = { bold: true };
                        totalCell.alignment = { horizontal: "center" };
                        totalCell.border = fullBorder();

                        ws.getColumn(totalColIndex).width = 18;
                    }

                    headerRow.eachCell({includeEmpty: true}, (cell, colNumber) => {
                        cell.font = {bold: true};
                        cell.alignment = {horizontal: "center", vertical: "middle"};
                        cell.border = fullBorder();

                        if (colNumber <= 2) return;
                        const headerText = String(cell.value || "");
                        let width = Math.max(12, headerText.length + 4);
                        ws.getColumn(colNumber).width = width;
                    });

                    rows.forEach(r => {
                        const row = ws.addRow(Object.values(r));

                        row.eachCell({includeEmpty: true}, cell => {
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
                    totalRow.getCell(1).font = {bold: true};
                    totalRow.getCell(1).border = fullBorder();
                    headerRow.eachCell((cell, colNumber) => {
                        const headerText = String(cell.value || "").toLowerCase();
                        let shouldSum = Object.values(tagihans).some(tagihan =>
                            headerText.includes(tagihan.toLowerCase())
                        );
                        totalRow.getCell(colNumber).font = {bold: true};
                        totalRow.getCell(colNumber).border = fullBorder();
                        if (!shouldSum) return;
                        const colLetter = ws.getColumn(colNumber).letter;
                        totalRow.getCell(colNumber).value = {
                            formula: `SUM(${colLetter}${dataStartRow}:${colLetter}${dataEndRow})`
                        };
                        totalRow.getCell(colNumber).numFmt = '"Rp "#,##0;\\("Rp "#,##0\\)';
                    });

                    const sumColumns = [];

                    headerRow.eachCell((cell, colNumber) => {
                        const headerText = String(cell.value || "").toLowerCase();
                        if (headerText === "total") {
                            totalColIndex = colNumber;
                            return;
                        }
                        const shouldSum = Object.values(tagihans).some(tagihan =>
                            headerText.includes(tagihan.toLowerCase())
                        );
                        if (shouldSum) {
                            sumColumns.push(colNumber);
                        }
                    });

                    if (totalColIndex) {
                        for (let rowNumber = dataStartRow; rowNumber <= dataEndRow + 1; rowNumber++) {
                            const row = ws.getRow(rowNumber);
                            const sumRange = sumColumns.map(col =>
                                `${ws.getColumn(col).letter}${rowNumber}`
                            );
                            row.getCell(totalColIndex).value = {
                                formula: `SUM(${sumRange.join(',')})`
                            };
                            row.getCell(totalColIndex).numFmt = '"Rp "#,##0;\\("Rp "#,##0\\)';
                            row.getCell(totalColIndex).font = {bold: true};
                            row.getCell(totalColIndex).border = fullBorder();
                            // ws.getColumn(totalColIndex).width = Math.max(12, headerText.length + 4);
                        }
                    }

                    const buffer = await wb.xlsx.writeBuffer();
                    if (!buffer || buffer.byteLength === 0) {
                        throw new Error("Excel buffer empty");
                    }
                    const blob = new Blob([buffer], {
                        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                    });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = wbTitle + " - " + kelasVal.replace(/~/g, " - ") + ".xlsx";
                    document.body.appendChild(a);
                    a.click();
                    URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } catch (err) {
                    warningAlert('Gagal Mengexport Excel');
                    console.log(err);
                }
            }

            function fullBorder() {
                return {
                    top: {style: "thin"},
                    left: {style: "thin"},
                    bottom: {style: "thin"},
                    right: {style: "thin"}
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
                    let url = '{{route('admin.data-tagihan.get-data-rekap')}}';
                    const form = new FormData(document.getElementById('filterForm'));
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
                            const contentType = response.headers.get('content-type');
                            let message = `Request failed with status ${status}`;
                            if (contentType && contentType.includes('application/json')) {
                                const errorData = await response.json();
                                message = errorData.message || message;
                            } else {
                                const errorText = await response.text();
                                message = errorText || message;
                            }

                            const error = new Error(message);
                            error.status = status;
                            throw error;
                        }

                        const result = await response.json();

                        if (!result['data'] || result['data'].length === 0) {
                            console.log('kosong');
                            const error = new Error("Data Tagihan Kosong");
                            error.status = 422;
                            throw error;
                        }

                        let tableRow = generateTableRow(result.data, result.mstTagihan)
                        await exportExcel(tableRow, params, result.mstTagihan)

                        successAlert('Sukses, Rekap telah dicetak');
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
                } else {
                    warningAlert('Silahkan pilih salah satu kelas terlebih dahulu!')
                }
            });
        });


    </script>
@endsection
