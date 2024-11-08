@extends('layouts.app')

@section('content')
    <link href="{{ asset('css/dashboard_content.css') }}" rel="stylesheet">

    <div class="fade-in-up page-content">
        <div class="ibox shadow-md" style="border: 2px solid grey;">
            <div class="ibox-head pe-0 ps-0">
                <div class="ibox-title ps-2">
                    <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                </div>
                <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                    <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                </a>
            </div>

            @include('dashboard.masterdata.alat.partials.table', ['alat' => $alat])

        </div>
    </div>

    <!-- Modal for Adding Data -->
    @include('dashboard.masterdata.alat.partials.modal-add')

    <!-- Modal for Editing Data -->
    @include('dashboard.masterdata.alat.partials.modal-edit')

    <!-- Modal for Delete Confirmation -->
    @include('dashboard.masterdata.alat.partials.modal-delete')
@endsection

@push('scripts_2')
    @stack('scripts_3')

    <script>
        // Fungsi untuk menutup modal tambah data
        // function closeModalAdd() {
        //     $('#modalForAdd').modal('hide');
        // }

        // Fungsi untuk membuka modal tambah data
        // function showModalAdd() {
        //     $('#modalForAdd').modal('show');
        // }

        // Fungsi untuk menutup modal edit data
        function closeModalEdit() {
            $('#modalForEdit').modal('hide');
        }

        // Fungsi untuk membuka modal edit data
        function showModalEdit() {
            $('#modalForEdit').modal('show');
        }

        // Event listener untuk semua tombol delete
        $(document).on('click', '.deleteBtn', function() {
            const id = $(this).data('id'); // Ambil ID dari atribut data-id
            showModalDelete(id); // Tampilkan modal delete dengan ID item
        });

        // Fungsi untuk membuka modal delete dan menyetel ID item yang akan dihapus
        function showModalDelete(id) {
            $('#confirmDeleteButton').data('id', id); // Set data-id dengan ID item
            $('#modalForDelete').modal('show');
        }

        // Fungsi untuk menutup modal delete
        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        // Event handler untuk tombol "Hapus" di modal delete
        $('#confirmDeleteButton').on('click', function() {
            const id = $(this).data('id'); // Ambil ID item dari data-id tombol
            deleteWithForm(id); // Panggil fungsi untuk menghapus dengan form
        });

        // Fungsi untuk menghapus data dengan mengirimkan form DELETE
        function deleteWithForm(id) {
            const form = document.getElementById('deleteForm');
            form.action = `/master-data-alats/${id}`; // Set URL action form dengan ID item
            form.submit(); // Kirim form
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
    </script>
@endpush
