@extends('layouts.app')

@push('styles_2')
    <style>
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

        @media only screen and (max-width: 768.7px) {
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
            <div class="mt-0 ibox-body table-responsive">
                <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style=width:100%>
                    <thead class=table-primary>
                        <tr>
                            <th scope=col>Nama Proyek</th>
                            <th scope=col>Detail</th>
                            <th scope=col></th>
                        </tr>
                    </thead>
                    <tbody id=body-table>
                        @foreach ($proyeks as $proyek)
                            <tr>
                                <td scope=col>{{ $proyek->nama_proyek }}</td>
                                <td class="m-0 p-0">
                                    <button class="btn text-primary m-0 ps-2" onclick='getDetailProyek("{{ $proyek->id }}")'>Detail</button>
                                </td>
                                <td class=center scope=col>
                                    <button class="btn btn-danger" data-bs-target=#modalForDelete data-bs-toggle=modal onclick="validationSecond({{ $proyek->id }},'{{ $proyek->nama_proyek }}')"><i class="bi bi-trash3"></i></button>
                                    <a class="btn btn-warning ms-3" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $proyek->id }})"><i class="bi bi-pencil-square"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalDetailProyek" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalDetailProyekLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold" id="modalDetailProyekLabel">Nilai</h1>
                    <button class="btn-close" type="button" onclick="closeModalProyek()"></button>
                </div>
                <div class="modal-body">
                    <ol class="list-group" id="list-users">
                        <li class="list-group-item">Placeholder</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="fade modal" id=modalForEdit data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title" id=modalForEditLabel>Ubah Data Proyek</h1>
                    <button class=btn-close type=button onclick=closeModalEdit()></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-3" style=overflow-y:auto method=POST>
                    @csrf
                    <div class="w-100 d-flex align-items-center flex-column modal-body">
                        <div class="w-100 form-floating mb-3 rounded">
                            <input class="border-dark-subtle border form-control" id=nama_proyek name=nama_proyek placeholder="Nama Proyek" required>
                            <label for=nama_proyek style="width:calc(100% - 20px)">Nama Proyek<span class="fw-bold text-danger">*</span></label>
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
    <div class="fade modal" id=modalForAdd data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title" id=modalForEditLabel>Tambah Data Proyek</h1>
                    <button class=btn-close type=button onclick=closeModalAdd()></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-3" style="overflow-y:auto" method="POST" action="{{ route('proyek.store') }}">
                    @csrf
                    <div class="w-100 d-flex align-items-center flex-column modal-body">
                        <div class="w-100 form-floating mb-3 rounded">
                            <input class="border-dark-subtle border form-control" id="nama_proyek" name="nama_proyek" placeholder="Nama Proyek" required>
                            <label for="nama_proyek" style="width:calc(100% - 20px)">Nama Proyek<span class="fw-bold text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="w-100 d-flex justify-content-between modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="fade modal" id=modalForDelete data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title" id=staticBackdropLabel>Form Konfirmasi</h1>
                    <button class=btn-close type=button onclick=closeModalDelete()></button>
                </div>
                <form method=POST>
                    @csrf @method('DELETE')
                    <div class=modal-body>
                        <div class=form-group>
                            <div class="mb-3 mt-3">
                                <p class="fw-bold form-label gap-0" for=nama_proyek required>Ketik Ulang "
                                <p class="m-0 text-primary" id=model-konfirmasi></p>"</p>
                                <input class="form-control border-dark" id=nama_proyek name=nama_proyek required>
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
        $('#table-data').DataTable({
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
            order: []
        });

        function closeModalProyek() {
            $('#modalDetailProyek').modal('hide');
        }

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
            document.querySelector('#modalForDelete form').action = `/proyek/delete/${id}`;
        };

        document.querySelector('#modalForDelete form').addEventListener('submit', function(event) {
            var confirmationText = document.getElementById('model-konfirmasi').innerText.trim();
            var inputName = document.querySelector('#modalForDelete #name');
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
            document.querySelector('#modalForEdit form').action = `/proyek/edit/${params}`;
            getProyek(params)
                .then(data => {
                    document.querySelector('#nama_proyek').value = data.nama_proyek;
                })
                .catch(error => {
                    showSweetAlert2(error, 'error');
                });
        }

        function getProyek(params) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "/proyek/" + params,
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

        function getDetailProyek(params) {
            getProyek(params)
                .then(data => {
                    // Kosongkan list yang ada sebelumnya menggunakan ID yang spesifik
                    const listGroup = $('#list-users');
                    listGroup.empty();

                    // Cek apakah array users kosong
                    if (data.users.length === 0) {
                        const noUsersItem = $('<li class="list-group-item"></li>').text("Belum ada User pada Proyek ini.");
                        listGroup.append(noUsersItem);
                    } else {
                        // Isi list dengan data baru, dengan menambahkan angka urut di depan nama
                        data.users.forEach((user, index) => {
                            const listItem = $('<li class="list-group-item"></li>').text(`${index + 1}. ${user.name}`);
                            listGroup.append(listItem);
                        });
                    }

                    // Tampilkan modal
                    $('#modalDetailProyek').modal('show');
                })
                .catch(error => {
                    // Sembunyikan modal jika terjadi error
                    $('#modalDetailProyek').modal('hide');
                    showSweetAlert2(error, 'error');
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
        </script>
        @foreach ($errors->all() as $error)
            <script>
                errInput += "<li>{{ $error }}</li>"
            </script>
        @endforeach
        <script>
            errInput += "</ul>", showSweetAlert2(errInput, "error")
        </script>
    @endif
@endpush
