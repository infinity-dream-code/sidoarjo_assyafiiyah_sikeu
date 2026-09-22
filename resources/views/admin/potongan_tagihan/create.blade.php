@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">
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
                <fieldset class="form-fieldset mb-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-5">
                                <label class="form-label" for="dari-tanggal">Tanggal Transaksi <span
                                        class="text-warning">*</span>(tanggal-bulan-tahun - tanggal-bulan-tahun)</label>
                                <input type="text" id="tanggal-transaksi" name="filter[tanggal-transaksi]"
                                       placeholder="tanggal/bulan/tahun"
                                       class="form-control" autocomplete="false" inputmode="numeric"/>
                            </div>
                            <div class="col mb-5">
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
                                <label class="form-label" for="filter[kelas]">
                                    Kelas
                                </label>
                                <select class="form-select" id="filter[kelas]" name="filter[kelas]"
                                        data-control="select2" data-placeholder="Pilih Kelas">
                                    <option value="all">Semua</option>
                                    @isset($kelas)
                                        @foreach($kelas as $item)
                                            <option
                                                value="{{$item->unit}}~{{$item->jenjang}}~{{$item->kelas}}">{{$item->unit}}
                                                - {{$item->jenjang}} {{$item->kelas}}</option>
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
                            <button type="button" class="btn btn-success" id="simpan-potongan">
                                <span class="ri-file-text-line me-2"></span>
                                Simpan Potongan Tagihan
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
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <fieldset class="form-fieldset">
                        <div class="card-datatable table-responsive text-nowrap">
                            <table class="table table-sm table-bordered table-hover"
                                   id="main_table">
                                <thead class="table-light">

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </fieldset>
                </div>
                <div class="col">
                    <form id="potongan-form">
                        <fieldset class="form-fieldset">
                            <label class="form-label">List Potongan Tagihan</label>
                            <table id="potongan-table" class="table table-bordered w-100">
                                <thead>
                                <tr>
                                    <th>Potongan</th>
                                    <th>Tanggal</th>
                                    <th>Detail</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="potongan-row">
                                    <td>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text">Rp. </span>
                                            <input type="text" name="potongan[]" placeholder="Potongan Tagihan"
                                                   class="potongan-input form-control formattedNumber"/>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="tanggal[]" placeholder="Tanggal"
                                               class="potongan-input form-control input-tanggal">
                                    </td>
                                    <td>
                                    <textarea type="text" name="deskripsi[]" placeholder="Deskripsi"
                                              class="potongan-input form-control"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('momentjs',true)
