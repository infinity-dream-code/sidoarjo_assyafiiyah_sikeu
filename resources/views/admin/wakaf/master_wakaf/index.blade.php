@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    <style>
        .dt-switch-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .dt-switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
        }

        .dt-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .dt-switch-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #d9dee3;
            border-radius: 30px;
            transition: background-color .25s ease;
        }

        .dt-switch-slider:before {
            content: "";
            position: absolute;
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background: #fff;
            border-radius: 50%;
            transition: transform .25s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .18);
        }

        .dt-switch input:checked + .dt-switch-slider {
            background: #28c76f;
        }

        .dt-switch input:checked + .dt-switch-slider:before {
            transform: translateX(26px);
        }

        .dt-switch-label {
            min-width: 58px;
            font-size: 13px;
            font-weight: 600;
        }

        .dt-switch-wrap.is-active .dt-switch-label {
            color: #28c76f;
        }

        .dt-switch-wrap.is-inactive .dt-switch-label {
            color: #ea5455;
        }
    </style>
@endsection

@section('content')
    <h3 class="page-heading d-flex text-gray-900 fw-bold flex-column justify-content-center my-0">
        {{($dataTitle??($mainTitle??($title??'')))}}
    </h3>
    <ul class="breadcrumb breadcrumb-style2">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.index') }}" class="text-hover-primary">Beranda</a>
        </li>
        <li class="breadcrumb-item">{{ $title ?? 'Wakaf' }}</li>
        <li class="breadcrumb-item">{{ $mainTitle ?? 'Wakaf' }}</li>
        <li class="breadcrumb-item active">{{ $dataTitle ?? 'Master Wakaf' }}</li>
    </ul>

    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <fieldset class="form-fieldset">
                    <h5>Filter</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="filter[nama]">Nama Sumbangan</label>
                            <input type="text" class="form-control" id="filter[nama]" name="filter[nama]" placeholder="Cari nama sumbangan" value="{{ request('filter.nama', '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="filter[status]">Status</label>
                            <select class="form-select" id="filter[status]" name="filter[status]">
                                @php($filterStatus = (string) request('filter.status', 'all'))
                                <option value="all" @selected($filterStatus === 'all')>Semua</option>
                                <option value="1" @selected($filterStatus === '1')>Aktif</option>
                                <option value="0" @selected($filterStatus === '0')>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <button type="reset" class="btn btn-secondary">
                            <span class="ri-reset-left-line me-2"></span>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="ri-search-line me-2"></span>Cari
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="card-datatable table-responsive text-nowrap">
            <table class="table table-sm table-bordered table-hover" id="main_table">
                <thead class="table-light"></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.js?v=20260924-id3')}}?v=20260605-export-fix"></script>

    <script type="text/javascript">
        const dtOptions = {
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
            buttons: ['copy', 'excel', 'pdf', 'print'],
        };

        document.addEventListener('DOMContentLoaded', function () {
            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);

                if (dtOptions.formId) {
                    const filterForm = $(`#${dtOptions.formId}`);
                    filterForm.on('submit', function (e) {
                        e.preventDefault();
                        dataReFilter(dtOptions.tableId);
                    });
                    filterForm.on('reset', function () {
                        setTimeout(function () {
                            dataReFilter(dtOptions.tableId);
                        }, 0);
                    });
                }
            }

            document.querySelector(`#${dtOptions.tableId} tbody`).addEventListener('change', function (e) {
                const toggle = e.target.closest('.dt-status-switch');
                if (!toggle) return;

                const itemId = toggle.getAttribute('data-id');
                if (!itemId) {
                    warningAlert('Data tidak valid, silahkan muat ulang halaman');
                    toggle.checked = !toggle.checked;
                    return;
                }

                loadingAlert('Mengubah status wakaf...');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const url = '{{ route('admin.wakaf.master-wakaf.toggle-status', ':id') }}'.replace(':id', itemId);

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                })
                    .then(async (response) => {
                        const result = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw {status: response.status, message: result.message || 'Gagal mengubah status'};
                        }
                        return result;
                    })
                    .then((result) => {
                        successAlert(result.message || 'Status berhasil diubah');
                        dataReload(dtOptions.tableId);
                    })
                    .catch((error) => {
                        toggle.checked = !toggle.checked;
                        errorAlert(error.message || 'Terjadi kesalahan saat mengubah status');
                    });
            });
        });
    </script>
@endsection
