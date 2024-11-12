<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h1 class="fs-5" id="modalForAddLabel">Tambah Master Data Alat</h1>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="alatForm" novalidate method="POST" action="{{ route('master_data_alat.store') }}">
                @csrf
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
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" id="add-alat" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles_3')
@endpush

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            // Fetch all forms we want to apply validation to
            const form = document.querySelector('#alatForm');

            // Apply Bootstrap validation on form submit
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Apply validation on blur (out of focus) for each input in the form
            form.querySelectorAll('input').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });
        })()

        $(document).ready(function() {
            // Find all input fields that are required
            $("input[required]").each(function() {
                // Find the label associated with the input
                const label = $(this).closest(".col-12").find("label");

                // Append the asterisk only if the label exists
                if (label.length) {
                    label.append(' <span class="text-danger required-asterisk">*</span>');
                }
            });
        });
    </script>
@endpush
