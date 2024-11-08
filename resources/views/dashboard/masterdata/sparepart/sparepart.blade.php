@extends('layouts.app')

@push('styles_2')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    </link>
@endpush

@section('content')
    <div class="fade-in-up page-content">
        <div class="ibox">
            {{-- Cek apakah user role adalah Pegawai --}}
            <div class="ibox-head pe-0 ps-0">
                <div class="ibox-title">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</div>
                @if (Auth::user()->role == 'Pegawai')
                    <a class="btn btn-primary btn-sm" id="button-for-modal-add" onclick="showModalAdd()">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a>
                @endif
            </div>

            @include('dashboard.masterdata.sparepart.partials.table', ['masterData' => $masterData])

        </div>
    </div>

    <!-- Modal untuk Tambah Data -->
    <div class="fade modal" id="modalForAdd" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title" id="modalForAddLabel">Tambah Data Master</h1>
                    <button class="btn-close" type="button" onclick="closeModalAdd()"></button>
                </div>
                {{-- <form class="d-flex w-100 align-items-center flex-column gap-3" style="overflow-y:auto" method="POST" action="{{ route('master_data.store') }}"> --}}
                <form class="d-flex w-100 align-items-center flex-column gap-3" style="overflow-y:auto" method="POST" action="#">
                    @csrf
                    <div class="d-flex w-100 align-items-center flex-column modal-body">
                        <div id="form-group">
                            <label class="form-label mb-0" for="supplier">Supplier</label>
                            <input class="form-control" id="supplier" name="supplier" type="text" placeholder="Supplier" required>
                        </div>
                        <div id="form-group">
                            <label class="form-label mb-0" for="sparepart">Sparepart</label>
                            <input class="form-control" id="sparepart" name="sparepart" type="text" placeholder="Sparepart" required>
                        </div>
                        <div id="form-group">
                            <label class="form-label mb-0" for="part_number">Part Number</label>
                            <input class="form-control" id="part_number" name="part_number" type="text" placeholder="Part Number">
                        </div>
                        <div id="form-group">
                            <label class="form-label mb-0" for="buffer_stock">Buffer Stock</label>
                            <input class="form-control" id="buffer_stock" name="buffer_stock" type="number" placeholder="Buffer Stock" required>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-between modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" id="add-data" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Data -->
    <div class="fade modal" id="modalForEdit" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title" id="modalForEditLabel">Ubah Data Master</h1>
                    <button class="btn-close" type="button" onclick="closeModalEdit()"></button>
                </div>
                <form class="d-flex w-100 align-items-center flex-column gap-3" style="overflow-y:auto" method="POST" action="">
                    @csrf
                    @method('PUT') <!-- Tambahkan PUT method untuk update -->
                    <div class="d-flex w-100 align-items-center flex-column modal-body">
                        <div id="form-group">
                            <label class="form-label mb-0" for="supplier">Supplier</label>
                            <input class="form-control" id="supplier" name="supplier" type="text" placeholder="Supplier" required>
                        </div>
                        <div id="form-group">
                            <label class="form-label mb-0" for="sparepart">Sparepart</label>
                            <input class="form-control" id="sparepart" name="sparepart" type="text" placeholder="Sparepart" required>
                        </div>
                        <div id="form-group">
                            <label class="form-label mb-0" for="part_number">Part Number</label>
                            <input class="form-control" id="part_number" name="part_number" type="text" placeholder="Part Number" required>
                        </div>
                        <div id="form-group">
                            <label class="form-label mb-0" for="buffer_stock">Buffer Stock</label>
                            <input class="form-control" id="buffer_stock" name="buffer_stock" type="number" placeholder="Buffer Stock" required>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-between modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" id="update-data" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts_2')
    <script>
        // Inisialisasi DataTables dengan konfigurasi khusus
        $('#table-data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master-data.sparepart.getData') }}", // Pastikan route ini sesuai dengan endpoint untuk mengambil data
                type: "GET"
            },
            language: {
                paginate: {
                    previous: '<i class="bi bi-caret-left"></i>',
                    next: '<i class="bi bi-caret-right"></i>'
                }
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50],
                [10, 25, 50]
            ],
            ordering: true,
            columns: [{
                    data: 'sparepart',
                    name: 'sparepart'
                },
                {
                    data: 'part_number',
                    name: 'part_number'
                },
                {
                    data: 'buffer_stock',
                    name: 'buffer_stock'
                },
                @if (Auth::user()->role == 'Pegawai')
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                    <button class="btn btn-danger deleteBtn" data-id="${row.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-warning ms-3 ubahBtn" data-id="${row.id}" onclick='fillFormEdit("${row.id}")'>
                        <i class="bi bi-pencil-square"></i>
                    </button>
                `;
                        }
                    }
                @endif
            ]
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
            showModalEdit(); // Ensure modal opens

            // Set the form action to the correct update route
            const form = document.querySelector('#modalForEdit form');
            form.action = `/master_data/${id}`;

            getMasterData(id)
                .then(data => {
                    // Fill the form inputs with the data
                    document.querySelector('#modalForEdit #supplier').value = data.data.supplier;
                    document.querySelector('#modalForEdit #sparepart').value = data.data.sparepart;
                    document.querySelector('#modalForEdit #part_number').value = data.data.part_number;
                    document.querySelector('#modalForEdit #buffer_stock').value = data.data.buffer_stock;
                })
                .catch(error => {
                    showSweetAlert2('Gagal mengambil data master', 'error');
                });
        }

        // Fungsi untuk mengambil data master berdasarkan ID menggunakan AJAX
        function getMasterData(id) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: `/master_data/${id}`,
                    type: 'GET',
                    success: function(response) {
                        resolve(response); // Resolving data received
                    },
                    error: function(xhr, status, error) {
                        reject(error); // Reject in case of error
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
                confirmButtonText: 'Ya, hapus data!',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'custom-confirm-delete',
                    cancelButton: 'custom-cancel-delete',
                    actions: 'custom-action-delete'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/master_data/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            Swal.fire(
                                'Terhapus!',
                                'Data telah dihapus.',
                                'success'
                            ).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus data.',
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
@endpush
