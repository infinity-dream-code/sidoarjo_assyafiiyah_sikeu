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
                                <label for="filter_dari_tanggal" class="form-label text-capitalize form-label-sm">
                                    Tanggal Pembayaran</label>
                                <input type="text" class="form-control"
                                       placeholder="dari tanggal" id="filter_dari_tanggal"
                                       name="filter[dari_tanggal]">
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
                            <button type="button" class="btn btn-facebook btn-print-rekap" id="cetak-rekap">
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
            <div class="row px-5 mb-2">
                <ul class="list-group list-group-timeline">
                    <li class="list-group-item list-group-timeline-warning">
                        Untuk mencetak kartu rekap, silahkan pilih tanggal transaksi dan unit/kelas terlebih dahulu!
                    </li>
                    <li class="list-group-item list-group-timeline-warning">
                        Pastikan browser anda tidak memblokir <i>POP-UP</i>!
                    </li>
                </ul>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"
            integrity="sha512-axXaF5grZBaYl7qiM6OMHgsgVXdSLxqq0w7F4CQxuFyrcPmn0JfnqsOtYHUun80g6mRRdvJDrTCyL8LQqBOt/Q=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/vfs_fonts.min.js"
            integrity="sha512-EFlschXPq/G5zunGPRSYqazR1CMKj0cQc8v6eMrQwybxgIbhsfoO5NAMQX3xFDQIbFlViv53o7Hy+yCWw6iZxA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
            cache: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
        };

        const instansi = {
            nama_instansi: @json(config('app.nama_instansi') ?? ''),
            nama_sub_1: "{{ config('app.nama_sub_instansi_1') ?? ''}}",
            nama_sub_2: "{{ config('app.nama_sub_instansi_2') ?? ''}}",
            akreditasi: "{{ config('app.akreditasi') ?? ''}}",
            alamat: @json(config('app.alamat') ?? ''),
            kontak: {
                telepon: "{{ config('app.telepon') ?? ''}}",
                email: "{{ config('app.email') ?? ''}}",
                website: "{{ config('app.website') ?? ''}}"
            }
        };
        const headerLogo = "{{ base64_encode(file_get_contents(public_path(config('app.logo')))) }}";
        const userName = "KASIR";
        const domisili = @json(config('app.domisili'));
        const tanggalSekarang = "{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}";

        const dariTanggal = $('#filter_dari_tanggal');

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

            let startOfMonth = moment().startOf('month');
            let today = moment();

            document.getElementById('cetak-rekap').addEventListener('click', function (e) {
                e.preventDefault();
                const form = new FormData(document.getElementById('rekapForm'));

                const params = new URLSearchParams();
                for (const [key, value] of form.entries()) {
                    params.append(key, value);
                }

                const unitValue = params.get('filter[unit]');
                const kelasValue = params.get('filter[kelas]');
                const invalidValues = [null, '', 'undefined', 'all'];

                if (invalidValues.includes(unitValue) && invalidValues.includes(kelasValue)) {
                    warningAlert('Silahkan pilih salah satu Tingkat/Kelas terlebih dahulu!');
                    return;
                }

                loadingAlert(`Membuat Rekap ... <br> Proses ini membutuhkan waktu beberapa saat<br><hr>
                    <p><span class="badge badge-dot bg-danger me-1"></span> Pastikan browser anda tidak memblokir <i>POP-UP</i>! </p>
                `);
                let url = '{{route('admin.rekap-penerimaan-harian.cetak-rekap-harian')}}';
                const fullUrl = `${url}?${params.toString()}`;
                const request = new Request(
                    fullUrl, {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            // 'Accept': 'application/pdf'
                        }
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
                        // console.log(data);
                        // successAlert('testing sukses');
                        generatePdfRekapTagihanPdfMake(data)
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

            });

            dariTanggal.datepicker({
                format: "dd-mm-yyyy",
                autoclose: true
            }).datepicker('setDate', new Date());

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

            function generatePdfRekapTagihanPdfMake(data) {
                const logo = headerLogo ? {image: 'data:image/jpeg;base64,' + headerLogo, width: 60} : '';
                const tanggalSekarang = formatTanggalIndonesia(new Date());

                const orientation = 'portrait';
                const pageMargins = [20, 20, 20, 20];
                const posts = data.data;
                const unitSelect = document.querySelector("[name='filter[unit]']");
                const selectedGroup = unitSelect.options[unitSelect.selectedIndex].dataset.group;

                const kelasSelect = document.querySelector("[name='filter[kelas]']");
                const selectedKelas = kelasSelect.value;
                const unit = selectedKelas.split("~");

                const headerTable = {
                    table: {
                        widths: ['15%', '85%'],
                        body: [
                            [
                                logo,
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
                                    ]
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders'
                };

                const content = [];

                const availableWidth = getContentWidth('A4', orientation, pageMargins);

                content.push(headerTable);
                content.push({
                    margin: [0, 5, 0, 5],
                    canvas: [
                        {type: 'line', x1: 0, y1: 0, x2: availableWidth, y2: 0, lineWidth: 2},
                        {type: 'line', x1: 0, y1: 3, x2: availableWidth, y2: 3, lineWidth: 0.5, lineColor: '#888'}
                    ]
                });

                content.push({text: 'REKAP DATA PENERIMAAN', style: 'title', margin: [0, 2, 0, 0]});
                content.push({text: 'Unit: ' + (selectedGroup ?? unit[0] ?? ""), style: 'subTitle', margin: [0, 2, 0, 5]});

                if (posts.length === 0) {
                    content.push({text: 'Tidak ada data', alignment: 'center', margin: [0, 20, 0, 0]});
                } else {
                    const tableBody = [];
                    tableBody.push([
                        {text: 'NIS', style: 'tableHeader'},
                        {text: 'Nama', style: 'tableHeader'},
                        {text: 'Kelas', style: 'tableHeader'},
                        {text: 'Cash', style: 'tableHeader'},
                        {text: 'VA', style: 'tableHeader'},
                        {text: 'Total', style: 'tableHeader'}
                    ]);

                    posts.forEach(item => {
                        tableBody.push([
                            {text: item.nocust, alignment: 'left'},
                            {text: item.nmcust, alignment: 'left'},
                            {text: item.DESC02 + '  ' + item.DESC03, alignment: 'left'},
                            {text: formatRupiah(item.transaksi), alignment: 'right'},
                            {text: formatRupiah(item.transaksi_va), alignment: 'right'},
                            {text: formatRupiah(item.total_transaksi_siswa), alignment: 'right'}
                        ]);
                    });

                    content.push({
                        table: {
                            widths: ['10%', '25%', '20%', '15%', '15%', '15%'],
                            body: tableBody,
                        },
                        layout: {
                            fillColor: (rowIndex) => rowIndex === 0 ? '#ededed' : null,
                            hLineWidth: () => 0.5,
                            vLineWidth: () => 0.5
                        },
                        margin: [0, 0, 0, 5],
                        fontSize: 9
                    });
                }

                const footer = {
                    columns: [
                        {text: '', width: '*'},
                        {
                            stack: [
                                {text: `${domisili}, ${tanggalSekarang}`, margin: [0, 10, 0, 5], alignment: 'center'},
                                {text: userName, alignment: 'center'}
                            ],
                            width: 'auto'
                        }
                    ]
                };

                content.push(footer);

                const docDefinition = {
                    pageSize: 'A4', // or 'LEGAL', 'LETTER'
                    pageOrientation: orientation, // or 'landscape'
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
                    }, defaultStyle: {
                        font: 'Times'
                    }
                };

                // pdfMake.createPdf(docDefinition).download('rekap-penerimaan.pdf');
                pdfMake.createPdf(docDefinition).open();

                successAlert('File rekap telah terbuka pada tab baru. <br>' +
                    '<p><span class="badge badge-dot bg-danger me-1"></span>Pastikan browser anda tidak memblokir "Pop-Up"</p>' +
                    '')

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

                function formatRupiah(amount) {
                    if (!amount) return 'Rp 0';
                    return 'Rp. ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }
        });

    </script>

    {!! ($modalLink??'') !!}
@endsection
