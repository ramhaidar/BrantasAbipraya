@push('styles_3')
    <style>

    </style>
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Master Data Alat</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="alatForm" style="overflow-y: auto" novalidate method="POST" action="{{ route('alat.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_alat">Pilih Alat</label>
                            <select class="form-control" id="id_master_data_alat" name="id_master_data_alat" required>
                                <option value="" disabled selected>Pilih Alat</option>
                                @foreach ($AlatAvailable as $alat)
                                    <option value="{{ $alat->id }}">{{ $alat->jenis_alat }} - {{ $alat->kode_alat }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Pilih Alat diperlukan.</div>
                        </div>
                    </div>
                </div>

                <input id="id_proyek" name="id_proyek" type="hidden" value="{{ $proyek->id }}">

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" id="add-alat" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            // Initialize Select2 with options
            $('#id_master_data_alat').select2({
                placeholder: "Pilih",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

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
            form.querySelectorAll('input, select').forEach((input) => {
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
    </script>
@endpush
