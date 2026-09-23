@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}">
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

    <form class="mainForm" id="bayar-form" action="#">
        <div class="card mb-6">
            <meta name="csrf-token" content="{{ csrf_token() }}" xmlns="http://www.w3.org/1999/html">

            <div class="card-header header-elements">
                <h5 class="mb-0 me-2">{{$mainTitle}}</h5>
            </div>
            <div class="card-body py-0">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-5">
                            <label class="required form-label" for="siswa">
                                Siswa
                            </label>
                            <select class="form-select" id="siswa" name="siswa"
                                    data-control="select2-ajax-siswa" data-placeholder="Masukkan NIS / Nama Siswa">
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-5">
                            <label class="form-label" for="tahun_pelajaran">
                                Tahun Pelajaran
                            </label>
                            <select class="form-select" id="tahun_pelajaran"
                                    name="filter[tahun_pelajaran]"
                                    data-control="select2"
                                    data-placeholder="Pilih Tahun Pelajaran">
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
                    <div class="col-12">
                        <div class="mb-5">
                            <label class="required form-label" for="saldo">
                                Saldo
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">Rp. </span>
                                <input readonly type="text" id="saldo" name="saldo"
                                       placeholder="Saldo"
                                       class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-5">
                            <label class="form-label" for="tanggal">Tanggal Bayar</label>
                            <input type="text" id="tanggal" name="tanggal" placeholder="tanggal/bulan/tahun"
                                   class="form-control"/>
                        </div>
                        <div class="mb-5">
                            <label class="form-label" for="bank">Bank</label>
                            <select type="text" id="bank" name="bank" class="form-select"
                                    data-control="select2" data-placeholder="Bank">
                                <option value="1140000">Manual Cash</option>
                                <option value="1140001">Manual BMI</option>
                                <option value="1140002">Manual Saldo</option>
                                <option value="1140003">Transfer Bank Lain</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-5">
                            <label class="form-label" for="total_tagihan">Total Tagihan</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">Rp. </span>
                                <input readonly type="text" id="total_tagihan" name="total_tagihan"
                                       placeholder="Total Tagihan"
                                       class="form-control formattedNumber"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-0 pt-0">
                <div class="d-flex">
                    <div class="ms-auto gap-6">
                        <button type="reset" class="btn btn-primary d-none">
                            <span class="ri-search-line me-2"></span>
                            Reset
                        </button>
                        <button type="button" class="btn btn-primary cari-tagihan ">
                            <span class="ri-search-line me-2"></span>
                            Cari
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-sm table-bordered table-hover"
                       id="main_table_2">
                    <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>NIS</th>
                        <th>NO. DAFTAR</th>
                        <th>Kelas</th>
                        <th>NO. VA</th>
                        <th>NAMA</th>
                        <th>Nama Tagihan</th>
                        <th>Periode</th>
                        <th>Tagihan</th>
                        <th>Nominal Bayar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-center" colspan="12">Silahkan Pilih Siswa</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-0">
                <div class="w-100">
                    <div class="row">
                        <div class="d-flex justify-content-center justify-content-md-end gap-4">
                            <button type="button" class="btn btn-danger cetak-tagihan">
                                <span class="ri-file-pdf-2-line me-2"></span>
                                Pratinjau
                            </button>
                            <button type="submit" class="btn btn-success btn-bayar">
                                <span class="ri-cash-line me-2"></span>
                                Bayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>
    <script src="{{asset('main/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('js/helper/formattedNumber.min.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>

    <script type="module">
        import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs';

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.mjs';
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"
            integrity="sha512-axXaF5grZBaYl7qiM6OMHgsgVXdSLxqq0w7F4CQxuFyrcPmn0JfnqsOtYHUun80g6mRRdvJDrTCyL8LQqBOt/Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/vfs_fonts.min.js"
            integrity="sha512-EFlschXPq/G5zunGPRSYqazR1CMKj0cQc8v6eMrQwybxgIbhsfoO5NAMQX3xFDQIbFlViv53o7Hy+yCWw6iZxA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        let dataColumns = [];
        let formId = '';
        let formClass = $('.mainForm');
        let tableId = 'main_table';
        const select2 = $(`[data-control='select2']`);
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let maxBayar = 0;
        let select2Param = '';

        let dtOptions = {
            tableId: 'main_table_2',
            formId: 'bayar-form',
            columnUrl: '{{($columnsUrl??null)}}',
            dataUrl: '{{($datasUrl??null)}}',
            dataColumns: [],
            thead: true,
            tfoot: true,
            paging: false,
            searching: false,
            fixedHeader: false,
            pageLength: 5,
            lengthMenu: [5, 25, 50, 75, 100],
            info: false,
            scrollX: false,
            serverSide: false,
            select: 'multi',
            scrollY: false,
            retrieve: true
        };

        document.addEventListener("DOMContentLoaded", function () {

            let currentDate = new Date();
            let day = currentDate.getDate().toString().padStart(2, '0');
            let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
            let year = currentDate.getFullYear();
            let formattedDate = day + '-' + month + '-' + year;

            function clearErrorMessages(formId) {
                const form = document.querySelector(`#${formId}`);
                const errorElements = form.querySelectorAll('.invalid-feedback');
                const errorClass = form.querySelectorAll('.is-invalid');

                errorElements.forEach(element => element.textContent = '');
                errorClass.forEach(element => element.classList.remove('is-invalid'));
            }

            if (select2.length) {
                select2.each(function () {
                    let $this = $(this);
                    // select2Focus($this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Pilih',
                        dropdownParent: $this.parent(),
                        language: {
                            noResults: function () {
                                return "Tidak ditemukan data yang sesuai!";
                            }
                        }
                    });
                });
            }

            formClass.on('submit', function (e) {
                e.preventDefault()
                loadingAlert();
                let url = '{{route('admin.keuangan.manual-pembayaran.store')}}';
                let tipe = 'POST';
                const formId = $(this).attr('id');
                let data = $(this).serialize();

                // console.log(url);
                let ajaxOptions = {
                    url: url,
                    type: tipe,
                    data: data,
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }

                // console.log(ajaxOptions)
                try {
                    const table = $(`#${dtOptions.tableId}`).DataTable();

                    const selectedIndexes = table.rows({selected: true}).indexes().toArray();
                    let Siswa = $('#siswa').val();
                    if (!Siswa) {
                        warningAlert('Silahkan pilih siswa');
                        return;
                    }
                    const checkedTagihan = $(`#${dtOptions.tableId} input[name="tagihan[post][]"]:checked`).length;
                    if (selectedIndexes.length < 1 && checkedTagihan < 1) {
                        warningAlert('Silahkan pilih tagihan yang akan dibayar');
                        return;
                    }
                    clearErrorMessages(formId)
                    $.ajax(ajaxOptions).done(function (responses) {
                        const thisForm = document.getElementById(formId);
                        thisForm.reset();
                        $('[data-control="select2-ajax-siswa"]').empty().trigger('change');
                        select2.each(function () {
                            $(this).trigger('change');
                        })
                        $("#tanggal").datepicker('update', formattedDate);
                        $(`#${dtOptions.tableId}`).DataTable().clear().draw();
                        AlertPrint(responses.message);
                    }).fail(function (xhr) {
                        if (xhr.status === 422) {
                            const errMessage = xhr.responseJSON.message
                            errorAlert(errMessage)
                            const errors = JSON.parse(xhr.responseText).error
                            if (errors) {
                                processErros(errors)
                            }
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
                } catch (e) {
                    errorAlert('terjadi error pada halaman, silahkan muat ulang');
                }
            })

            $('.cari-tagihan').on('click', function (e) {
                let Siswa = $('#siswa').val();
                if (Siswa) {
                    getSaldoSiswa('tarik-siswa', Siswa);
                    dataReload(dtOptions.tableId);
                    $('#total_tagihan').val('');
                } else {
                    warningAlert('Silahkan Pilih Siswa!');
                }
            });

            $("#tanggal").datepicker({
                format: "dd-mm-yyyy",
                autoclose: true,
            }).datepicker('update', formattedDate);


            $(document).on('click', '.cetak-tagihan', function (e) {
                printTagihan()
            });

            $(document).on('click', '.test-tagihan', function (e) {
                AlertPrint()
            });

            $('[data-control="select2-ajax-siswa"]').select2({
                allowClear: true,
                placeholder: $(this).data('placeholder'),
                ajax: {
                    url: '{{ route('admin.master-data.data-siswa.get-siswa-select2') }}',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        select2Param = params.term;
                        return {
                            term: params.term,
                            nis: true
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }, language: {
                    inputTooShort: function () {
                        return "Masukkan NIS atau Nama Siswa";
                    }, noResults: function () {
                        let w = $.isNumeric(select2Param) ? 'NIS' : 'Nama';
                        return "Siswa dengan " + w + ": <span class='bg-label-danger'><b>" + select2Param + "</b></span> tidak ditemukan!";
                    }, searching: function () {
                        return "Mencari Siswa ......"
                    }
                }, escapeMarkup: function (markup) {
                    return markup;
                }, minimumInputLength: 4,
            }).on('select2:selecting', function (e) {
                if (e.params.args.data.id === '') {
                    e.preventDefault();
                }
            });

            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
            }

            const getNominalInput = (rowNode) => $(rowNode).find('input.nominal-bayar-input');

            const parseNominal = (value) => {
                if (!value) return 0;
                return parseInt(String(value).replace(/\./g, ''), 10) || 0;
            };

            const formatNominal = (value) => {
                const amount = parseNominal(value);
                return amount > 0 ? amount.toLocaleString('id-ID') : '';
            };

            const configureNominalInput = (input, rowData, fillDefault = false) => {
                const sisaBayar = Number(rowData.sisa_bayar ?? rowData.BILLAM) || 0;
                const canCicil = Number(rowData.can_cicil ?? 0) === 1;

                input.prop('disabled', false).prop('readonly', false).attr('required', true);
                input.removeAttr('max min');
                input.attr('data-sisa-bayar', sisaBayar);
                input.attr('title', canCicil
                    ? 'Tagihan ini dapat dicicil'
                    : 'Tagihan ini tidak dapat dicicil, pembayaran harus lunas');

                const current = parseNominal(input.val());
                if (fillDefault && current <= 0 && sisaBayar > 0) {
                    input.val(formatNominal(sisaBayar));
                }
            };

            const updateTotalTagihan = () => {
                const table = $(`#${dtOptions.tableId}`).DataTable();
                let totalTagihan = 0;

                table.rows().every(function () {
                    const rowNode = this.node();
                    const checkbox = $(rowNode).find('input[name="tagihan[post][]"]');
                    const input = getNominalInput(rowNode);
                    if (checkbox.prop('checked') && input.length) {
                        totalTagihan += parseNominal(input.val());
                    }
                });

                $('input[name=total_tagihan]').val(totalTagihan ? totalTagihan.toLocaleString('id-ID') : '');
            };

            const isRowSelected = (rowNode, checkbox) => {
                return checkbox.prop('checked') || $(rowNode).hasClass('selected');
            };

            const syncNominalBayarInputs = () => {
                const table = $(`#${dtOptions.tableId}`).DataTable();

                table.rows().every(function () {
                    const rowNode = this.node();
                    const rowData = this.data();
                    const checkbox = $(rowNode).find('input[name="tagihan[post][]"]');
                    const input = getNominalInput(rowNode);
                    const isSelected = isRowSelected(rowNode, checkbox);

                    if (!input.length) {
                        return;
                    }

                    if (isSelected) {
                        if (!checkbox.prop('checked')) {
                            checkbox.prop('checked', true);
                        }
                        configureNominalInput(input, rowData, true);
                    } else {
                        checkbox.prop('checked', false);
                        input.prop('disabled', true).val('').removeAttr('required');
                    }
                });

                updateTotalTagihan();
            };

            const activateNominalRow = (inputEl, fillDefault = false) => {
                const rowNode = $(inputEl).closest('tr');
                const checkbox = rowNode.find('input[name="tagihan[post][]"]');
                const table = $(`#${dtOptions.tableId}`).DataTable();
                const rowData = table.row(rowNode).data();

                checkbox.prop('checked', true);
                if (!rowNode.hasClass('selected')) {
                    table.row(rowNode).select();
                }

                configureNominalInput($(inputEl), rowData, fillDefault);
                updateTotalTagihan();
            };

            $(`#${dtOptions.tableId}`)
                .on('init.dt draw.dt', syncNominalBayarInputs)
                .on('mousedown click', 'td.exclude-selection, input.nominal-bayar-input', function (e) {
                    e.stopPropagation();
                    const input = $(this).is('input.nominal-bayar-input')
                        ? $(this)
                        : $(this).find('input.nominal-bayar-input');
                    if (input.length) {
                        activateNominalRow(input[0], true);
                        setTimeout(() => input.trigger('focus'), 0);
                    }
                })
                .on('focus', 'input.nominal-bayar-input', function () {
                    activateNominalRow(this, false);
                })
                .on('change', 'input[name="tagihan[post][]"]', function () {
                    const table = $(`#${dtOptions.tableId}`).DataTable();
                    const row = table.row($(this).closest('tr'));
                    if (this.checked) {
                        row.select();
                    } else {
                        row.deselect();
                    }
                    syncNominalBayarInputs();
                })
                .on('select.dt', function (e, dt, type, indexes) {
                    if (type !== 'row') return;
                    indexes.forEach(function (idx) {
                        $(dt.row(idx).node()).find('input[name="tagihan[post][]"]').prop('checked', true);
                    });
                    syncNominalBayarInputs();
                })
                .on('deselect.dt', function (e, dt, type, indexes) {
                    if (type !== 'row') return;
                    indexes.forEach(function (idx) {
                        $(dt.row(idx).node()).find('input[name="tagihan[post][]"]').prop('checked', false);
                    });
                    syncNominalBayarInputs();
                })
                .on('input', 'input.nominal-bayar-input', function () {
                    const sisaBayar = Number($(this).attr('data-sisa-bayar')) || 0;
                    let amount = parseNominal($(this).val());
                    if (sisaBayar > 0 && amount > sisaBayar) {
                        amount = sisaBayar;
                        $(this).val(formatNominal(amount));
                    }
                    updateTotalTagihan();
                })
                .on('blur', 'input.nominal-bayar-input', function () {
                    const amount = parseNominal($(this).val());
                    $(this).val(amount > 0 ? formatNominal(amount) : '');
                    updateTotalTagihan();
                });

            function AlertPrint(Message = null) {
                Message = Message ?? 'Tagihan sukses dibayar, apakah anda ingin mencetak tagihan?';
                Swal.fire({
                    html: Message,
                    icon: "success",
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: 'Cetak Bukti Bayar',
                    cancelButtonText: 'Tutup',
                    customClass: {
                        confirmButton: "btn btn-outline-success",
                        cancelButton: "btn btn-outline-secondary"
                    },
                }).then(function (result) {
                    if (result.value) {
                        printPaidTagihan();
                    }
                });
            }

            async function printPaidTagihan() {
                loadingAlert('Membuat Kartu Siswa');
                let url = '{{route('admin.keuangan.manual-pembayaran.cetak-tagihan-dibayar')}}';

                const request = new Request(
                    url, {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                try {
                    const response = await fetch(request);

                    if (!response.ok) {
                        throw await buildHttpError(response);
                    }

                    const result = await response.json();

                    if (!result?.tagihans?.length) {
                        throw createError("Data Tagihan Kosong", 422);
                    }
                    const data = await generateKuitansi(result);
                    const pdf = await generatePdf('KUITANSI', data.data, data.unit ?? false)
                    // if (!result['tagihans'] || result['tagihans'].length === 0) {
                    //     console.log('kosong');
                    //     const error = new Error("Data Tagihan Kosong");
                    //     error.status = 422;
                    //     throw error;
                    // }

                    if (pdf) {
                        successAlert('Sukses, Rekap telah dicetak');
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
            }

            async function printTagihan() {
                loadingAlert();
                const formId = $('#bayar-form');
                let data = $(formId).serialize();
                const table = $(`#${dtOptions.tableId}`).DataTable();
                const selectedIndexes = table.rows({selected: true}).indexes().toArray();
                let Siswa = $('#siswa').val();
                if (!Siswa) {
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }
                    warningAlert('Silahkan pilih siswa');
                    return;
                }
                if (selectedIndexes.length < 1) {
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }
                    warningAlert('Silahkan pilih tagihan yang akan dicetak');
                    return;
                }
                const baseUrl = '{{ route('admin.keuangan.manual-pembayaran.cetak-tagihan') }}';
                const url = baseUrl + (data ? '?' + data : '');
                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/pdf',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }
                    if (!response.ok) {
                        let msg = 'Terjadi kesalahan, silahkan coba memuat ulang halaman';
                        const ct = response.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            try {
                                const j = await response.json();
                                msg = j.message || msg;
                            } catch (e) {
                            }
                        }
                        if (response.status === 419) {
                            msg = null; if (typeof window.pingKeepAlive === 'function') { window.pingKeepAlive(); return; };
                        } else if (response.status === 403) {
                            msg = 'Anda tidak memiliki izin untuk mengakses halaman ini';
                        } else if (response.status === 404) {
                            msg = 'Halaman tidak ditemukan';
                        } else if (response.status === 500) {
                            msg = 'Tidak dapat terhubung ke server, Silahkan periksa koneksi internet anda';
                        }
                        errorAlert(msg);
                        return;
                    }
                    const blob = await response.blob();
                    const previewUrl = URL.createObjectURL(blob);
                    window.open(previewUrl, '_blank');
                    successAlert('File tagihan terbuka pada tab baru');
                } catch (e) {
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }
                    errorAlert('Terjadi kesalahan, silahkan coba memuat ulang halaman');
                }
            }

            function getSaldoSiswa(target, siswa) {
                loadingAlert();
                let url = '{{route('admin.keuangan.saldo.saldo-virtual-account.get-saldo')}}';
                let ajaxOptions = {
                    url: url,
                    type: 'get',
                    datatype: 'json',
                    data: {
                        'siswa': siswa,
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                }
                $.ajax(ajaxOptions).done(function (response) {
                    const raw = typeof response === 'object' && response !== null
                        ? (response.saldo ?? 0)
                        : response;
                    let saldo = parseInt(String(raw).replace(/\./g, ''), 10) || 0;
                    saldo = saldo.toLocaleString('id-ID');

                    $('#saldo').val(saldo);
                    Swal.close();
                }).fail(function (xhr) {
                    if (xhr.status === 422) {
                        errorAlert('Data tidak ditemukan')
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

            pdfMake.fonts = {
                Times: {
                    normal: 'https://cdn.jsdelivr.net/npm/@canvas-fonts/times-new-roman@1.0.4/Times New Roman.ttf',
                    bold: 'https://cdn.jsdelivr.net/npm/@canvas-fonts/times-new-roman-bold@1.0.4/Times New Roman Bold.ttf',
                    italics: 'https://cdn.jsdelivr.net/npm/@canvas-fonts/times-new-roman-italic@1.0.4/Times New Roman Italic.ttf',
                    bolditalics: 'https://cdn.jsdelivr.net/npm/@canvas-fonts/times-new-roman-bold@1.0.4/Times New Roman Bold.ttf'
                }, Roboto: {
                    normal: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Regular.ttf',
                    bold: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Medium.ttf',
                    italics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Italic.ttf',
                    bolditalics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-MediumItalic.ttf'
                },
            };

            const instansi = {
                nama_instansi: "{{ config('app.nama_instansi') }}",
                nama_sub_1: "{{ config('app.nama_sub_instansi_1') }}",
                nama_sub_2: "{{ config('app.nama_sub_instansi_2') }}",
                akreditasi: "{{ config('app.akreditasi') }}",
                alamat: "{{ config('app.alamat') }}",
                kontak: {
                    telepon: "{{ config('app.telepon') }}",
                    email: "{{ config('app.email') }}",
                    website: "{{ config('app.website') }}"
                }
            };
            const headerLogo = "{{ base64_encode(file_get_contents(public_path(config('app.logo')))) }}";
            const tandaTangan = @json($tanda_tangan);
            const userName = "{{ Auth::user()->name }}";
            const domisili = "{{ config('app.domisili') }}";
            const tanggalSekarang = "{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}";
            const APP_VA_PREFIX = @json((string) (config('app.nova') ?: '751095'));
            const showVA = (nis) => typeof formatNoVA === 'function'
                ? formatNoVA(nis, APP_VA_PREFIX)
                : (() => {
                    const digits = String(nis ?? '').replace(/\D/g, '');
                    if (!digits) return '';
                    return APP_VA_PREFIX + digits.padStart(16 - APP_VA_PREFIX.length, '0');
                })();

            function getContentWidth(pageSize = 'A4', orientation = 'portrait', margins = [30, 30, 30, 30]) {
                const sizes = {
                    A4: [595.28, 841.89],
                    A3: [841.89, 1190.55],
                    LETTER: [612, 792],
                    LEGAL: [612, 1008]
                };
                const key = String(pageSize).toUpperCase();
                const size = sizes[key] || sizes.A4;

                // swap width/height for landscape
                const pageW = orientation === 'landscape' ? size[1] : size[0];
                const [ml, , mr] = margins;
                return pageW - ml - mr;
            }

            async function getLogoUnit(unit = false) {
                const fallbackLogo = 'data:image/jpeg;base64,' + "{{ base64_encode(file_get_contents(public_path(config('app.logo')))) }}";
                try {
                    if (!unit) {
                        throw 'error';
                    }
                    const cacheKey = `logo_unit_${unit}`;
                    const cachedLogo = localStorage.getItem(cacheKey);
                    if (cachedLogo) {
                        return cachedLogo;
                    }
                    const params = new URLSearchParams();
                    params.append('unit', unit);
                    const request = new Request(
                        `{{ route('admin.master-data.get-logo') }}?${params.toString()}`,
                        {
                            method: "GET",
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        }
                    );
                    const response = await fetch(request);
                    if (!response.ok) {
                        throw 'error';
                    }
                    const result = await response.json();
                    if (!result.data) {
                        throw 'error';
                    }
                    localStorage.setItem(cacheKey, result.data);
                    return result.data;
                } catch {
                    return fallbackLogo;
                }
            }

            function formatMetodePembayaran(data) {
                const descriptions = {
                    '1140000': 'Manual Cash',
                    '1140001': 'Manual BMI',
                    '1140002': 'Manual SALDO',
                    '1140003': 'Transfer Bank Lain',
                    '1140004': 'INFAQ',
                    '1140005': 'Transfer Bank BRI',
                    '1200001': 'Loket Manual - Beasiswa',
                    '1200002': 'Loket Manual - Potongan',
                    '1': 'H2H VA BMI - ATM',
                    '2': 'H2H VA BMI - Teller',
                    '3': 'H2H VA BMI - IBANK',
                    '4': 'H2H VA BMI - EDC',
                    '5': 'H2H VA BMI - MOBILE',
                    '6': 'ANDROID',
                    null: 'Nomor VA',
                    '': 'Nomor VA'
                };
                return descriptions[data] || data;
            }

            async function generatePdf(title, bodyContent, unit_logo = false) {
                try {
                    let logo = 'data:image/jpeg;base64,' + headerLogo;

                    if (unit_logo) {
                        logo = await getLogoUnit(unit_logo);
                    }

                    const orientation = 'portrait';
                    const pageMargins = [20, 20, 20, 20];
                    const tanggalSekarang = new Date().toLocaleDateString('id-ID', {
                        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                    });
                    const availableWidth = getContentWidth('A4', orientation, pageMargins);

                    const headerTable = {
                        alignment: 'center',
                        table: {
                            widths: [60, '*'],
                            body: [[
                                logo ? {
                                    image: logo,
                                    width: 60,
                                    alignment: 'center'
                                } : '',
                                {
                                    stack: [
                                        instansi.nama_sub_1 ? {
                                            text: instansi.nama_sub_1.toUpperCase(),
                                            style: 'headerSmall'
                                        } : '',
                                        instansi.nama_sub_2 ? {
                                            text: instansi.nama_sub_2.toUpperCase(),
                                            style: 'headerSmall'
                                        } : '',
                                        {text: instansi.nama_instansi.toUpperCase(), style: 'headerBig'},
                                        instansi.akreditasi ? {text: instansi.akreditasi, style: 'headerSmall'} : '',
                                        instansi.alamat ? {text: instansi.alamat, style: 'headerSmall'} : '',
                                        {
                                            text: `Telp: ${instansi.kontak.telepon || '-'} | Email: ${instansi.kontak.email || '-'} | Web: ${instansi.kontak.website || '-'}`,
                                            style: 'headerSmall'
                                        }
                                    ],
                                    alignment: 'center'
                                }
                            ]]
                        },
                        layout: 'noBorders'
                    };

                    // Footer (shared)
                    const footer = {
                        columns: [
                            {text: '', width: '*'},
                            {
                                stack: [
                                    {
                                        text: `${domisili}, ${tanggalSekarang}`,
                                        margin: [0, 10, 0, 0],
                                        alignment: 'center'
                                    },
                                    tandaTangan ? {
                                        image: tandaTangan,
                                        width: 100,
                                        alignment: 'center'
                                    } : {},
                                    {text: userName, alignment: 'center'}
                                ],
                                width: 'auto'
                            }
                        ]
                    };

                    // Combine all content
                    const content = [
                        headerTable,
                        {
                            margin: [0, 5, 0, 5],
                            canvas: [
                                {type: 'line', x1: 0, y1: 0, x2: availableWidth, y2: 0, lineWidth: 2},
                                {
                                    type: 'line',
                                    x1: 0,
                                    y1: 3,
                                    x2: availableWidth,
                                    y2: 3,
                                    lineWidth: 0.5,
                                    lineColor: '#888'
                                }
                            ]
                        },
                        {text: title, style: 'title', margin: [0, 5, 0, 5]},
                        ...bodyContent,
                        footer
                    ];

                    // PDF definition
                    const docDefinition = {
                        pageSize: 'A4',
                        pageOrientation: orientation,
                        pageMargins: pageMargins,
                        content: content,
                        styles: {
                            headerBig: {fontSize: 16, bold: true, alignment: 'center'},
                            headerSmall: {fontSize: 12, alignment: 'center'},
                            title: {fontSize: 14, bold: true, alignment: 'center'},
                            subTitle: {fontSize: 12, bold: true},
                            tableHeader: {bold: true, fillColor: '#ededed', alignment: 'center'},
                            small: {fontSize: 9, alignment: 'center'},
                            tableFont: {fontSize: 5}
                        },
                        defaultStyle: {font: 'Times'}
                    };

                    pdfMake.createPdf(docDefinition).open();

                    successAlert('File telah didownload <br>' +
                        '<p><span class="badge badge-dot bg-danger me-1"></span> Cek pada menu unduhan browser anda untuk memeriksa!</p>');
                } catch (e) {
                    console.error('Error generating PDF:', e);
                    errorAlert(e.message);
                }
            }

            function formatTanggalBayar(value) {
                if (!value || value === '' || value === '0000-00-00 00:00:00') {
                    return '-';
                }
                const parsed = new Date(value);
                if (Number.isNaN(parsed.getTime())) {
                    return '-';
                }
                return parsed.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            }

            async function generateKuitansi(data) {
                try {
                    const bodyContent = [];

                    let siswa = data.siswa;
                    const namaSiswa = siswa.NMCUST ?? siswa.nmcust ?? '-';
                    const paymentBank = data.bank ?? '';
                    const fidBank = paymentBank || data.tagihans?.[0]?.FIDBANK ?? '';
                    const biayaLayanan = (data.biaya_layanan !== undefined && data.biaya_layanan !== null)
                        ? Number(data.biaya_layanan)
                        : (fidBank === '1140002' ? 0 : 2000);
                    let nocust = siswa.NOCUST === null || siswa.NOCUST === '' || siswa.NOCUST === '-' || !siswa.NOCUST ? false : siswa.NOCUST;
                    const uniqueMetode = [...new Set(data.tagihans.map(item => String(item.FIDBANK ?? paymentBank ?? '')))].filter(Boolean);
                    const metodeLabel = paymentBank
                        ? formatMetodePembayaran(paymentBank)
                        : (uniqueMetode.length > 1
                            ? 'Beragam'
                            : formatMetodePembayaran(fidBank));

                    const mainTable = [
                        [(nocust ? 'NIS ' : 'No. Pendaftaran'), ': ' + (nocust ? nocust : siswa.NUM2ND), 'Unit', ': ' + siswa.CODE02],
                        [(nocust ? 'No. VA ' : '-'), ': ' + (nocust ? showVA(nocust) : ''), 'Kelas', ': ' + (siswa.DESC02 ?? '') + ' ' + (siswa.DESC03 ?? '')],
                        ['Nama ', ': ' + namaSiswa, 'Orang Tua', ': ' + (siswa.GENUS ?? siswa.genus ?? '-')],
                        ['Metode Bayar', ': ' + metodeLabel, '', ''],
                    ]

                    bodyContent.push({
                            table: {
                                widths: ['15%', '35%', '15%', '35%'],
                                body: mainTable
                            },
                            layout: 'noBorders'
                        },
                        {
                            text: '',
                            margin: [0, 5, 0, 5]
                        }
                    );

                    const tableBody = [];
                    tableBody.push(
                        ['#', 'Nama Tagihan', 'Periode', 'Tagihan', 'Bayar', 'Metode', 'Tanggal Bayar']
                            .map(h => ({text: h, style: 'tableHeader'})),
                    );

                    let totalBayar = 0;

                    let tagihans = data.tagihans;
                    tagihans.forEach((item, index) => {
                        const billAm = Number(item.BILLAM ?? 0);
                        const nominalBayar = Number(item.NOMINAL_BAYAR ?? item.BILLAM ?? 0);
                        const itemFidBank = item.FIDBANK ?? paymentBank ?? '';
                        const tanggalBayar = formatTanggalBayar(item.PAIDDT);

                        totalBayar += nominalBayar;

                        tableBody.push([
                            {text: index + 1, alignment: 'center'},
                            {text: item.BILLNM, alignment: 'left'},
                            {text: item.BTA, alignment: 'left'},
                            {text: formatRupiah(billAm), alignment: 'right'},
                            {text: formatRupiah(nominalBayar), alignment: 'right'},
                            {text: formatMetodePembayaran(itemFidBank), alignment: 'left'},
                            {text: tanggalBayar, alignment: 'left'},
                        ]);
                    })

                    if (biayaLayanan > 0) {
                        tableBody.push([
                            {colSpan: 4, text: 'Total Tagihan', alignment: 'right', style: 'tableHeader'},
                            {}, {}, {},
                            {text: formatRupiah(totalBayar), alignment: 'right'},
                            {}, {}
                        ])

                        tableBody.push([
                            {colSpan: 4, text: 'Biaya Layanan', alignment: 'right', style: 'tableHeader'},
                            {}, {}, {},
                            {text: formatRupiah(biayaLayanan), alignment: 'right'},
                            {}, {}
                        ])
                    }

                    tableBody.push([
                        {colSpan: 4, text: 'Total', alignment: 'right', style: 'tableHeader'},
                        {}, {}, {},
                        {text: formatRupiah(totalBayar + biayaLayanan), alignment: 'right'},
                        {}, {}
                    ])

                    bodyContent.push({
                        table: {
                            widths: ['3%', '22%', '11%', '14%', '14%', '16%', '20%'],
                            body: tableBody,
                        },
                        layout: {
                            fillColor: (rowIndex) => rowIndex === 0 ? '#ededed' : null,
                            hLineWidth: () => 0.5,
                            vLineWidth: () => 0.5
                        },
                        margin: [0, 0, 0, 10],
                        fontSize: 12
                    });

                    const forPdf = [];
                    forPdf['unit'] = siswa.CODE02;
                    forPdf['data'] = bodyContent;
                    return forPdf;
                } catch (e) {
                    console.log(e)
                }
            }

            async function generatePDFTagihan(data) {
                try {
                    const bodyContent = [];

                    let siswa = data.siswa;
                    let nocust = siswa.NOCUST === null || siswa.NOCUST === '' || siswa.NOCUST === '-' || !siswa.NOCUST ? false : siswa.NOCUST;

                    const mainTable = [
                        [(nocust ? 'NIS ' : 'No. Pendaftaran'), ': ' + (nocust ? nocust : siswa.NUM2ND), 'Unit', ': ' + siswa.CODE02],
                        [(nocust ? 'No. VA ' : '-'), ': ' + (nocust ? showVA(nocust) : ''), 'Kelas', ': ' + siswa.DESC02 + ' ' + siswa.DESC03],
                        ['Nama ', ': ' + siswa.NMCUST, 'Orang Tua', ': ' + (siswa.GENUS ?? siswa.genus ?? '-')],
                        ['', '', '', ''],
                    ]

                    bodyContent.push({
                            table: {
                                widths: ['15%', '35%', '15%', '35%'],
                                body: mainTable
                            },
                            layout: 'noBorders'
                        },
                        {
                            text: '',
                            margin: [0, 5, 0, 5]
                        }
                    );

                    const tableBody = [];
                    tableBody.push(
                        ['#', 'Nama Tagihan', 'Peridoe', 'Tagihan']
                            .map(h => ({text: h, style: 'tableHeader'})),
                    );

                    let totalTagihan = 0;

                    let tagihan = data.tagihans;
                    tagihan.forEach((item, index) => {
                        totalTagihan += item.BILLAM;

                        tableBody.push([
                            {text: index + 1, alignment: 'center'},
                            {text: item.BILLNM, alignment: 'left'},
                            {text: item.BTA, alignment: 'left'},
                            {text: formatRupiah(item.BILLAM), alignment: 'right'}
                        ]);
                    })

                    tableBody.push([
                        {colSpan: 3, text: 'Total Tagihan', alignment: 'right', style: 'tableHeader'},
                        {}, {},
                        {text: formatRupiah(totalTagihan), alignment: 'right'},
                    ])

                    bodyContent.push({
                        table: {
                            widths: ['3%', '27%', '20%', '50%'],
                            body: tableBody,
                        },
                        layout: {
                            fillColor: (rowIndex) => rowIndex === 0 ? '#ededed' : null,
                            hLineWidth: () => 0.5,
                            vLineWidth: () => 0.5
                        },
                        margin: [0, 0, 0, 10],
                        fontSize: 12
                    });

                    return bodyContent;
                } catch (e) {
                    console.log(e)
                }
            }

            function formatRupiah(amount) {
                if (!amount) return 'Rp 0';
                return 'Rp. ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function createError(message, status, extra = {}) {
                const err = new Error(message);
                err.status = status;
                Object.assign(err, extra);
                return err;
            }

            async function buildHttpError(response) {
                const status = response.status;
                const contentType = response.headers.get('content-type');

                let message = `Request failed with status ${status}`;
                let extra = {};

                try {
                    if (contentType?.includes('application/json')) {
                        const data = await response.json();
                        message = data.message ?? message;
                        extra = data;
                    } else {
                        const text = await response.text();
                        message = text || message;
                    }
                } catch {
                }

                return createError(message, status, extra);
            }
        });
    </script>

    {!! ($modalLink??'') !!}
@endsection
