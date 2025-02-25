@push('styles_3')
@endpush

<!-- Modal Add -->
<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data User</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="userForm" style="overflow-y: auto" novalidate method="POST" action="/users/add">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="add_name">Nama</label>
                            <input class="form-control" id="add_name" name="name" type="text" placeholder="Nama" required>
                            <div class="invalid-feedback">Nama diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="add_username">Username</label>
                            <input class="form-control" id="add_username" name="username" type="text" placeholder="Username" required>
                            <div class="invalid-feedback">Username diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="add_sex">Jenis Kelamin</label>
                            <select class="form-control" id="add_sex" name="sex" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">Jenis Kelamin diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="add_role">Role</label>
                            <select class="form-control" id="add_role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="superadmin">Super Admin</option>
                                <option value="svp">SVP</option>
                                <option value="vp">VP</option>
                                <option value="admin_divisi">Admin Divisi</option>
                                <option value="koordinator_proyek">Koordinator Proyek</option>
                            </select>
                            <div class="invalid-feedback">Role diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="add_proyek">Proyek</label>
                            <select class="form-control w-100" id="add_proyek" name="proyek[]" multiple="multiple">
                                @foreach ($proyeks as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="add_phone">Phone</label>
                            <input class="form-control" id="add_phone" name="phone" type="text" placeholder="Phone" required minlength="8">
                            <div class="invalid-feedback">Nomor telepon minimal 8 karakter.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="add_email">Email</label>
                            <input class="form-control" id="add_email" name="email" type="email" placeholder="Email" required>
                            <div class="invalid-feedback">Email diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="add_password">Password</label>
                            <input class="form-control" id="add_password" name="password" type="password" placeholder="Password" required minlength="8">
                            <div class="invalid-feedback">Password minimal 8 karakter diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" id="add-user" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            const form = document.querySelector('#userForm');

            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    $('#add-user').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                }
                form.classList.add('was-validated');
            });

            form.querySelectorAll('input, select').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });

            // Initialize Select2
            $('#add_sex, #add_role').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#add_proyek').select2({
                placeholder: "Pilih Proyek",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 0,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            }).on("select2:select select2:unselect", function() {
                $(this).select2('open');
            });

            $('#modalForAdd').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $(this).find('form').removeClass('was-validated');
                $('#add_sex, #add_role, #add_proyek').val(null).trigger('change');
            });
        })()
    </script>
@endpush
