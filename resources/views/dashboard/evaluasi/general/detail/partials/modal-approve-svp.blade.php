<!-- Modal for SVP Approve -->
<div class="fade modal" id="modalForApproveSvp" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2">Konfirmasi Approve SVP</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0" id="approveSvpMessage">Apakah Anda yakin ingin Approve RKB ini sebagai SVP?</p>
                <p class="p-0 m-0">Pastikan semua data telah diisi dengan benar sebelum melakukan approval.</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary w-25" id="confirmApproveSvpButton">Approve SVP</button>
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
            $('#approveSvpButton').on('click', function() {
                const action = $(this).data('action');
                const message = $(this).data('message');
                
                // Set the form action
                $('#approveRkbSvpForm').attr('action', action);
                $('#approveSvpMessage').text(message);
            });

            // Move this outside the click handler to prevent multiple bindings
            $('#confirmApproveSvpButton').on('click', function() {
                $('#approveRkbSvpForm').submit();
            });
        });
    </script>
@endpush
