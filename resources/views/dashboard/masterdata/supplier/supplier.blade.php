@extends('component.sidebar')
@section('css')
    <link href=/css/random-css-datatable.css rel=stylesheet>
    <style>
        .alert-custom-css {
            max-width: 400px;
            width: 90%
        }

        #form-group {
            width: 90%
        }

        .space-nowrap {
            white-space: nowrap
        }

        .center {
            text-align: center !important
        }

        .custom-confirm-delete {
            margin-right: 5%
        }

        .custom-cancel-delete {
            margin-left: 5%
        }

        .custom-action-delete {
            width: 100% !important;
            justify-content: space-between
        }

        @media screen and (max-width:500px) {
            #button-for-modal-add span {
                display: none
            }

            #button-for-modal-add {
                font-size: 20px
            }
        }
    </style>
@endsection
@section('content')
    <div class="fade-in-up page-content">
        <div class="ibox">
            {{-- check if user role is admin --}}
            <div class="ibox-head pe-0 ps-0">
                <div class="ibox-title">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</div>
                {{-- @if (Auth::user()->role == 'Pegawai') --}}
                    <a class="btn btn-primary btn-sm" id="button-for-modal-add" onclick="showModalAdd()">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a>
                {{-- @endif --}}
            </div>
            <div class="mt-3 ibox-body table-responsive">
                <table class="border-dark m-0 table table-bordered table-striped" id=table-data style=width:100%>
                    <thead class=table-primary>
                        <tr>
                            <th scope=col hidden>
                            <th scope=col>Supplier
                            <th scope=col>Detail
                            <th scope=col>
                    <tbody id=body-table>
                        @foreach ($proyeks as $proyek)
                            <tr>
                                <td scope=col hidden>
                                <td scope=col>{{ $proyek->nama_proyek }}
                                <td class="m-0 p-0">
                                    <button class="btn text-primary m-0 ps-2" onclick='getDetailProyek("{{ $proyek->id }}")'>Detail</button>
                                </td>
                                <td class=center scope=col>
                                    <button class="btn btn-danger" data-bs-target=#modalForDelete data-bs-toggle=modal onclick="validationSecond({{ $proyek->id }},'{{ $proyek->nama_proyek }}')"><i class="bi bi-trash3"></i></button>
                                    <a class="btn btn-warning ms-3" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $proyek->id }})"><i class="bi bi-pencil-square"></i></a>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
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

    <!-- Modal for Editing Data -->
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
@endsection

@section('script')
    <script>
        // Inisialisasi DataTables dengan konfigurasi khusus
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
            // order: [
            //     [2, 'asc']
            // ],
            ordering: true,
        });

        // Fungsi untuk menutup modal tambah data
        function closeModalAdd() {
            $('#modalForAdd').modal('hide');
        }

        // Fungsi untuk membuka modal tambah data
        function showModalAdd() {
            $('#modalForAdd').modal('show');
        }

        // Fungsi untuk menutup modal edit data
        function closeModalEdit() {
            $('#modalForEdit').modal('hide');
        }

        // Fungsi untuk membuka modal edit data
        function showModalEdit() {
            $('#modalForEdit').modal('show');
        }

        // Fungsi untuk menampilkan alert dengan SweetAlert2
        function showSweetAlert2(msg, icon) {
            let title = '';
            if (icon === 'success') {
                title = 'Transaksi Berhasil!';
                msg = `Berhasil ${msg}.`;
            }
            Swal.fire({
                html: msg,
                icon: icon,
                title: title,
                confirmButtonText: 'Oke',
                customClass: {
                    popup: 'alert-custom-css'
                }
            });
        }

        // Fungsi untuk mengisi form edit dengan data yang didapat dari server
        function fillFormEdit(id) {
            showModalEdit(); // Memastikan modal edit muncul
            document.querySelector('#modalForEdit form').action = `/alat/${id}`;
            getAlat(id)
                .then(data => {
                    // Mengisi form dengan data yang diterima
                    document.querySelector('#modalForEdit #nama_proyek').value = data.data.nama_proyek;
                    document.querySelector('#modalForEdit #jenis_alat').value = data.data.jenis_alat;
                    document.querySelector('#modalForEdit #merek_alat').value = data.data.merek_alat;
                    document.querySelector('#modalForEdit #tipe_alat').value = data.data.tipe_alat;
                    document.querySelector('#modalForEdit #kode_alat').value = data.data.kode_alat;
                })
                .catch(error => {
                    showSweetAlert2('Gagal mengambil data alat', 'error');
                });
        }

        // Fungsi untuk mengambil data alat berdasarkan ID menggunakan AJAX
        function getAlat(id) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: `/alat/${id}`,
                    type: 'GET',
                    data: {
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        // Event listener untuk tombol delete
        $(document).on('click', '.deleteBtn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus barang!',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'custom-confirm-delete',
                    cancelButton: 'custom-cancel-delete',
                    actions: 'custom-action-delete'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/alat/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Barang telah dihapus.',
                                'success'
                            ).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus barang.',
                                'error'
                            );
                        }
                    });
                }
            })
        });
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
                errInput += "<li>{{ $error }}</li>";
            @endforeach
            errInput += "</ul>";
            showSweetAlert2(errInput, "error");
        </script>
    @endif
@endsection
