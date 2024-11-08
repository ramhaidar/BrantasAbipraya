<div class="modal fade" id="modalForDelete" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button class="btn-close" type="button" onclick="closeModalDelete()"></button>
            </div>
            <div class="modal-body">
                <span>Apakah Anda yakin ingin menghapus item ini?</span>
                <br class="p-0 m-0">
                <span>Tindakan ini tidak dapat dibatalkan!</span>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" onclick="closeModalDelete()">Batal</button>
                <button class="btn btn-danger w-25" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" style="display: none;" method="POST">
    @csrf
    @method('DELETE')
</form>

@push('scripts_3')
    <script>
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
    </script>
@endpush
