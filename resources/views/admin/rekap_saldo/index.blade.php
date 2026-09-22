@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
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
                        <strong>Pastikan telah mengisi <a href="#filter[periode]">PERIODE</a>!</strong>
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
                                <label class="form-label required" for="dari-tanggal">Periode</label>
                                <input type="text" class="form-control form-control"
                                       placeholder="periode" id="filter[periode]"
                                       name="filter[periode]">
                            </div>
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
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>

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
                if (!selectedGroup) return;
                const $kelasSelect = $("[name='filter[kelas]']");
                if ($(this).val() === 'all') {
                    $kelasSelect.find('option').each(function () {
                        $(this).prop('disabled', false);
                    });
                } else {
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
                    if (fallbackOption) {
                        $postInput.val([fallbackOption]).trigger('change');
                    }
                }
            });

            let startOfMonth = moment().startOf('month');
            let today = moment();

            const periode = $('input[name="filter[periode]"]');
            periode.datepicker({
                format: "yyyymm",
                startView: "months",
                minViewMode: "months",
                autoclose: true
            });

            function generateTableRekap(data) {
                console.log(data);
                return data.map((s, index) => {
                    return {
                        "No": `${index + 1}`,
                        "NIS": s.nocust,
                        "Nama": s.nmcust,
                        "Saldo Awal": s.opening_balance,
                        "Saldo Periode Dipilih": s.current_net,
                        "Saldo Akhir": s.closing_balance,
                    };
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
                let periodeVal = params.get('filter[periode]') ?? null;
                if (invalidValues.includes(periodeVal)) {
                    periodeVal = false;
                }
                let kelasVal = params.get('filter[kelas]') ?? null;
                if (invalidValues.includes(kelasVal)) {
                    kelasVal = 'Semua';
                }

                let thnAkaVal = params.get('filter[angkatan]') ?? null;
                if (invalidValues.includes(thnAkaVal)) {
                    thnAkaVal = 'Semua';
                }

                const wbTitle = "REKAP SALDO"
                const wb = new ExcelJS.Workbook();
                const ws = wb.addWorksheet(wbTitle);

                const header = Object.keys(rows[0]);

                ws.insertRow(1, [wbTitle]);
                ws.insertRow(2, ["Unit, Kelas", kelasVal.replace(/~/g, " - ")]);
                ws.insertRow(3, ["Tahun Angkatan", thnAkaVal]);
                ws.insertRow(4, ["Periode", periodeVal]);

                const boldRows = [1, 2, 3, 4];

                boldRows.forEach(rowNumber => {
                    const row = ws.getRow(rowNumber);

                    row.eachCell({includeEmpty: true}, cell => {
                        cell.font = {bold: true};
                    });

                    row.commit();
                });

                ws.insertRow(7, []);

                const headerRowNumber = 8;

                const headerRow = ws.insertRow(headerRowNumber, header);

                headerRow.eachCell({includeEmpty: true}, (cell, colNumber) => {
                    cell.font = {bold: true};
                    cell.alignment = {horizontal: "center", vertical: "middle"};
                    cell.border = fullBorder();
                    cell.fill = cellBGColor();

                    if (colNumber !== 1) {
                        ws.getColumn(colNumber).width = 30;
                    }else{
                        const headerText = String(cell.value || "");
                        let width = Math.max(12, headerText.length + 4);
                        ws.getColumn(colNumber).width = width;
                    }
                });

                rows.forEach(r => {
                    const row = ws.addRow(Object.values(r));

                    row.eachCell({includeEmpty: true}, cell => {
                        if (cell.value instanceof Date) {
                            cell.numFmt = "dddd, dd mmmm yyyy";
                        }

                        if (typeof cell.value === "number") {
                            cell.numFmt = '"Rp "#,##0;[Red]-"Rp "#,##0';
                        }

                        cell.border = fullBorder();
                    });
                });

                const dataStartRow = headerRowNumber + 1;
                const dataEndRow = ws.lastRow.number;

                const totalRow = ws.addRow([]);
                ws.mergeCells(totalRow.number, 1, totalRow.number, 3);

                const cell = totalRow.getCell(1);
                cell.value = "TOTAL";
                cell.font = { bold: true };
                cell.alignment = { horizontal: 'center' };
                cell.fill = cellBGColor();
                headerRow.eachCell((cell, colNumber) => {
                    const headerText = String(cell.value || "").toLowerCase();
                    totalRow.getCell(colNumber).font = {bold: true};
                    totalRow.getCell(colNumber).border = fullBorder();

                    if (['no', 'nis', 'nama'].includes(headerText)) {
                        return;
                    }

                    const colLetter = ws.getColumn(colNumber).letter;

                    totalRow.getCell(colNumber).value = {
                        formula: `SUM(${colLetter}${dataStartRow}:${colLetter}${dataEndRow})`
                    };

                    totalRow.getCell(colNumber).numFmt = '"Rp "#,##0;\\("Rp "#,##0\\)';
                    totalRow.getCell(colNumber).fill = cellBGColor();
                });

                const buffer = await wb.xlsx.writeBuffer();
                const blob = new Blob([buffer], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                });

                const a = document.createElement("a");
                a.href = URL.createObjectURL(blob);
                a.download = wbTitle + " - " + kelasVal.replace(/~/g, " - ") + ".xlsx";
                a.click();

                return true;
            }

            function fullBorder() {
                return {
                    top: {style: "thin"},
                    left: {style: "thin"},
                    bottom: {style: "thin"},
                    right: {style: "thin"}
                };
            }

            function cellBGColor() {
                return {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {argb: 'FFEBE1FF'}
                }
            }

            $(document).on('click', '.btn-print-rekap', async function (e) {
                // warningAlert("Sedang dalam perbaikan");
                loadingAlert(`Membuat Rekap ... <br> Proses ini membutuhkan waktu beberapa saat<br><hr>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan browser anda tidak memblokir <i>POP-UP</i>! </p>
                `);
                let data = $(`#${dtOptions.formId}`).serialize();
                let params;
                params = new URLSearchParams(data);

                if (params) {
                    let url = '{{route('admin.rekap-saldo.get-data-rekap')}}';
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
                        let tableRow = generateTableRekap(result.data);
                        let file = await exportExcel(tableRow, params);

                        if (file) {
                            successAlert('Sukses, Rekap telah dicetak');
                        }else{
                            const error = new Error('gagal mencetak');
                            error.status = 422;
                            throw error;
                        }
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

    {!! ($modalLink??'') !!}
@endsection
