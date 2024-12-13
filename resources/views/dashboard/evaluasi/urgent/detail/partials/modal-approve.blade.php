<!-- Modal for Approve -->
<div class="fade modal" id="modalForApprove" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForApproveLabel">Konfirmasi Approve</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0" id="approveMessage">Apakah Anda yakin ingin Approve RKB ini?</p>
                <p class="p-0 m-0">Pastikan semua data telah diisi dengan benar sebelum melakukan finalisasi.</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary w-25" id="confirmApproveButton">Approve</button>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Simpan referensi form utama
            const form = $('#approveRkbForm');

            // Tombol Approve
            $('#approveBtnButton').on('click', function() {
                const action = $(this).data('action');
                const message = $(this).data('message');

                // Atur action dan pesan di modal
                form.attr('action', action);
                $('#approveMessage').text(message);

                // Atur tombol konfirmasi approve
                $('#confirmApproveButton').on('click', function() {
                    form.submit(); // Submit form untuk approve
                });
            });
        });
    </script>
@endpush
