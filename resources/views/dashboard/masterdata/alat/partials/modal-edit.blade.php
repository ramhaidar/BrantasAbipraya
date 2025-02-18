@push('styles_3')
    <style>
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endpush

<div class="fade modal" id="modalForEdit" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Master Data Alat</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editAlatForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="jenis_alat">Jenis Alat</label>
                            <input class="form-control" id="jenis_alat" name="jenis_alat" type="text" placeholder="Jenis Alat" required>
                            <div class="invalid-feedback">Jenis Alat diperlukan.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label required" for="kode_alat">Kode Alat</label>
                            <input class="form-control" id="kode_alat" name="kode_alat" type="text" placeholder="Kode Alat" required>
                            <div class="invalid-feedback">Kode Alat diperlukan.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label required" for="merek_alat">Merek Alat</label>
                            <input class="form-control" id="merek_alat" name="merek_alat" type="text" placeholder="Merek Alat" required>
                            <div class="invalid-feedback">Merek Alat diperlukan.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label required" for="tipe_alat">Tipe Alat</label>
                            <input class="form-control" id="tipe_alat" name="tipe_alat" type="text" placeholder="Tipe Alat" required>
                            <div class="invalid-feedback">Tipe Alat diperlukan.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label required" for="serial_number">Serial Number</label>
                            <input class="form-control" id="serial_number" name="serial_number" type="text" placeholder="Serial Number" required>
                            <div class="invalid-feedback">Serial Number diperlukan.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-alat" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            // Form edit untuk validasi
            const editForm = document.querySelector('#editAlatForm');

            // Validasi saat submit form
            editForm.addEventListener('submit', (event) => {
                if (!editForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
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

        function fillFormEdit(id) {
            // Create and append loading overlay
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $('#modalForEdit').append($loadingOverlay);

            // Generate the URL to fetch data using the named route
            const url = "{{ route('master_data_alat.show', ':id') }}".replace(':id', id);
            const updateUrl = "{{ route('master_data_alat.update', ':id') }}".replace(':id', id);

            // Lakukan AJAX GET request ke server untuk mengambil data item
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Mengisi nilai input di modal edit dengan data yang diterima
                    $('#editAlatForm #jenis_alat').val(response.jenis_alat);
                    $('#editAlatForm #kode_alat').val(response.kode_alat);
                    $('#editAlatForm #merek_alat').val(response.merek_alat);
                    $('#editAlatForm #tipe_alat').val(response.tipe_alat);
                    $('#editAlatForm #serial_number').val(response.serial_number);

                    // Set action form for update
                    $('#editAlatForm').attr('action', updateUrl);

                    // Tampilkan modal edit
                    $('#modalForEdit').modal('show');

                    // Remove loading overlay on success
                    $loadingOverlay.remove();
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                    // Remove loading overlay on error
                    $loadingOverlay.remove();
                }
            });
        }

        // Add submit handler to show loading during form submission
        $('#editAlatForm').on('submit', function(e) {
            if (this.checkValidity()) {
                const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#modalForEdit').append($loadingOverlay);

                // Remove overlay after 500ms (adjust based on your needs)
                setTimeout(() => $loadingOverlay.remove(), 500);
            }
        });

        // Event listener untuk tombol edit di tabel
        $(document).on('click', '.ubahBtn', function() {
            const id = $(this).data('id');
            fillFormEdit(id);
        });
    </script>
@endpush
