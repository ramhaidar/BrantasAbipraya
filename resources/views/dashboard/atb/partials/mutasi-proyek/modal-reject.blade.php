<div class="fade modal" id="modalForReject" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForRejectLabel">Konfirmasi Tolak Mutasi</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">Apakah Anda yakin ingin menolak mutasi ini?</p>
                <p class="p-0 m-0">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger w-25" id="confirmRejectButton">Tolak</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form untuk mengirimkan permintaan PATCH -->
<form id="rejectForm" style="display: none;" method="POST">
    @csrf
    @method('PATCH')
</form>

@push('scripts_3')
    <script>
        $(document).on('click', '.rejectBtn', function() {
            const id = $(this).data('id');
            showModalReject(id);
        });

        function showModalReject(id) {
            $('#confirmRejectButton').data('id', id);
            $('#modalForReject').modal('show');
        }

        $('#confirmRejectButton').on('click', function() {
            const id = $(this).data('id');
            rejectWithForm(id);
        });

        function rejectWithForm(id) {
            const form = document.getElementById('rejectForm');
            form.action = `{{ route('atb.mutasi.reject', ['id' => ':id']) }}`.replace(':id', id);
            form.submit();
        }
    </script>
@endpush
