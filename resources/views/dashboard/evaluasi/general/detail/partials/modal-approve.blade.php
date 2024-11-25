<!-- Modal for Approval -->
<div class="fade modal" id="modalForApprove" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForApproveLabel">Konfirmasi Finalisasi Detail RKB General</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">Apakah Anda yakin ingin melakukan Approve pada RKB General ini?</p>
                <p class="p-0 m-0">Pastikan semua data telah diisi dengan benar sebelum melakukan finalisasi.</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary w-25" id="confirmApproveButton">Approve</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for finalization request -->
<form id="approveForm" style="display: none;" method="POST">
    @csrf
    @method('POST')
</form>

@push('scripts_3')
    <script>
        // Trigger the hidden submit button on modal confirmation
        $('#confirmApproveButton').on('click', function() {
            $('#hiddenApproveRkbButton').click(); // Trigger hidden button in the form
        });
    </script>
@endpush
