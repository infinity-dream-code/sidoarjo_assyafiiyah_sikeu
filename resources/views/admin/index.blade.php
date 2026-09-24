@extends('layouts.admin_new')
@section('title',$dataTitle??$mainTitle??$title??'Dashboard')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/apex-charts/apex-charts.css')}}"/>

@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 col-xxl-6 mb-6 order-3 order-xxl-1">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2 mb-1">Tagihan Dibayar</h5>
                        <p class="text-body mb-0">Total tagihan yang dibayar</p>
                    </div>
                    <a class="btn btn-outline-primary" href="{{route('admin.keuangan.penerimaan-siswa.data-penerimaan.index')}}">
                        Detail
                    </a>
                </div>
                <div class="card-body">
                    <div id="tagihan_dibayar"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xxl-6 mb-6 order-3 order-xxl-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Tagihan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <p class="d-block mb-0 text-body">Jumlah Tagihan</p>
                        </div>
                        <h4 class="mb-0">{{$jumlah_tagihan_belum_dibayar + $jumlah_tagihan_dibayar}}</h4>
                    </div>
                    <div class="row mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-twitter rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="menu-icon icon icon-tabler icons-tabler-outline icon-tabler-list-check">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3.5 5.5l1.5 1.5l2.5 -2.5"/>
                                        <path d="M3.5 11.5l1.5 1.5l2.5 -2.5"/>
                                        <path d="M3.5 17.5l1.5 1.5l2.5 -2.5"/>
                                        <path d="M11 6l9 0"/>
                                        <path d="M11 12l9 0"/>
                                        <path d="M11 18l9 0"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{$jumlah_tagihan_dibayar}}</h5>
                                <p class="mb-0">Tagihan Dibayar</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-twitter rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                         class="menu-icon icon icon-tabler icons-tabler-outline icon-tabler-list">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 6l11 0"/>
                                        <path d="M9 12l11 0"/>
                                        <path d="M9 18l11 0"/>
                                        <path d="M5 6l0 .01"/>
                                        <path d="M5 12l0 .01"/>
                                        <path d="M5 18l0 .01"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{$jumlah_tagihan_belum_dibayar}}</h5>
                                <p class="mb-0">Tagihan Belum Dibayar</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @php
                        $total_tagihan = $jumlah_tagihan_dibayar + $jumlah_tagihan_belum_dibayar;
                        if ($total_tagihan > 0) {
                            $persenBelumDibayar = round($jumlah_tagihan_belum_dibayar / $total_tagihan * 100, 2);
//                            $persenDibayar = round($jumlah_tagihan_dibayar / $total_tagihan * 100, 2);
                            $persenDibayar = 100-$persenBelumDibayar;
                        } else {
                            $persenDibayar = 0;
                            $persenBelumDibayar = 0;
                        }
                    @endphp
                    <div class="row">
                        <div class="col-4">
                            <div class="d-flex gap-2 align-items-center mb-2">
                                <div class="avatar avatar-xs flex-shrink-0">
                                    <div class="avatar-initial rounded bg-label-success">
                                        <i class="ri-check-line ri-24px"></i>
                                    </div>
                                </div>
                                <p class="mb-0">Dibayar</p>
                            </div>
                            <h4 class="mb-2">{{$persenDibayar}}%</h4>
                        </div>
                        <div class="col-4">
                            <div class="divider divider-vertical">
                                <div class="divider-text">
                                    <span class="badge-divider-bg">*-*</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                                <p class="mb-0">Belum Dibayar</p>
                                <div class="avatar avatar-xs flex-shrink-0">
                                    <div class="avatar-initial rounded bg-label-danger">
                                        <i class="ri-close-line ri-16px"></i>
                                    </div>
                                </div>
                            </div>
                            <h4 class="mb-2">{{$persenBelumDibayar}}%</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                        <div class="progress w-100 rounded" style="height: 12px;">
                            <div class="progress-bar bg-success" style="width: {{$persenDibayar}}%" role="progressbar"
                                 aria-valuenow="{{$persenDibayar}}" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-danger" role="progressbar"
                                 style="width: {{$persenBelumDibayar}}%" aria-valuenow="{{$persenBelumDibayar}}"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 mb-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title m-0">SPP Dibayar Bulanan</h5>
                        <small class="text-body">Rekap Januari - Desember</small>
                    </div>
                    <form method="GET" action="{{ route('admin.index') }}" class="d-flex align-items-center gap-2">
                        <label for="tahun" class="mb-0 text-body">Tahun</label>
                        <select id="tahun" name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @forelse($tahun_tahun as $tahun)
                                <option value="{{ $tahun }}" {{ (int) $tahun_dipilih === (int) $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @empty
                                <option value="{{ now()->year }}">{{ now()->year }}</option>
                            @endforelse
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <div id="tagihan_dibayar_bulanan"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0">Pembayaran Baru</h5>
                </div>
                <div class="card-body py-3 mb-3">
                    <ul class="timeline pb-0 mb-0">
                        @if(isset($tagihan_baru_dibayar))
                            @php
                                $codes = [
                                    '1140000' => 'Manual Cash',
                                    '1140001' => 'Manual BMI',
                                    '1140002' => 'Manual SALDO',
                                    '1140003' => 'Transfer Bank Lain',
                                    '1140004' => 'INFAQ',
                                    '1200001' => 'Loket Manual - Beasiswa',
                                    '1200002' => 'Loket Manual - Potongan',
                                ];
                            @endphp
                            @if($tagihan_baru_dibayar->count() == 0)
                                <li class="timeline-item timeline-item-transparent border-transparent">
                                    <span class="timeline-point timeline-point-gray"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-2">
                                            <h6 class="mb-0">Tidak ada tagihan yang baru dibayar</h6>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @foreach($tagihan_baru_dibayar as $item)
                                <li class="timeline-item ps-6 border-success border-left-dashed">
                                    <span class="timeline-indicator-advanced text-success border-0 shadow-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round"
                                             class="icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-check">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95"/>
                                            <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44"/>
                                            <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92"/>
                                            <path d="M8.56 20.31a9 9 0 0 0 3.44 .69"/>
                                            <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95"/>
                                            <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44"/>
                                            <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92"/>
                                            <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69"/>
                                            <path d="M9 12l2 2l4 -4"/>
                                        </svg>
                                    </span>
                                    <div class="timeline-event ps-1">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{$item->BILLNM}}</h6>
                                            <small class="text-dark">
                                                {{ \Carbon\Carbon::parse($item->PAIDDT)->locale('id')->isoFormat('dddd, D MMMM YYYY')}}
                                            </small>
                                        </div>
                                        <h6>@rupiah($item->BILLAM??0)</h6>
                                        <p class="mt-1 mb-0">{{$item->nama}} - {{$item->nis}}
                                            <br> {{$item->CODE02}} - {{$item->DESC02}} - {{$item->DESC04}}</p>
                                    </div>
                                </li>
                                <div class="border-0 border-success border-top border-dashed mb-2 "></div>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{asset('main/libs/apex-charts/apexcharts.js')}}"></script>
    <script>
        let labelColor, headingColor, currentTheme, bodyColor;

        if (isDarkStyle) {
            labelColor = config.colors_dark.textMuted;
            headingColor = config.colors_dark.headingColor;
            bodyColor = config.colors_dark.bodyColor;
            currentTheme = 'dark';
        } else {
            labelColor = config.colors.textMuted;
            headingColor = config.colors.headingColor;
            bodyColor = config.colors.bodyColor;
            currentTheme = 'light';
        }

        // Chart Colors
        const chartColors = {
            donut: {
                series1: config.colors.success,
                series2: '#43ff64e6',
                series3: '#43ff6473',
                series4: '#43ff6433'
            },
            line: {
                series1: config.colors.success,
                series2: config.colors.success,
                series3: '#43ff64e6'
            }
        };
    </script>
    <script type="module">
        @php
            $maxCountTaighanDibayar = 10;
            $maxCountTagihanDibayarBulanan = 10;
        @endphp
        const dataHarian = [
            @foreach($chartTagihanDibayar as $item)
                @php
                    $maxCountTaighanDibayar = max($maxCountTaighanDibayar, $item['count'] ?? 0);
                @endphp
                {{$item['count']??0}},
            @endforeach
        ];
        const categoriesHarian = [
            @foreach($chartTagihanDibayar as $item)
                '{{$item['date']??'-'}}',
            @endforeach
        ];
        const dataBulanan = [
            @foreach($chartTagihanDibayarBulanan as $item)
                @php
                    $maxCountTagihanDibayarBulanan = max($maxCountTagihanDibayarBulanan, $item['count'] ?? 0);
                @endphp
                {{$item['count']??0}},
            @endforeach
        ];
        const categoriesBulanan = [
            @foreach($chartTagihanDibayarBulanan as $item)
                '{{$item['date']??'-'}}',
            @endforeach
        ];

        const reversedDataHarian = [...dataHarian].reverse();
        const reversedCategoriesHarian = [...categoriesHarian].reverse();

        const tagihanDibayarHarian = document.querySelector('#tagihan_dibayar'),
            tagihanDibayarHarianConfig = {
                series: [
                    {
                        name: 'Tagihan Dibayar',
                        type: 'column',
                        data: reversedDataHarian
                    },
                ],
                chart: {
                    height: 270,
                    type: 'bar',
                    stacked: false,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    markers: {
                        width: 8,
                        height: 8,
                        offsetX: -3
                    },
                    height: 40,
                    offsetY: 10,
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    },
                    fontSize: '15px',
                    fontFamily: 'Inter',
                    fontWeight: 400,
                    labels: {
                        colors: headingColor,
                        useSeriesColors: false
                    },
                    offsetY: 10
                },
                grid: {
                    strokeDashArray: 8
                },
                colors: [chartColors.line.series2],
                fill: {
                    opacity: [1, 1]
                },
                plotOptions: {
                    bar: {
                        columnWidth: '30%',
                        startingShape: 'rounded',
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    tickAmount: 10,
                    categories: reversedCategoriesHarian,
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '13px',
                            fontFamily: 'Inter',
                            fontWeight: 400
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    tickAmount: 4,
                    min: 1,
                    max: {{$maxCountTaighanDibayar + 10}},
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '13px',
                            fontFamily: 'Inter',
                            fontWeight: 400
                        },
                        // formatter: function (val) {
                        //     return val + '%';
                        // }
                    }
                },
                responsive: [
                    {
                        breakpoint: 1400,
                        options: {
                            chart: {
                                height: 270
                            },
                            xaxis: {
                                labels: {
                                    style: {
                                        fontSize: '10px'
                                    }
                                }
                            },
                            legend: {
                                itemMargin: {
                                    vertical: 0,
                                    horizontal: 10
                                },
                                fontSize: '13px',
                                offsetY: 12
                            }
                        }
                    },
                    {
                        breakpoint: 1399,
                        options: {
                            chart: {
                                height: 415
                            },
                            plotOptions: {
                                bar: {
                                    columnWidth: '50%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 982,
                        options: {
                            plotOptions: {
                                bar: {
                                    columnWidth: '30%'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: 250
                            },
                            legend: {
                                offsetY: 7
                            }
                        }
                    }
                ]
            };
        if (typeof tagihanDibayarHarian !== undefined && tagihanDibayarHarian !== null) {
            const chartHarian = new ApexCharts(tagihanDibayarHarian, tagihanDibayarHarianConfig);
            chartHarian.render();
        }

        const tagihanDibayarBulanan = document.querySelector('#tagihan_dibayar_bulanan'),
            tagihanDibayarBulananConfig = {
                series: [
                    {
                        name: 'SPP Dibayar',
                        type: 'column',
                        data: dataBulanan
                    }
                ],
                chart: {
                    height: 270,
                    type: 'bar',
                    stacked: false,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    markers: {
                        width: 8,
                        height: 8,
                        offsetX: -3
                    },
                    height: 40,
                    offsetY: 10,
                    itemMargin: {
                        horizontal: 10,
                        vertical: 0
                    },
                    fontSize: '15px',
                    fontFamily: 'Inter',
                    fontWeight: 400,
                    labels: {
                        colors: headingColor,
                        useSeriesColors: false
                    },
                    offsetY: 10
                },
                grid: {
                    strokeDashArray: 8
                },
                colors: [chartColors.line.series1],
                fill: {
                    opacity: [1, 1]
                },
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        startingShape: 'rounded',
                        endingShape: 'rounded',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    tickAmount: 12,
                    categories: categoriesBulanan,
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '13px',
                            fontFamily: 'Inter',
                            fontWeight: 400
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    tickAmount: 4,
                    min: 0,
                    max: {{$maxCountTagihanDibayarBulanan + 10}},
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '13px',
                            fontFamily: 'Inter',
                            fontWeight: 400
                        }
                    }
                }
            };
        if (typeof tagihanDibayarBulanan !== undefined && tagihanDibayarBulanan !== null) {
            const chartBulanan = new ApexCharts(tagihanDibayarBulanan, tagihanDibayarBulananConfig);
            chartBulanan.render();
        }
    </script>
@endsection
