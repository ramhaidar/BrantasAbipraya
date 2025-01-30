<!-- Modal for VP Approve -->
<div class="fade modal" id="modalForApproveVp" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForApproveVpLabel">
                    {{ $rkb->is_approved_vp ? 'Konfirmasi Pembatalan Approve VP' : 'Konfirmasi Approve VP' }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">
                    {{ $rkb->is_approved_vp ? 'Apakah Anda yakin ingin membatalkan Approve RKB ini sebagai VP?' : 'Apakah Anda yakin ingin Approve RKB ini sebagai VP?' }}
                </p>
                <p class="p-0 m-0">
                    {{ $rkb->is_approved_vp ? 'Pembatalan akan menghapus status approve VP.' : 'Pastikan semua data telah diisi dengan benar sebelum melakukan approval.' }}
                </p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn {{ $rkb->is_approved_vp ? 'btn-danger' : 'btn-primary' }} w-25" id="confirmApproveVpButton">
                    {{ $rkb->is_approved_vp ? 'Batalkan' : 'Approve' }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for VP approval -->
<form id="approveRkbVpForm" style="display: none;" method="POST">
    @csrf
</form>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            const form = $('#approveRkbVpForm');

            $('#approveVpButton, #cancelApproveVpButton').on('click', function() {
                const action = $(this).data('action');
                form.attr('action', action);
            });

            $('#confirmApproveVpButton').on('click', function() {
                form.submit();
            });
        });
    </script>
@endpush
