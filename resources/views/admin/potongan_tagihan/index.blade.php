@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
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
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{($dataTitle??$mainTitle??$title)}}</h5>
            <a type="button" href="{{route('admin.potongan-tagihan.create')}}" class="btn btn-success"
               id="simpan-potongan">
                <span class="ri-add-line me-2"></span>
                Buat Potongan Tagihan
            </a>
        </div>
        <div class="card-body">
            <div class="row px-5 mb-2">
                <ul class="list-group list-group-timeline">
                    <li class="list-group-item list-group-timeline-danger">
                        <strong>Pastikan browser anda tidak memblokir <i>POP-UP</i>!</strong>
                    </li>
                </ul>
            </div>
            <form id="filter_form">
                <fieldset class="form-fieldset mb-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-5">
                                <label class="form-label" for="tanggal-transaksi">Tanggal Transaksi <span
                                        class="text-warning">*</span>(tanggal-bulan-tahun - tanggal-bulan-tahun)</label>
                                <input type="text" id="tanggal-transaksi" name="filter[tanggal-transaksi]"
                                       placeholder="tanggal/bulan/tahun"
                                       class="form-control" autocomplete="false" inputmode="numeric"/>
                            </div>
                            <div class="mb-5">
                                <label class="form-label" for="tanggal-potongan">Tanggal Potongan <span
                                        class="text-warning">*</span>(tanggal-bulan-tahun - tanggal-bulan-tahun)</label>
                                <input type="text" id="tanggal-potongan" name="filter[tanggal-potongan]"
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
                            <button type="button" class="btn btn-danger text-nowrap" id="btn-hapus">
                                <span class="ri-delete-bin-2-line me-2"></span>
                                Hapus
                            </button>
                            <button type="button" class="btn btn-facebook text-nowrap" id="cetak-kuitansi">
                                <span class="ri-info-card-line me-2"></span>
                                Cetak Kuitansi
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
        <div class="card-body">
            <div class="row">
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
        </div>
    </div>
