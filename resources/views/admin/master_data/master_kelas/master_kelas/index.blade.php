@extends('layouts.admin_new')
@section('style')
    <link rel="stylesheet" href="{{asset('main/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('main/libs/select2/select2.min.css')}}">
@endsection
@section('content')
    <h3 class="page-heading d-flex text-gray-900 fw-bold flex-column justify-content-center my-0">
        {{($dataTitle??($mainTitle??($title??'')))}}
    </h3>
    <ul class="breadcrumb breadcrumb-style2">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.index') }}" class="text-hover-primary">Beranda</a>
        </li>

        @isset($title)
            <li class="breadcrumb-item">{{ $title }}</li>
        @endisset

        @isset($mainTitle)
            <li class="breadcrumb-item">{{ $mainTitle }}</li>
        @endisset

        @if(isset($dataTitle) && isset($mainTitle) && $mainTitle !== $dataTitle)
            <li class="breadcrumb-item {{$showTitle??'active'}}">
                @if(isset($indexUrl))
                    <a href="{{ $indexUrl }}" class="text-hover-primary">{{ $dataTitle }}</a>
                @else
                    {{ $dataTitle }}
                @endif
            </li>

            @isset($showTitle)
                <li class="breadcrumb-item active">{{ $showTitle }}</li>
            @endisset
        @endif
    </ul>

    <div class="card">
        <div class="card-header header-elements">
            <h5 class="mb-0 me-2">{{($dataTitle??$mainTitle)}}</h5>
            <div class="card-header-elements ms-auto">
                <div class="w-100">
                    <div class="row">
                        <div class="d-flex justify-content-center justify-content-md-end gap-4">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modal-create" title="Buat Data">
                                <span class="ri-add-line me-2"></span>
                                Buat Data
                            </button>
                        </div>
                    </div>
                </div>
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
    <script src="{{asset('main/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('js/datatableCustom/Datatable-0-4.min.js')}}"></script>
    <script src="{{asset('js/helper/errorInputHelper.min.js')}}"></script>
    <script src="{{asset('main/libs/select2/select2.min.js')}}"></script>


    <form id="addForm" class="mainForm">
        <div class="modal modal-blur fade" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Tambah Data Master Kelas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <fieldset class="form-fieldset">
                            <div class="mb-3">
                                <label class="form-label required" for="sekolah">Sekolah</label>
                                <select class="form-select" name="sekolah" id="sekolah" data-control='select2'>
                                    @if(isset($sekolah))
                                        @foreach($sekolah as $item)
                                            <option value="{{$item->CODE01}}">{{$item->DESC01}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="unit_kelas">Unit</label>
                                <input type="text" class="form-control" id="unit_kelas" name="unit_kelas" autocomplete="off"
                                       placeholder="Unit" required>
                                <div class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="kelas">Kelas</label>
                                <input type="text" class="form-control" name="kelas" id="kelas" autocomplete="off"
                                       placeholder="Kelas" required>
                                <div class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required" for="kelompok">Kelompok</label>
                                <input type="text" class="form-control" id="kelompok" name="kelompok" autocomplete="off"
                                       placeholder="Kelompok" required>
                                <div class="invalid-feedback" role="alert">
                                    <strong></strong>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <input type="reset" value="Batal" class="btn btn-outline-secondary w-100"
                                           data-bs-dismiss="modal">
                                </div>
                                <div class="col">
                                    <input type="submit" value="Simpan Data" class="btn btn-primary w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="form-delete" class="mainForm">
        <div class="modal modal-blur fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true"
             data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-header ">
                        <div class="modal-title">
                            Hapus Data
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-capitalize text-center py-4">
                        <span class="ri-delete-bin-line ri-3x"></span>
                        <h4>Hapus Master kelas?</h4>
                        <div class="">
                            anda yakin akan menghapus data master kelas?
                        </div>
                    </div>
                    <div class="modal-body py-4">
                        <fieldset class="form-fieldset">
                            <div class="mb-3 row">
                                <label for="kelompok" class="col-sm-4 col-form-label form-label-sm">Sekolah</label>
                                <div class="col">
                                    <input type="text" readonly class="form-control  form-control-sm" id="kelompok"
                                           name="kelompok">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="unit" class="col-sm-4 col-form-label form-label-sm">Unit</label>
                                <div class="col">
                                    <input type="text" readonly class="form-control  form-control-sm" id="unit"
                                           name="unit">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="jenjang" class="col-sm-4 col-form-label form-label-sm">Kelas</label>
                                <div class="col">
                                    <input type="text" readonly class="form-control  form-control-sm" id="jenjang"
                                           name="jenjang">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="kelas" class="col-sm-4 col-form-label form-label-sm">Kelompok</label>
                                <div class="col">
                                    <input type="text" readonly class="form-control  form-control-sm" id="kelas" name="kelas">
                                </div>
                            </div>
                        </fieldset>
                        <input type="hidden" id="delete_id" name="item_id" value="">
                    </div>
                    <div class="modal-footer ">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <input type="reset" class="btn btn-outline-secondary w-100" value="Batal"
                                           data-bs-dismiss="modal">
                                </div>
                                <div class="col">
                                    <input type="submit" value="Hapus" class="btn btn-danger w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        let select2 = document.querySelector(`[data-control='select2']`);

        const dtOptions = {
            tableId: 'main_table',
            formId: false,
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
        };

        const modalCreateElement = document.getElementById('modal-create');
        const modalCreate = new bootstrap.Modal(modalCreateElement);

        const modalDeleteElement = document.getElementById('modal-delete');
        const modalDelete = new bootstrap.Modal(modalDeleteElement);

        function isEmpty(val) {
            return val === false || val === undefined || val === null || val === '' || (Array.isArray(val) && val.length === 0)
        }

        document.querySelectorAll(".mainForm").forEach(form => {
           form.addEventListener('submit',function (e){
               e.preventDefault();
               switch(form.id){
                   case 'addForm':
                       submitForm('store');
                       break;
                   case 'form-delete':
                       submitForm('delete');
                       break;
                   default:
                       return;
               }
           })
        });

        document.querySelector(`#${dtOptions.tableId} tbody`).addEventListener('click', function (e) {
            if (e.target.closest('.btn-hapus')) {
                const rowEl = e.target.closest('tr');

                if (rowEl) {
                    const rowData = DT[`${dtOptions.tableId}`].row(rowEl).data();
                    Object.entries(rowData).forEach(([key, value]) => {
                        let input = document.querySelector(`#form-delete [name="${key.toLowerCase()}"]`);
                        if (input) {
                            input.value = value;
                        }
                    });
                    modalDelete.show();
                }
            }
        });

        document.querySelectorAll(".modal").forEach(modal => {
            modal.addEventListener('hide.bs.modal', function (e) {
                const form = modal.closest("form");
                if (!form) return;

                form.reset();
                clearErrorMessages(form.id);

                setTimeout(() => {
                    modal.querySelectorAll("[data-control='select2']").forEach(select => {
                        const event = new Event("change", { bubbles: true });
                        select.dispatchEvent(event);
                    });
                }, 0);
            });
        });

        function submitForm(form) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let request, item_id, user_id, url, data = null;
            switch (form) {
                case 'store':
                    loadingAlert('Menyimpan Master Kelas....');
                    url = '{{route('admin.master-data.master-kelas.store')}}';
                    const formCreate = document.querySelector('#addForm');
                    data = new FormData(formCreate);
                    console.log(data);
                    request = new Request(
                        url, {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                            }, body: data
                        });
                    break;
                case 'delete':
                    loadingAlert('Menghapus tagihan....');
                    item_id = document.getElementById('delete_id').value;
                    url = '{{route('admin.master-data.master-kelas.destroy',':id')}}'
                    url = url.replace(':id', item_id)

                    if(isEmpty(item_id)){
                        warningAlert('Data tidak valid, silahkan muat ulang halaman');
                        return;
                    }

                    request = new Request(
                        url, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            }, body: JSON.stringify({
                                // _method: 'DELETE',
                                user_id: user_id
                            })
                        });
                    break;
                default:
                    errorAlert('Data tidak valid!');
                    return;
            }

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
                    switch (form) {
                        case 'store':
                            modalCreate.hide();
                            break;
                        case 'delete':
                            modalDelete.hide();
                            break;
                    }
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

        document.addEventListener("DOMContentLoaded", function () {
            if (dtOptions.dataUrl && dtOptions.columnUrl) {
                getDT(dtOptions);
                if (dtOptions.formId) {
                    let filterForm = document.getElementById(`${dtOptions.formId}`);
                    filterForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        dataReload(dtOptions.tableId);
                    });
                    filterForm.addEventListener('reset', function (e) {
                        setTimeout(function () {
                            dataReload(dtOptions.tableId);
                            const select2InForm = select2.filter(`#${dtOptions.formId} [data-control='select2']`);
                            if (select2InForm.length) {
                                select2InForm.each(function () {
                                    let $this = $(this);
                                    $this.trigger('change');
                                });
                            }
                            dataReFilter(dtOptions.tableId);
                        }, 0)
                    });
                }
            }

            document.querySelectorAll("[data-control='select2']").forEach(select => {
                let wrapper = document.createElement("div");
                wrapper.classList.add("position-relative");
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);
                $(select).select2({
                    placeholder: "Pilih satu",
                    language: "id",
                    dropdownParent: $(wrapper)
                });
            });
        });
    </script>

@endsection
