<!-- Modal Add -->
<div class="fade modal" id=modalForAdd data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class=modal-content>
            <div class=modal-header>
                <h1 class="fs-5 modal-title" id=modalForAddLabel>Tambah Data User</h1>
                <button class=btn-close type=button onclick=closeModalAdd()></button>
            </div>
            <form class="w-100 d-flex align-items-center flex-column gap-3" style=overflow-y:auto method=POST action=/users/add>@csrf
                <div class="w-100 d-flex align-items-center flex-column modal-body">
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=add_name name=name placeholder="Nama anda" maxlength=255 required>
                        <label for=add_name style="width:calc(100% - 20px)">Nama<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=add_username name=username placeholder="Nama anda" maxlength=255 required autocomplete="username">
                        <label for=add_username style="width:calc(100% - 20px)">Username<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 mb-3 rounded">
                        <label class=form-label for=tipe>Jenis Kelamin</label>
                        <div class=input-group>
                            <select class="form-control" id=add_sex name=sex required>
                                <option value=Laki-laki>Laki-Laki</option>
                                <option value=Perempuan>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="w-100 mb-3 rounded">
                        <label class=form-label for=role>Pilih Role</label>
                        <div class=input-group>
                            <select class="form-control" id=add_role name=role required>
                                <option value=Admin>Admin</option>
                                <option value=Pegawai>Pegawai</option>
                                <option value=Boss>Boss</option>
                            </select>
                        </div>
                    </div>
                    <div class="w-100 mb-3 rounded">
                        <label class="form-label w-100" for="proyek">Pilih Proyek</label>
                        <select class="form-control w-100" id="add_proyek" name="proyek[]" style="width: 100%" multiple="multiple">
                            @foreach ($proyeks as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-1"></div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=add_phone name=phone placeholder="Nama anda" maxlength=255 required>
                        <label for=add_phone style="width:calc(100% - 20px)">Phone<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=add_email name=email type=email placeholder="Nama anda" maxlength=255 required>
                        <label for=add_email style="width:calc(100% - 20px)">Email<span class="fw-bold text-danger">*</span></label>
                    </div>
                    <div class="w-100 form-floating mb-3 rounded">
                        <input class="border-dark-subtle border form-control" id=add_password name=password type=password placeholder="Password anda" minlength=8 autocomplete="new-password">
                        <label for=add_password style="width:calc(100% - 20px)">Password</label>
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

@push('scripts_4')
    <script>
        $(document).ready(function() {
            $('#add_sex').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#add_role').select2({
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
            }).on("select2:select", function(e) {
                $(this).select2('open');
            }).on("select2:unselect", function(e) {
                $(this).select2('open');
            });
        });

        function closeModalAdd() {
            $('#modalForAdd').modal('hide');
        }

        function showModalAdd() {
            $('#modalForAdd').modal('show');
        }
    </script>
@endpush
