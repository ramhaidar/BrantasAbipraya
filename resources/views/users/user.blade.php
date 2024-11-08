@extends('layouts.app')

@push('styles_2')
    <style>
        /* Existing styles */
        td {
            white-space: nowrap;
        }

        .alert-custom-css {
            max-width: 400px;
            width: 90%;
        }

        #form-group {
            width: 90%;
        }

        .space-nowrap {
            white-space: nowrap;
        }

        .center {
            text-align: center !important;
        }

        .custom-confirm-delete {
            margin-right: 5%;
        }

        .custom-cancel-delete {
            margin-left: 5%;
        }

        .custom-action-delete {
            width: 100% !important;
            justify-content: space-between;
        }

        @media screen and (max-width: 500px) {
            #button-for-modal-add span {
                display: none;
            }

            #button-for-modal-add {
                font-size: 20px;
            }
        }

        /* DataTables and layout-specific styles */
        .ibox {
            position: relative;
            margin-bottom: 25px;
            padding: 20px 20px 0px 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, .2);
        }

        .ibox .ibox-head {
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 50px;
        }

        .ibox .ibox-body {
            padding: 15px 20px 20px 20px;
        }

        #table-data_wrapper {
            display: flex;
            flex-wrap: wrap;
            flex-direction: column;
            gap: 15px;
            margin-top: 5px;
        }

        #table-data {
            padding-left: 20px;
            padding-right: 20px;
        }

        #table-data th {
            white-space: nowrap;
        }

        .dataTables_length {
            display: flex;
        }

        #table-data_filter {
            display: flex;
            justify-content: flex-end;
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
        }

        .sorting:hover {
            cursor: pointer;
        }

        .table-responsive {
            width: 100%;
            position: relative;
            overflow-x: hidden;
        }

        .table-responsive .row:first-child {
            width: calc(100% + 22px);
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 2;
        }

        .table-responsive .row:nth-child(2) {
            width: calc(100% + 22px);
            overflow-x: auto;
        }

        .table-responsive .row:nth-child(2) .col-sm-12 {
            padding-left: 0px;
            padding-right: 0px;
        }

        .table-responsive .row:nth-child(3) {
            width: calc(100% + 22px);
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 2;
        }

        #table-data_wrapper .row:first-child:first-child .col-sm-12.col-md-6:first-child {
            padding: 0px;
        }

        @media only screen and (max-width: 768px) {
            .row:first-child {
                justify-content: space-between;
                gap: 10px;
            }

            .col-sm-12.col-md-5 {
                display: none;
            }

            .col-sm-12.col-md-6:first-child {
                min-width: 200px;
            }

            .col-sm-12.col-md-6:nth-child(2) {
                min-width: 280px;
                padding-left: 0px;
            }

            .col-sm-12.col-md-6 {
                width: 45%;
                padding-right: 0px;
            }

            #table-data_filter {
                justify-content: flex-start;
            }
        }

        @media only screen and (max-width: 626px) {
            #table-data_filter {
                justify-content: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="fade-in-up page-content">
        <div class=ibox>
            <div class=ibox-head>
                <div class=ibox-title>{{ $page }}</div><a class="btn btn-primary btn-sm" id=button-for-modal-add onclick=showModalAdd()><i class="fa fa-plus"></i> <span class=ms-2>Tambah Data</span></a>
            </div>
            <div class="ibox-body table-responsive">
                <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style=width:100%>
                    <thead class=table-primary>
                        <tr>
                            <th scope=col>Name</th>
                            <th scope=col>Username</th>
                            <th scope=col>Jenis Kelamin</th>
                            <th scope=col>Role</th>
                            <th scope=col>Phone</th>
                            <th scope=col>Email</th>
                            <th scope=col></th>
                        </tr>
                    </thead>
                    <tbody id=body-table>
                        @foreach ($users as $user)
                            <tr>
                                <td scope=col>{{ $user->name }}</td>
                                <td scope=col>{{ $user->username }}</td>
                                <td scope=col>{{ $user->sex }}</td>
                                <td scope=col>{{ $user->role }}</td>
                                <td scope=col>{{ $user->phone }}</td>
                                <td scope=col>{{ $user->email }}</td>
                                <td class=center scope=col>
                                    <button class="btn btn-danger" data-bs-target=#modalForDelete data-bs-toggle=modal onclick="validationSecond({{ $user->id }},'{{ $user->name }}')"><i class="bi bi-trash3"></i></button>
                                    <a class="btn btn-warning ms-3" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $user->id }})"><i class="bi bi-pencil-square"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="fade modal" id=modalForEdit data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title" id=modalForEditLabel>Ubah Data User</h1>
                    <button class=btn-close type=button onclick=closeModalEdit()></button>
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
                                @foreach ($proyek as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_proyek }}</option>
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
                                @foreach ($proyek as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_proyek }}</option>
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

    <!-- Modal Delete -->
    <div class="fade modal" id=modalForDelete data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title" id=staticBackdropLabel>Form Konfirmasi</h1>
                    <button class=btn-close type=button onclick=closeModalDelete()></button>
                </div>
                <form method=POST>@csrf @method('DELETE')
                    <div class=modal-body>
                        <div class=form-group>
                            <div class="mb-3 mt-3">
                                <p class="fw-bold form-label gap-0" for=confirm_name required>Ketik Ulang "
                                <p class="m-0 text-primary" id=model-konfirmasi></p>"</p>
                                <input class="form-control border-dark" id=confirm_name name=name required>
                            </div>
                        </div>
                    </div>
                    <div class=modal-footer><a class="btn btn-secondary" onclick=closeModalDelete()>Batal</a>
                        <button class="btn btn-danger" type=submit>Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts_2')
    <script>
        $(document).ready(function() {
            new DataTable('#table-data', {
                language: {
                    paginate: {
                        previous: '<i class="bi bi-caret-left"></i>',
                        next: '<i class="bi bi-caret-right"></i>'
                    }
                },
                pageLength: -1,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [], // Prevents default sorting on any column
            });
            // $('#table-data').DataTable({
            // language: {
            //     paginate: {
            //         previous: '<i class="bi bi-caret-left"></i>',
            //         next: '<i class="bi bi-caret-right"></i>'
            //     }
            // },
            // pageLength: -1,
            // lengthMenu: [
            //     [10, 25, 50, -1],
            //     [10, 25, 50, "All"]
            // ],
            // order: [], // Prevents default sorting on any column
            // });


            $('#edit_sex').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit'),
            });

            $('#add_sex').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#edit_role').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#add_role').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $(document).ready(function() {
                $('#edit_proyek').select2({
                    placeholder: "Pilih Proyek",
                    allowClear: true,
                    closeOnSelect: false,
                    minimumResultsForSearch: 0,
                    dropdownParent: $('#modalForEdit'),
                }).on("select2:select", function(e) {
                    $(this).select2('open');
                }).on("select2:unselect", function(e) {
                    $(this).select2('open');
                });
            });

            $(document).ready(function() {
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
        });

        function closeModalAdd() {
            $('#modalForAdd').modal('hide');
        }

        function showModalAdd() {
            $('#modalForAdd').modal('show');
        }

        function closeModalEdit() {
            $('#modalForEdit').modal('hide');
        }

        function showModalEdit() {
            $('#modalForEdit').modal('show');
        }

        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        function showModalDelete() {
            $('#modalForDelete').modal('show');
        }

        const validationSecond = (id, name) => {
            document.querySelector('#model-konfirmasi').innerText = name;
            document.querySelector('#modalForDelete form').action = `/users/delete/${id}`;
        };

        document.querySelector('#modalForDelete form').addEventListener('submit', function(event) {
            var confirmationText = document.getElementById('model-konfirmasi').innerText.trim();
            var inputName = document.querySelector('#confirm_name');
            if (inputName.value.trim() !== confirmationText) {
                event.preventDefault();
                showSweetAlert2('Masukkan tidak sesuai. Silakan coba lagi!', 'error')
            }
        });

        function showSweetAlert2(msg, icon) {
            let title = '';
            if (icon == 'success') {
                title = 'Transaksi Berhasil!';
                msg = `Berhasil ${msg}.`;
            }
            Swal.fire({
                html: msg,
                icon: icon,
                confirmButtonText: 'Oke',
                customClass: {
                    popup: 'alert-custom-css'
                }
            });
        }

        function fillFormEdit(params) {
            document.querySelector('#modalForEdit form').action = `/users/edit/${params}`;
            getUser(params)
                .then(data => {
                    document.querySelector('#edit_name').value = data.name;
                    document.querySelector('#edit_username').value = data.username;
                    $('#edit_sex').val(data.sex).trigger('change'); // Pastikan nilai dari backend diset dan UI select2 diperbarui
                    $('#edit_role').val(data.role).trigger('change'); // Sama seperti di atas

                    document.querySelector('#edit_phone').value = data.phone;
                    document.querySelector('#edit_email').value = data.email;

                    let proyekIds = data.proyek.map(proyek => proyek.id); // Pastikan ini sesuai struktur data yang kamu terima
                    $('#edit_proyek').val(proyekIds).trigger('change'); // Memperbarui select2 dengan ID proyek yang telah dipilih
                })
                .catch(error => {
                    showSweetAlert2(error, 'error');
                });
        }

        function getUser(params) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "/users/" + params,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }
    </script>

    @if (session()->has('success'))
        <script>
            showSweetAlert2('{{ session('success') }}', 'success');
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            showSweetAlert2('{{ session('error') }}', 'error');
        </script>
    @endif

    @if ($errors->any())
        <script>
            let errInput = '<ul class="m-0 no-bullet">';
            @foreach ($errors->all() as $error)
                errInput += "<li>{{ $error }}</li>"
            @endforeach
            errInput += "</ul>";
            showSweetAlert2(errInput, "error")
        </script>
    @endif
@endpush
