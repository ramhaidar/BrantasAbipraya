@push('styles_3')
@endpush

<div class="modal fade" id="modalForDelete" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 pb-2">Konfirmasi Hapus</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <span>Apakah Anda yakin ingin menghapus user ini?</span>
                <br class="p-0 m-0">
                <span>Tindakan ini tidak dapat dibatalkan!</span>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
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
        $(document).ready(function() {
            // Event listener for delete buttons
            $('.deleteBtn').on('click', function() {
                const id = $(this).data('id');
                showModalDelete(id);
            });

            // Handle confirmation button click
            $('#confirmDeleteButton').on('click', function() {
                const id = $(this).data('id');
                if (id) {
                    deleteWithForm(id);
                }
            });
        });

        function showModalDelete(id) {
            if (id) {
                $('#confirmDeleteButton').data('id', id);
                $('#modalForDelete').modal('show');
            }
        }

        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        function deleteWithForm(id) {
            $('#confirmDeleteButton').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            const form = document.getElementById('deleteForm');
            form.action = `/users/delete/${id}`;
            form.submit();
        }
    </script>
@endpush
