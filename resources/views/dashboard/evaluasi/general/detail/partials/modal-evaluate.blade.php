<!-- Modal for Evaluate -->
<div class="fade modal" id="modalForEvaluate" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEvaluateLabel">
                    {{ $rkb->is_evaluated ? 'Konfirmasi Pembatalan Evaluasi' : 'Konfirmasi Evaluasi' }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">
                    {{ $rkb->is_evaluated ? 'Apakah Anda yakin ingin membatalkan Evaluasi RKB ini?' : 'Apakah Anda yakin ingin menyimpan hasil Evaluasi RKB ini?' }}
                </p>
                <p class="p-0 m-0">
                    {{ $rkb->is_evaluated ? 'Pembatalan akan menghapus semua hasil evaluasi yang sudah dilakukan.' : 'Pastikan semua data telah diisi dengan benar sebelum melakukan evaluasi.' }}
                </p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn {{ $rkb->is_evaluated ? 'btn-danger' : 'btn-primary' }} w-25" id="confirmEvaluateButton">
                    {{ $rkb->is_evaluated ? 'Batalkan' : 'Evaluate' }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            const form = $('#approveRkbForm');

            $('#evaluateBtnButton').on('click', function() {
                const action = $(this).data('action');
                form.attr('action', action);
            });

            $('#confirmEvaluateButton').on('click', function() {
                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                form.submit();
            });
        });
    </script>
@endpush
