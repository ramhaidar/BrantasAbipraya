<!-- Modal for SVP Approve -->
<div class="fade modal" id="modalForApproveSvp" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForApproveSvpLabel">
                    {{ $rkb->is_approved_svp ? 'Konfirmasi Pembatalan Approve SVP' : 'Konfirmasi Approve SVP' }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">
                    {{ $rkb->is_approved_svp ? 'Apakah Anda yakin ingin membatalkan Approve RKB ini sebagai SVP?' : 'Apakah Anda yakin ingin Approve RKB ini sebagai SVP?' }}
                </p>
                <p class="p-0 m-0">
                    {{ $rkb->is_approved_svp ? 'Pembatalan akan menghapus status approve SVP.' : 'Pastikan semua data telah diisi dengan benar sebelum melakukan approval.' }}
                </p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn {{ $rkb->is_approved_svp ? 'btn-danger' : 'btn-primary' }} w-25" id="confirmApproveSvpButton">
                    {{ $rkb->is_approved_svp ? 'Batalkan' : 'Approve' }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for SVP approval -->
<form id="approveRkbSvpForm" style="display: none;" method="POST">
    @csrf
</form>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            const form = $('#approveRkbSvpForm');

            $('#approveSvpButton, #cancelApproveSvpButton').on('click', function() {
                const action = $(this).data('action');
                form.attr('action', action);
            });

            $('#confirmApproveSvpButton').on('click', function() {
                form.submit();
            });
        });
    </script>
@endpush
