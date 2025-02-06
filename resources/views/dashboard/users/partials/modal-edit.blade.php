@push('styles_3')
@endpush

<!-- Modal Edit -->
<div class="fade modal" id="modalForEdit" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Data User</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editUserForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="edit_name">Nama</label>
                            <input class="form-control" id="edit_name" name="name" type="text" placeholder="Nama" required>
                            <div class="invalid-feedback">Nama diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="edit_username">Username</label>
                            <input class="form-control" id="edit_username" name="username" type="text" placeholder="Username" required>
                            <div class="invalid-feedback">Username diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="edit_sex">Jenis Kelamin</label>
                            <select class="form-control" id="edit_sex" name="sex" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">Jenis Kelamin diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="edit_role">Role</label>
                            <select class="form-control" id="edit_role" name="role" required>
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
                            <label class="form-label" for="edit_proyek">Proyek</label>
                            <select class="form-control w-100" id="edit_proyek" name="proyek[]" multiple="multiple">
                                @foreach ($proyeks as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="edit_phone">Phone</label>
                            <input class="form-control" id="edit_phone" name="phone" type="text" placeholder="Phone" required minlength="8">
                            <div class="invalid-feedback">Nomor telepon minimal 8 karakter.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="edit_email">Email</label>
                            <input class="form-control" id="edit_email" name="email" type="email" placeholder="Email" required>
                            <div class="invalid-feedback">Email diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="edit_password">Password</label>
                            <input class="form-control" id="edit_password" name="password" type="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="8">
                            <div class="invalid-feedback">Password minimal 8 karakter.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-user" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            const editForm = document.querySelector('#editUserForm');

            editForm.addEventListener('submit', (event) => {
                if (!editForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                editForm.classList.add('was-validated');
            });

            editForm.querySelectorAll('input, select').forEach((input) => {
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
            $('#edit_sex, #edit_role').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#edit_proyek').select2({
                placeholder: "Pilih Proyek",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 0,
                dropdownParent: $('#modalForEdit'),
                width: '100%'
            }).on("select2:select select2:unselect", function() {
                $(this).select2('open');
            });
        })();

        function fillFormEdit(id) {
            $.ajax({
                url: `/users/${id}`,
                type: 'GET',
                success: function(response) {
                    $('#editUserForm #edit_name').val(response.name);
                    $('#editUserForm #edit_username').val(response.username);
                    $('#editUserForm #edit_sex').val(response.sex).trigger('change');
                    $('#editUserForm #edit_role').val(response.role).trigger('change');
                    $('#editUserForm #edit_phone').val(response.phone);
                    $('#editUserForm #edit_email').val(response.email);

                    let proyekIds = response.proyek.map(proyek => proyek.id);
                    $('#editUserForm #edit_proyek').val(proyekIds).trigger('change');

                    $('#editUserForm').attr('action', `/users/edit/${id}`);
                    $('#modalForEdit').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                }
            });
        }
    </script>
@endpush
