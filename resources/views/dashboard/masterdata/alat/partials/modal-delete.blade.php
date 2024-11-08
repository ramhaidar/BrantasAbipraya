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
