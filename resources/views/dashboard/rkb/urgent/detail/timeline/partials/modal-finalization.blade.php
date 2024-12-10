<!-- Modal for finalization -->
<div class="fade modal" id="modalForFinalize" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForFinalizeLabel">Konfirmasi Finalisasi Detail RKB Urgent</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">Apakah Anda yakin ingin melakukan Finalisasi pada Detail RKB Urgent ini?</p>
                <p class="p-0 m-0">Pastikan semua data telah diisi dengan benar sebelum melakukan finalisasi.</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary w-25" id="confirmFinalizeButton">Finalisasi</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for finalization request -->
<form id="finalizeForm" style="display: none;" method="POST">
    @csrf
    @method('POST')
</form>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Event listener for all finalize buttons
            $(document).on('click', '.finalizeBtn', function() {
                const id = $(this).data('id'); // Get ID from data-id attribute
                showModalFinalize(id); // Show modal with the ID
            });

            // Function to show the modal and set the ID for finalization
            function showModalFinalize(id) {
                $('#confirmFinalizeButton').data('id', id); // Set data-id in the modal finalize button
                $('#modalForFinalize').modal('show');
            }

            // Event handler for the "Finalize" button in the modal
            $('#confirmFinalizeButton').on('click', function() {
                const id = $(this).data('id'); // Get the ID from the data-id attribute
                finalizeWithForm(id); // Call function to finalize
            });

            // Function to finalize data by submitting a hidden form
            function finalizeWithForm(id) {
                const form = document.getElementById('finalizeForm');

                // Dynamically set the form action using the route helper
                form.action = `{{ route('rkb_urgent.finalize', ['id' => ':id']) }}`.replace(':id', id);

                form.submit(); // Submit the form
            }
        });
    </script>
@endpush
