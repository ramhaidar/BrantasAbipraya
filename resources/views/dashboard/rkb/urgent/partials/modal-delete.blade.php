<div class="fade modal" id="modalForDelete" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForDeleteLabel">Konfirmasi Hapus RKB Urgent</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">Apakah Anda yakin ingin menghapus RKB Urgent ini?</p>
                <p class="p-0 m-0">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger w-25" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form untuk mengirimkan permintaan DELETE -->
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

        // Event handler untuk tombol "Hapus" di modal delete
        $('#confirmDeleteButton').on('click', function() {
            const id = $(this).data('id'); // Ambil ID item dari data-id tombol
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            deleteWithForm(id); // Panggil fungsi untuk menghapus dengan form
        });

        // Fungsi untuk menghapus data dengan mengirimkan form DELETE
        function deleteWithForm(id) {
            const form = document.getElementById('deleteForm');

            // Gunakan route() untuk membuat URL dinamis
            form.action = `{{ route('rkb_urgent.destroy', ['id' => ':id']) }}`.replace(':id', id);

            form.submit(); // Kirim form
        }
    </script>
@endpush
