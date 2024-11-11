<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h1 class="fs-5" id="modalForAddLabel">Tambah Master Data Sparepart</h1>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="sparepartForm" novalidate method="POST" action="{{ route('master_data_sparepart.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="nama">Nama Sparepart</label>
                            <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Sparepart" required>
                            <div class="invalid-feedback">Nama Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="part_number">Part Number</label>
                            <input class="form-control" id="part_number" name="part_number" type="text" placeholder="Part Number" required>
                            <div class="invalid-feedback">Part Number diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="merk">Merk</label>
                            <input class="form-control" id="merk" name="merk" type="text" placeholder="Merk" required>
                            <div class="invalid-feedback">Merk diperlukan.</div>
                        </div>

                        <!-- Multi-select for Supplier -->
                        <div class="col-12">
                            <label class="form-label" for="suppliers">Supplier</label>
                            <select class="form-control" id="suppliers" name="suppliers[]" multiple="multiple">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Supplier diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" id="add-sparepart" type="submit">Simpan</button>
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
            'use strict'

            // Fetch the form for validation
            const form = document.querySelector('#sparepartForm');

            // Apply Bootstrap validation on form submit
            form.addEventListener('submit', (event) => {
                // Check if inputs are valid
                if (!form.checkValidity() || !validateSelect2()) {
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

            // Initialize Select2 for multi-select
            $('#suppliers').select2({
                placeholder: "Pilih Supplier",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 0,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            }).on("select2:select select2:unselect", function() {
                validateSelect2();
            });
        })();
    </script>
@endpush