@section('datatable',true)
@section('datatable-buttons',true)
@section('datatable-select',true)
@section('datatable-row-grup',true)
@section('datatable-fixed-columns',true)
@section('select2',true)
@section('script')
    <script src="{{asset('main/libs/moment/moment.js')}}"></script>
    <script src="{{asset('main/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>
    <script src="{{asset('js/helper/formattedNumber.min.js')}}"></script>
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let today = moment();

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
            select: 'multi',
        };

        function handleAutoAppend(e, tableId, inputClass) {
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll("tbody tr");
            const lastRow = rows[rows.length - 1];
            const input = lastRow.querySelector(`.${inputClass}`);

            if (e.target === input && input.value.trim() !== "") {
                const newRow = lastRow.cloneNode(true);
                newRow.querySelectorAll("input").forEach(i => i.value = "");
                // const newIndex = rows.length + 1;
                // newRow.querySelector(".row-number").textContent = newIndex;
                table.querySelector("tbody").appendChild(newRow);
                $(newRow).find('.input-tanggal').datepicker({
                    format: "dd-mm-yyyy",
                    autoclose: true,
                    setDate: today
                });
            }
        }

        function resetDynamicTable(tableId) {
            const tableBody = document.querySelector(`#${tableId} tbody`);
            if (!tableBody) return;
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                if (index !== 0) {
                    row.remove();
                }
            });
            const firstRow = tableBody.querySelector('tr');
            firstRow.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
        }

        function handleAutoRemove(e, tableId, inputClass) {
            if (!e) return;
            if (!e.target.classList) return;
            if (!e.target.classList.contains(inputClass)) return;

            const table = document.getElementById(tableId);
            const row = e.target.closest("tr");
            const inputVal = e.target.value.trim();
            const rows = table.querySelectorAll("tbody tr");
            let emptyRows = Array.from(rows).filter(r => {
                const input = r.querySelector(`.${inputClass}`);
                return input && input.value.trim() === "";
            });
            if (inputVal === "" && rows.length > 1 && emptyRows.length > 1) {
                row.remove();
                // updateRowNumbers(tableId);
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

            let startOfMonth = moment().startOf('month');
            let date = $('#tanggal-transaksi');
            date.daterangepicker({
                startDate: startOfMonth,
                endDate: today,
                autoUpdateInput: false,
                todayHighlight: true,
                autoclose: true,
                locale: {
                    format: 'DD-MM-YYYY',
                    separator: " - ",
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                    fromLabel: "Dari",
                    toLabel: "Ke",
                    customRangeLabel: "Kustom",
                    daysOfWeek: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                    monthNames: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
                    firstDay: 0,
                },
                maxDate: moment()
            }, function (start, end) {
                let duration = end.diff(start, 'days');
                if (duration > 365) {
                    warningAlert("Maksimal 365 hari.");
                    date.data('daterangepicker').setStartDate(start);
                    date.data('daterangepicker').setEndDate(start.clone().add(6, 'days'));
                }
            });

            date.on('apply.daterangepicker hide.daterangepicker', function (ev, picker) {
                if (picker.startDate && picker.endDate) {
                    $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                }
            });

            date.on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                // picker.setStartDate(moment().startOf('month'));
                // picker.setEndDate(moment());
            });

            const inputTanggal = $('input[name="tanggal[]"]');

            inputTanggal.datepicker({
                format: "dd-mm-yyyy",
                autoclose: true,
                setDate: today
            });

            const inputTables = [
                {id: 'potongan-table', inputClass: 'potongan-input'},
            ];

            document.addEventListener("input", function (e) {
                inputTables.forEach(({id, inputClass}) => handleAutoAppend(e, id, inputClass));
            });

            document.addEventListener("blur", function (e) {
                inputTables.forEach(({id, inputClass}) => handleAutoRemove(e, id, inputClass));
            }, true);

            document.getElementById('simpan-potongan').addEventListener('click', async function (e) {
                e.preventDefault();
                loadingAlert('menyimpan data potongan');
                let data = DT[`${dtOptions.tableId}`].rows({selected: true}).data();

                if (!data[0] || !data[0]['AA'] || data[0]['AA'].length === 0) {
                    warningAlert('silahkan pilih tagihan yang akan dipotong!')
                    return;
                }

                let potongan = new FormData(document.getElementById('potongan-form'));
                const potonganValues = potongan.getAll('potongan[]');
                const tanggalValues = potongan.getAll('tanggal[]');
                const valid = potonganValues.some(val => val.trim() !== '');
                const validTanggal = tanggalValues.some(val => val.trim() !== '');

                if (!valid) {
                    e.preventDefault();
                    warningAlert('Silahkan isi minimal satu potongan tagihan!');
                    return;
                }

                if (!validTanggal) {
                    e.preventDefault();
                    warningAlert('Silahkan isi tanggal potongan tagihan!');
                    return;
                }

                const formData = new FormData();
                data = data.pluck('AA')
                    .toArray();

                data.forEach(id => {
                    formData.append('item_id[]', id);
                });

                for (const [key, value] of potongan.entries()) {
                    formData.append(key, value);
                }

                const request = new Request(
                    '{{route('admin.potongan-tagihan.store')}}', {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': "application/json"
                        },
                        body: formData
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
                    DT[`${dtOptions.tableId}`].rows().deselect();
                    resetDynamicTable('potongan-table');
                    successAlert(result['message']);
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

            })
        });


    </script>
@endsection
