<div class="fade modal" id="modalForEdit" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Data Proyek</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editProyekForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="nama">Nama Proyek</label>
                            <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Proyek" required>
                            <div class="invalid-feedback">Nama Proyek diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-proyek" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        (() => {
            'use strict';

            // Form edit untuk validasi
            const editForm = document.querySelector('#editProyekForm');

            // Validasi saat submit form
            editForm.addEventListener('submit', (event) => {
                if (!editForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    $('#update-proyek').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                }
                editForm.classList.add('was-validated');
            });

            // Validasi saat blur (out of focus) untuk setiap input di form edit
            editForm.querySelectorAll('input').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });
        })();

        // Function to display modal for editing and populate form with server data
        function fillFormEdit(id) {
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#modalForEdit').modal('show');
            $('#modalForEdit').append($loadingOverlay);

            const url = "{{ route('proyek.show', ':id') }}".replace(':id', id);

            // AJAX GET request to fetch proyek data
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Populate fields with data
                    $('#editProyekForm #nama').val(response.data.nama);

                    // Set action form to update the specific proyek with PUT method
                    $('#editProyekForm').attr('action', url);

                    $loadingOverlay.remove();
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                    $loadingOverlay.remove();
                }
            });
        }

        // Event listener untuk tombol edit di tabel
        // $(document).on('click', '.ubahBtn', function() {
        //     const id = $(this).data('id'); // Ambil ID dari atribut data-id
        //     fillFormEdit(id); // Panggil fungsi untuk mengisi form edit
        // });
    </script>
@endpush
