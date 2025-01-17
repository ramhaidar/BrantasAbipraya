<!-- Modal Edit -->
<div class="fade modal" id=modalForEdit data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class=modal-content>
            <div class=modal-header>
                <h1 class="fs-5 modal-title" id=modalForEditLabel>Ubah Data User</h1>
                <button class=btn-close data-bs-dismiss="modal" type=button></button>
            </div>
            <form class="w-100 d-flex align-items-center flex-column gap-3" style=overflow-y:auto method=POST>@csrf
                <div class="w-100 d-flex flex-column modal-body">
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=edit_name name=name placeholder="Nama anda" maxlength=255 required>
                        <label for=edit_name style="width:calc(100% - 20px)">Nama<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=edit_username name=username placeholder="Nama anda" maxlength=255 required autocomplete="username">
                        <label for=edit_username style="width:calc(100% - 20px)">Username<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 mb-3 rounded">
                        <label class=form-label for=sex>Jenis Kelamin</label>
                        <div class=input-group>
                            <select class="form-control" id=edit_sex name=sex required>
                                <option value=Laki-laki>Laki-Laki</option>
                                <option value=Perempuan>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="w-100 mb-3 rounded">
                        <label class=form-label for=role>Pilih role</label>
                        <div class=input-group>
                            <select class="form-control" id=edit_role name=role required>
                                <option value=Admin>Admin</option>
                                <option value=Pegawai>Pegawai</option>
                                <option value=Boss>Boss</option>
                            </select>
                        </div>
                    </div>
                    <div class="w-100 mb-3 rounded">
                        <label class="form-label w-100" for="proyek">Pilih Proyek</label>
                        <select class="form-control w-100" id="edit_proyek" name="proyek[]" style="width: 100%" multiple="multiple">
                            @foreach ($proyeks as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=edit_phone name=phone placeholder="Nama anda" maxlength=255 required>
                        <label for=edit_phone style="width:calc(100% - 20px)">Phone<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=edit_email name=email type=email placeholder="Nama anda" maxlength=255 required>
                        <label for=edit_email style="width:calc(100% - 20px)">Email<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=edit_password name=password type=password placeholder="Password anda" minlength=8 autocomplete="current-password">
                        <label for=edit_password style="width:calc(100% - 20px)">Password</label>
                    </div>
                </div>
                <div class="w-100 d-flex justify-content-between modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss=modal type=button>Batal</button>
                    <button class="btn btn-primary" type=submit>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
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
                dropdownParent: $('#modalForEdit')
            }).on("select2:select select2:unselect", function() {
                $(this).select2('open');
            });

            // Modal show/hide functions
            $('#modalForEdit').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });

        // Fetch user data and fill form
        function fillFormEdit(userId) {
            document.querySelector('#modalForEdit form').action = `/users/edit/${userId}`;
            fetchUserData(userId)
                .then(data => {
                    $('#edit_name').val(data.name);
                    $('#edit_username').val(data.username);
                    $('#edit_sex').val(data.sex).trigger('change');
                    $('#edit_role').val(data.role).trigger('change');
                    $('#edit_phone').val(data.phone);
                    $('#edit_email').val(data.email);

                    let proyekIds = data.proyek.map(proyek => proyek.id);
                    $('#edit_proyek').val(proyekIds).trigger('change');
                })
                .catch(error => {
                    console.error("Error fetching user data:", error);
                    showSweetAlert2("Failed to load user data. Please try again.", 'error');
                });
        }

        // Fetch user data with AJAX
        function fetchUserData(userId) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `/users/${userId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: resolve,
                    error: (xhr, status, error) => reject(error)
                });
            });
        }
    </script>
@endpush
