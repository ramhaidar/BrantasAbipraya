@push('styles_3')
    <style>

    </style>
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Master Data Alat</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="alatForm" style="overflow-y: auto" novalidate method="POST" action="{{ route('alat.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 w-100">
                            <label class="form-label w-100 required" for="id_master_data_alat">Pilih Alat</label>
                            <select class="form-control w-100" id="id_master_data_alat" name="id_master_data_alat[]" multiple="multiple" required>
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
                    <button class="btn btn-secondary me-2 w-25" id="reset-alat-form" type="reset">Reset</button>
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
                placeholder: "Pilih Alat",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 0,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            }).on("select2:select", function(e) {
                $(this).select2('open');
            }).on("select2:unselect", function(e) {
                $(this).select2('open');
            });

            // Fetch all forms we want to apply validation to
            const form = document.querySelector('#alatForm');

            // Apply Bootstrap validation on form submit
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    $('#add-alat').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
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

            // Handle form reset
            document.getElementById('reset-alat-form').addEventListener('click', () => {
                $('#id_master_data_alat').val(null).trigger('change');
                form.classList.remove('was-validated');
                form.querySelectorAll('.is-valid, .is-invalid').forEach((input) => {
                    input.classList.remove('is-valid', 'is-invalid');
                });
            });
        })()
    </script>
@endpush
