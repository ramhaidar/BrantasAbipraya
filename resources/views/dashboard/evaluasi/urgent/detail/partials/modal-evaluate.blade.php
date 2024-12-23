<!-- Modal for Evaluate -->
<div class="fade modal" id="modalForEvaluate" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEvaluateLabel">Konfirmasi Evaluasi</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0" id="evaluateMessage">Apakah Anda yakin ingin menyimpan hasil Evaluasi RKB ini?</p>
                <p class="p-0 m-0">Pastikan semua data telah diisi dengan benar sebelum melakukan finalisasi.</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary w-25" id="confirmEvaluateButton">Evaluate</button>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Event handler untuk tombol evaluasi
            $('#evaluateBtnButton').on('click', function() {
                const action = $(this).data('action');
                const form = $('#approveRkbForm'); // Get form reference

                if (form.length) { // Check if form exists
                    form.attr('action', action);
                } else {
                    console.error('Form #approveRkbForm not found');
                }
            });

            // Event handler untuk tombol konfirmasi di modal
            $('#confirmEvaluateButton').on('click', function() {
                const form = $('#approveRkbForm'); // Get form reference again

                if (form.length) { // Check if form exists
                    $('#modalForEvaluate').modal('hide');
                    form.submit();
                } else {
                    console.error('Form #approveRkbForm not found');
                    $('#modalForEvaluate').modal('hide');
                }
            });
        });
    </script>
@endpush
