@push('styles_3')
@endpush

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
        // Event listener for all delete buttons
        $(document).on('click', '.deleteBtn', function() {
            const id = $(this).data('id'); // Retrieve ID from data-id attribute
            showModalDelete(id); // Show delete modal with the item ID
        });

        // Function to show the delete modal and set the ID of the item to delete
        function showModalDelete(id) {
            $('#confirmDeleteButton').data('id', id); // Set data-id with item ID
            $('#modalForDelete').modal('show');
        }

        // Function to close the delete modal
        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        // Event handler for the "Delete" button in the delete modal
        $('#confirmDeleteButton').on('click', function() {
            const id = $(this).data('id'); // Get item ID from button data-id
            deleteWithForm(id); // Call function to delete with form
        });

        // Function to delete data by submitting the DELETE form
        function deleteWithForm(id) {
            const form = document.getElementById('deleteForm');
            // Generate action URL using named route and replace ':id' with the item ID
            const actionUrl = "{{ route('master_data_alat.destroy', ':id') }}".replace(':id', id);
            form.action = actionUrl;
            form.submit(); // Submit the form
        }
    </script>
@endpush
