@push('styles_3')
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Master Data Sparepart</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="sparepartForm" style="overflow-y: auto" novalidate method="POST" action="{{ route('master_data_sparepart.store') }}">
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

                        <!-- Selection for Kategori (Select2) -->
                        <div class="col-12">
                            <label class="form-label required" for="kategori">Kategori</label>
                            <select class="form-control" id="kategori" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->kode }} - {{ $category->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Kategori diperlukan.</div>
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

            // Initialize Select2 for Kategori
            $('#kategori').select2({
                placeholder: "Pilih Kategori",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            }).on("select2:select select2:unselect", function() {
                validateSelect2();
            });

            // Initialize Select2 for multi-select (Suppliers)
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

            // Custom validation for Select2
            function validateSelect2() {
                let isValid = true;

                // Validate Kategori
                const kategori = $('#kategori');
                if (kategori.val() === "" || kategori.val() === null) {
                    kategori.next('.select2').find('.select2-selection').addClass('is-invalid');
                    isValid = false;
                } else {
                    kategori.next('.select2').find('.select2-selection').removeClass('is-invalid');
                }

                return isValid;
            }

            // Handle form reset to reset Select2 fields
            form.addEventListener('reset', () => {
                setTimeout(() => {
                    $('#kategori').val('').trigger('change'); // Reset kategori
                    $('#suppliers').val(null).trigger('change'); // Reset suppliers
                    form.classList.remove('was-validated'); // Remove validation styles
                }, 0); // Ensure this runs after the form reset
            });
        })();

        // $(document).ready(function() {
        //     // Find all input fields that are required
        //     $("input[required]").each(function() {
        //         // Find the label associated with the input
        //         const label = $(this).closest(".col-12").find("label");

        //         // Append the asterisk only if the label exists
        //         if (label.length) {
        //             label.append(' <span class="text-danger required-asterisk">*</span>');
        //         }
        //     });
        // });
    </script>
@endpush
