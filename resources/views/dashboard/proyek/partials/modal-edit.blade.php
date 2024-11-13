<div class="fade modal" id="modalForEdit" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h1 class="fs-5" id="modalForEditLabel">Ubah Data Proyek</h1>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editProyekForm" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="nama_proyek">Nama Proyek</label>
                            <input class="form-control" id="nama_proyek" name="nama_proyek" type="text" placeholder="Nama Proyek" required>
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

@push('styles_3')
    <style>
        /* CSS for required asterisk */
        .form-label.required::after {
            content: " *";
            color: red;
            font-weight: bold;
            margin-left: 2px;
        }
    </style>
@endpush

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
            const url = "{{ route('proyek.show', ':id') }}".replace(':id', id);

            // AJAX GET request to fetch proyek data
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Populate fields with data
                    $('#editProyekForm #nama_proyek').val(response.data.nama_proyek);

                    // Set action form to update the specific proyek with PUT method
                    $('#editProyekForm').attr('action', url);

                    // Display the edit modal
                    $('#modalForEdit').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                }
            });
        }

        // Event listener untuk tombol edit di tabel
        $(document).on('click', '.ubahBtn', function() {
            const id = $(this).data('id'); // Ambil ID dari atribut data-id
            fillFormEdit(id); // Panggil fungsi untuk mengisi form edit
        });
    </script>
@endpush
