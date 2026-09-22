@php use Carbon\Carbon; @endphp
<meta name="csrf-token" content="{{ csrf_token() }}" xmlns="http://www.w3.org/1999/html">
@php
    $year = Carbon::now()->format('Y');
@endphp
<form id="deleteForm" class="mainForm">
    <div class="modal modal-blur fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-danger"></div>
                <div class="modal-header ">
                    <div class="modal-title">
                        Hapus Data
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-capitalize text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="84" height="84" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7l16 0"/>
                        <path d="M10 11l0 6"/>
                        <path d="M14 11l0 6"/>
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                    </svg>
                    <h3>Hapus Master Siswa?</h3>
                    <div class="">
                        anda yakin akan menghapus data Master Siswa?
                    </div>

                    <input type="hidden" id="delete_id" name="delete_id" value="12">
                </div>
                <div class="modal-footer ">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <input type="reset" class="btn btn-outline-secondary w-100" value="Batal"
                                       data-bs-dismiss="modal">
                            </div>
                            <div class="col">
                                <input type="submit" value="Hapus Data" class="btn btn-danger w-100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="editForm" class="mainForm">
    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" role="dialog" aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-warning"></div>
                <div class="modal-header">
                    <div class="modal-title">
                        Edit Data Master Siswa
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-capitalize py-2">
                    <fieldset class="form-fieldset">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="edit-nis">Nomor Induk Siswa</label>
                                    <input type="text" class="form-control numberOnly" name="nis" id="edit-nis"
                                           autocomplete="off"
                                           placeholder="Nomor Induk Siswa" required>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit-no_pendaftaran">Nomor Pendaftaran
                                        Siswa</label>
                                    <input type="text" class="form-control numberOnly" name="no_pendaftaran"
                                           id="edit-no_pendaftaran"
                                           autocomplete="off"
                                           placeholder="Nomor Pendaftaran Siswa">
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label required" for="edit-nama">Nama Siswa</label>--}}
{{--                                    <input type="text" class="form-control" name="nama" id="edit-nama"--}}
{{--                                           autocomplete="off"--}}
{{--                                           placeholder="Nama Siswa" required>--}}
{{--                                    <div class="invalid-feedback" role="alert">--}}
{{--                                        <strong></strong>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="mb-3">
                                    <label class="form-label" for="edit-jk">Jenis Kelamin</label>
                                    <select type="text" class="form-select" name="jk" id="edit-jk" autocomplete="off"
                                            data-placeholder="Jenis Kelamin">
                                        <option value="0">Perempuan</option>
                                        <option value="1">Laki-Laki</option>
                                    </select>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required" for="edit-id_kelas">Kelas Siswa</label>
                                    <select type="text" class="form-select" name="id_kelas" id="edit-id_kelas"
                                            autocomplete="off"
                                            placeholder="Kelas" required>
                                        @isset($kelas)
                                            @foreach($kelas as $item)
                                                <option
                                                    value="{{$item->id}}">{{$item->unit}}  -  {{$item->kelas}} {{$item->kelompok}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
{{--                                <div class="mb-3">--}}
{{--                                    <label class="form-label required" for="edit-id_thn_aka">Angkatan</label>--}}
{{--                                    <select class="form-select" data-control="select2" name="id_thn_aka"--}}
{{--                                            id="edit-id_thn_aka"--}}
{{--                                            data-placeholder="Angkatan" required>--}}
{{--                                        @isset($angkatan)--}}
{{--                                            @foreach($angkatan as $item)--}}
{{--                                                <option value="{{$item->id}}">{{$item->thn_aka}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @else--}}
{{--                                            <option>data kosong</option>--}}
{{--                                        @endisset--}}
{{--                                    </select>--}}
{{--                                    <div class="invalid-feedback" role="alert">--}}
{{--                                        <strong></strong>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="edit-nowa">Nomor WhatsApp</label>
                                    <input type="text" class="form-control numberOnly" id="edit-nowa" name="nowa"
                                           placeholder="Nomor WhatsApp" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit-nama_ortu">Nama Orang Tua/Wali</label>
                                    <input type="text" class="form-control" id="edit-nama_ortu" name="nama_ortu"
                                           placeholder="Nama Orang Tua/Wali">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="edit-alamat">Alamat</label>
                                    <textarea type="text" class="form-control" name="alamat" id="edit-alamat"
                                              autocomplete="off"
                                              placeholder="Alamat"></textarea>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
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
                                <input type="submit" value="Simpan Data" class="btn btn-warning w-100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="addForm" class="mainForm">
    <div class="modal modal-blur fade" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Data Master Siswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <fieldset class="form-fieldset">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="nis">Nomor Induk Siswa</label>
                                    <input type="text" class="form-control numberOnly" name="nis" id="nis"
                                           autocomplete="off"
                                           placeholder="Nomor Induk Siswa" required>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="no_pendaftaran">Nomor Pendaftaran
                                        Siswa</label>
                                    <input type="text" class="form-control numberOnly" name="no_pendaftaran"
                                           id="no_pendaftaran"
                                           autocomplete="off"
                                           placeholder="Nomor Pendaftaran Siswa">
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required" for="nama">Nama Siswa</label>
                                    <input type="text" class="form-control" name="nama" id="nama" autocomplete="off"
                                           placeholder="Nama Siswa" required>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="jk">Jenis Kelamin</label>
                                    <select type="text" class="form-select" name="jk" id="jk" autocomplete="off"
                                            placeholder="Jenis Kelamin">
                                        <option value="0">Perempuan</option>
                                        <option value="1">Laki-Laki</option>
                                    </select>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required" for="id_kelas">Kelas Siswa</label>
                                    <select type="text" class="form-select" name="id_kelas" id="id_kelas"
                                            autocomplete="off"
                                            placeholder="Kelas" required>
                                        @isset($kelas)
                                            @foreach($kelas as $item)
                                                <option
                                                    value="{{$item->id}}">{{$item->kelas}} {{$item->kelompok}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required" for="id_thn_aka">Angkatan</label>
                                    <select class="form-select" data-control="select2" name="id_thn_aka" id="id_thn_aka"
                                            data-placeholder="Angkatan" required>
                                        @isset($angkatan)
                                            @foreach($angkatan as $item)
                                                <option value="{{$item->id}}">{{$item->thn_aka}}</option>
                                            @endforeach
                                        @else
                                            <option>data kosong</option>
                                        @endisset
                                    </select>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="nowa">Nomor WhatsApp</label>
                                    <input type="text" class="form-control numberOnly" id="nowa" name="nowa"
                                           placeholder="Nomor WhatsApp" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="nama_ortu">Nama Orang Tua/Wali</label>
                                    <input type="text" class="form-control" id="nama_ortu" name="nama_ortu"
                                           placeholder="Nama Orang Tua/Wali">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="alamat">Alamat</label>
                                    <textarea type="text" class="form-control" name="alamat" id="alamat"
                                              autocomplete="off"
                                              placeholder="Alamat"></textarea>
                                    <div class="invalid-feedback" role="alert">
                                        <strong></strong>
                                    </div>
                                </div>
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

<link rel="stylesheet" href="{{asset('main/libs/select2/select2.css')}}">
<script src="{{asset('main/libs/select2/select2.js')}}"></script>

<script>
    let id_action = '';

    function clearErrorMessages(formId) {
        const form = document.querySelector(`#${formId}`);
        const errorElements = form.querySelectorAll('.invalid-feedback');
        const errorClass = form.querySelectorAll('.is-invalid');

        errorElements.forEach(element => element.textContent = '');
        errorClass.forEach(element => element.classList.remove('is-invalid'));
    }

    function processErros(errors){
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

    document.addEventListener("DOMContentLoaded", function () {
        $(document).on('keypress', '.numberOnly', function (e) {
            const charCode = e.which ? e.which : e.keyCode;
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
            }
        })

        $(document).on('keypress', '.formattedNumber', function (e) {
            const charCode = e.which ? e.which : e.keyCode;
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
            }
        })

        $(document).on('input', '.formattedNumber', function (e) {
            const formattedValue = $(this).val();
            const parsedNumber = parseInt(formattedValue.replace(/\./g, ''));

            if (!isNaN(parsedNumber)) {
                const formattedString = parsedNumber.toLocaleString('id-ID');
                $(this).val(formattedString);
            } else {

            }
        });

        $('.mainForm').on('submit', function (e) {
            e.preventDefault()
            let url
            let tipe
            const formId = $(this).attr('id');
            let data = $(this).serialize();

            if (formId === "deleteForm") {
                loadingAlert('Menghapus data siswa');
                url = '{{route('admin.master-data.data-siswa.destroy',':id')}}'
                url = url.replace(':id', id_action)
                tipe = 'DELETE';
            } else if (formId === "editForm") {
                loadingAlert('Mengubah data siswa');
                url = '{{route('admin.master-data.data-siswa.update',':id')}}'
                url = url.replace(':id', id_action)
                tipe = 'PUT';
            } else if (formId === "addForm") {
                loadingAlert('Menyimpan data siswa');
                url = '{{route('admin.master-data.data-siswa.store')}}'
                tipe = 'POST';
            }
            {{--else if (formId === "formImport") {--}}
            {{--                    url = '{{route('admin.master-data.tahun-akademik.import')}}'--}}
            {{--        tipe = 'POST';--}}
            {{--    data = new FormData(this);--}}
            {{--}--}}

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
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

            if (formId === "formImport") {
                ajaxOptions.contentType = false;
                ajaxOptions.processData = false;
            }

            // console.log(ajaxOptions)
            clearErrorMessages(formId)
            $.ajax(ajaxOptions).done(function (responses) {
                document.getElementById(formId).reset();
                successAlert(responses.message);
                dataReload('main_table');
                $("#" + $(e.target).attr('id')).find('[data-bs-dismiss="modal"]').trigger('click')
            }).fail(function (xhr) {
                if (xhr.status === 422) {
                    const response = JSON.parse(xhr.responseText);
                    const error = response.error;
                    const errors = response.errors;
                    const errMessage = response.message || xhr.responseJSON.message;
                    errorAlert(errMessage);
                    if (error) {
                        processErros(error);
                    } else if (errors) {
                        processErros(errors);
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
        })

        $('#modal-create').on('show.bs.modal', function (e) {
            const formId = this.id
            $(this).parent().trigger('reset')
            // initTomSelect('tipe')
            clearErrorMessages(formId)
        })

        $("input[name=fileImport]").change(function () {
            let filename = this.files[0].name;
            $('#filename').text("File: " + filename);
        });

        $('#modal-import').on('hidden.bs.modal', function () {
            file.removeFiles()
        })

        $('#modal-delete').on('show.bs.modal', function (e) {
            let data = $(e.relatedTarget).data('val')
            id_action = data.item_id;
            $("#delete_id").val(id_action)
        })

        $('#modal-edit').on('show.bs.modal', function (e) {
            const formId = $(this).parent().attr('id');
            clearErrorMessages(formId);
            let data = $(e.relatedTarget).data('val');
            id_action = data.item_id;
            $.each(data, function (key, value) {
                let input = $("#edit-" + key);
                input.val(value);
            });
        })

        $('#modal-detail').on('show.bs.modal', function (e) {
            let data = $(e.relatedTarget).data('val')

            $.each(data, function (key, value) {
                let input = $("#detail-" + key);
                input.val(value);
            });
        })

        $('#angkatan').select2({
            dropdownParent: $('#modal-create')
        });

        $('#edit-angkatan').select2({
            dropdownParent: $('#modal-edit')
        });
        // initTomSelect('tipe')
        // initTomSelect('edit-tipe')
    })
</script>