@endsection
@section('momentjs',true)
@section('datatable',true)
@section('datatable-buttons',true)
@section('datatable-select',true)
@section('datatable-row-grup',true)
@section('datatable-fixed-columns',true)
@section('bootstrap-daterangepicker', true)
@section('script')
    <script src="{{asset('main/libs/select2/select2.js')}}"></script>

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

    <script src="{{asset('js/helper/formattedNumber.min.js')}}"></script>
    <script type="text/javascript">
        const select2 = $(`[data-control='select2']`);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let dtOptions = {
            tableId: 'main_table',
            formId: 'filter_form',
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
            buttons: ['copy', 'excel', 'pdf'],
            select: 'multi',
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

            let startOfMonth = moment().startOf('month');
            let today = moment();
            let date = $('#tanggal-transaksi');
            let tanggal_potongan = $('#tanggal-potongan');
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

            tanggal_potongan.daterangepicker({
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
                    tanggal_potongan.data('daterangepicker').setStartDate(start);
                    tanggal_potongan.data('daterangepicker').setEndDate(start.clone().add(6, 'days'));
                }
            });

            tanggal_potongan.on('apply.daterangepicker hide.daterangepicker', function (ev, picker) {
                if (picker.startDate && picker.endDate) {
                    $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                }
            });

            tanggal_potongan.on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                // picker.setStartDate(moment().startOf('month'));
                // picker.setEndDate(moment());
            });

            const instansi = {
                nama_instansi: @json(config('app.nama_instansi') ?? ''),
                nama_sub_1: "{{ config('app.nama_sub_instansi_1') }}",
                nama_sub_2: "{{ config('app.nama_sub_instansi_2') }}",
                akreditasi: "{{ config('app.akreditasi') }}",
                alamat: @json(config('app.alamat')),
                kontak: {
                    telepon: "{{ config('app.telepon') }}",
                    email: "{{ config('app.email') }}",
                    website: "{{ config('app.website') }}"
                }
            };
            const headerLogo = "{{ base64_encode(file_get_contents(public_path(config('app.logo')))) }}";
            const userName = "{{ Auth::user()->name }}";
            const domisili = @json(config('app.domisili'));

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

            function formatRupiah(amount) {
                if (!amount) return 'Rp 0';
                return 'Rp. ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function getContentWidth(pageSize = 'A4', orientation = 'portrait', margins = [30, 30, 30, 30]) {
                const sizes = {
                    A4: [595.28, 841.89],
                    A3: [841.89, 1190.55],
                    LETTER: [612, 792],
                    LEGAL: [612, 1008]
                };
                const key = String(pageSize).toUpperCase();
                const size = sizes[key] || sizes.A4;

                const pageW = orientation === 'landscape' ? size[1] : size[0];
                const [ml, , mr] = margins;
                return pageW - ml - mr;
            }

            async function generatePdf(title, bodyContent) {
                try {
                    let logo = 'data:image/jpeg;base64,' + headerLogo;

                    const orientation = 'portrait';
                    const pageMargins = [20, 20, 20, 20];
                    const tanggalSekarang = formatTanggalIndonesia(new Date());
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
                                    {text: userName, alignment: 'center'}
                                ],
                                width: 'auto'
                            }
                        ]
                    };

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

            function generateKuitansi(biayaLayanan = false) {
                let data = DT[`${dtOptions.tableId}`].rows({selected: true}).data().toArray();

                if (!data[0]) {
                    warningAlert('silahkan pilih minimal satu tagihan!');
                    return;
                }

                let firstValue = data[0]?.nocust;
                let allSame = data.every(row => row.nocust === firstValue);

                if (!allSame) {
                    warningAlert('silahkan pilih siswa yang sama!');
                    return;
                }

                let siswa = data[0];
                let nocust = siswa.nocust === null || siswa.nocust === '' || siswa.nocust === '-' || !siswa.nocust ? false : siswa.nocust;

                const mainTable = [
                    [(nocust ? 'NIS ' : 'No. Pendaftaran'), ': ' + (nocust ? nocust : siswa.NUM2ND), 'Unit', ': ' + siswa.CODE02],
                    ['No. VA', ': ' + siswa.NOVA, 'Kelas', ': ' + siswa.DESC02 + ' ' + siswa.DESC03],
                    ['Nama ', ': ' + siswa.nmcust, '', ''],
                    ['Metode Bayar', ': ' + formatMetodePembayaran(siswa.FIDBANK ?? ''), '', ' '],
                ]

                let content = [];
                const headerContent = [
                    {
                        table: {
                            widths: ['15%', '35%', '15%', '35%'],
                            body: mainTable
                        },
                        layout: 'noBorders'
                    },
                    {
                        text: '',
                        margin: [0, 5, 0, 5]
                    },
                ]

                content.push(headerContent);

                const tableBody = [];
                tableBody.push(
                    ['#', 'Nama Tagihan', 'Peridoe', 'Tagihan', 'Bayar', 'Tanggal Bayar']
                        .map(h => ({text: h, style: 'tableHeader'})),
                );

                let totalTagihan = 0;

                data.forEach((item, index) => {
                    let tanggalBayar = item.PAIDDT;
                    if (tanggalBayar && tanggalBayar !== '' && tanggalBayar !== '0000-00-00 00:00:00') {
                        tanggalBayar = formatTanggalIndonesia(tanggalBayar);
                    }

                    totalTagihan += item.BILLAM;

                    tableBody.push([
                        {text: index + 1, alignment: 'center'},
                        {text: item.BILLNM, alignment: 'left'},
                        {text: item.BTA, alignment: 'left'},
                        {text: formatRupiah(item.BILLAM), alignment: 'right'},
                        {text: formatRupiah(item.BILLAM), alignment: 'right'},
                        {text: tanggalBayar, alignment: 'left'},
                    ]);
                    let cutLists = item.BILL_CUT_LISTS_RAW;
                    if (cutLists) {
                        tableBody.push([
                            {},
                            {
                                colSpan: 5,
                                text: "Potongan " + item.BILLNM,
                                alignment: 'left',
                                style: 'tableHeader'
                            }, {}, {}, {}, {}
                        ])

                        let totalPotongan = 0;
                        tableBody.push([
                            {},
                            {text: "Tanggal", alignment: 'center', style: 'tableHeader',colSpan: 2},
                            {},
                            {text: "Potongan", alignment: 'center', style: 'tableHeader'},
                            {colSpan: 2, text: "Keterangan", alignment: 'center', style: 'tableHeader'},
                            {}
                        ]);

                        cutLists.forEach((cut_item, index) => {
                            let tanggalPotongan = cut_item.CUT_DATE;
                            if (tanggalPotongan && tanggalPotongan !== '' && tanggalPotongan !== '0000-00-00 00:00:00') {
                                tanggalPotongan = formatTanggalIndonesia(tanggalPotongan);
                            }

                            totalPotongan += cut_item.BILL_CUT;
                            tableBody.push([
                                {},
                                {text: tanggalPotongan,colSpan: 2, alignment: 'left'},
                                {},
                                {text: formatRupiah(cut_item.BILL_CUT), alignment: 'right'},
                                {text: cut_item.REASON ?? '-', alignment: 'left', colSpan: 2},
                                {}
                            ])
                        })

                        tableBody.push([
                            {colSpan: 3, text: 'Total Potongan', alignment: 'right', style: 'tableHeader'},
                            {},
                            {},
                            {text: formatRupiah(totalPotongan), alignment: 'right'},
                            {text:'', colSpan: 2, style: 'tableHeader'}, {}
                        ])
                    }
                })

                if (biayaLayanan) {
                    tableBody.push([
                        {colSpan: 4, text: 'Biaya Layanan', alignment: 'right', style: 'tableHeader'},
                        {}, {}, {},
                        {text: formatRupiah(2000), alignment: 'right'},
                        {},
                    ])
                }


                tableBody.push([
                    {colSpan: 4, text: 'Total Tagihan Yang Dibayarkan', alignment: 'right', style: 'tableHeader'},
                    {}, {}, {},
                    {text: formatRupiah(totalTagihan + (biayaLayanan ? 2000 : 0)), alignment: 'right'},
                    {},
                ])

                content.push({
                    table: {
                        widths: ['3%', '25%', '15%', '15%', '20%', '22%'],
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

                generatePdf('KUITANSI', content);
            }

            function formatMetodePembayaran(data) {
                const descriptions = {
                    '1140000': 'Manual Cash',
                    '1140001': 'Manual BMI',
                    '1140002': 'Manual SALDO',
                    '1140003': 'Transfer Bank Lain',
                    '1140004': 'Transfer Bank BNI',
                    '1140005': 'Transfer Bank BRI',
                    '1200001': 'Loket Manual - Beasiswa',
                    '1200002': 'Loket Manual - Potongan',
                    '1': 'H2H VA BMI - ATM',
                    '2': 'H2H VA BMI - Teller',
                    '3': 'H2H VA BMI - IBANK',
                    '4': 'H2H VA BMI - EDC',
                    '5': 'H2H VA BMI - MOBILE',
                    '6': 'ALL BMI',
                    null: 'Nomor VA',
                    '': 'Nomor VA'
                };
                return descriptions[data] || data;
            }

            document.getElementById('cetak-kuitansi').addEventListener('click', function (e) {
                e.preventDefault();
                generateKuitansi();
            });

            document.getElementById('btn-hapus').addEventListener('click', function (e) {
                e.preventDefault();

                let data = DT[`${dtOptions.tableId}`].rows({selected: true}).data();

                if (!data[0] || !data[0]['AA'] || data[0]['AA'].length === 0) {
                    warningAlert('silahkan pilih tagihan yang akan dipotong!')
                    return;
                }

                let url = '{{route('admin.potongan-tagihan.destroy', ':id')}}'
                url = url.replace(':id', data[0]['AA'])

                const request = new Request(url, {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': "application/json"
                        },
                    });

                fetch(request)
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw {status: response.status, message: data.message || response.statusText};
                        }
                        return data;
                    })
                    .then(data => {
                        dataReload(dtOptions.tableId);
                        successAlert(data.message);
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

            // document.getElementById('cetak-kuitansi-2000').addEventListener('click', function (e) {
            //     e.preventDefault();
            //     generateKuitansi(true);
            // });
        });


    </script>
@endsection
