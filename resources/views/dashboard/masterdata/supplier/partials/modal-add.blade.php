@push('styles_3')
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Master Data Supplier</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="supplierForm" novalidate method="POST" action="{{ route('master_data_supplier.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Nama Supplier -->
                        <div class="col-12">
                            <label class="form-label required" for="nama">Nama Supplier</label>
                            <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Supplier" required>
                            <div class="invalid-feedback">Nama Supplier diperlukan.</div>
                        </div>

                        <!-- Alamat -->
                        <div class="col-12">
                            <label class="form-label required" for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat" required></textarea>
                            <div class="invalid-feedback">Alamat diperlukan.</div>
                        </div>

                        <!-- Contact Person -->
                        <div class="col-12">
                            <label class="form-label required" for="contact_person">Contact Person</label>
                            <input class="form-control" id="contact_person" name="contact_person" type="text" placeholder="Contact Person" required>
                            <div class="invalid-feedback">Contact Person diperlukan.</div>
                        </div>

                        <!-- Multi-select for Spareparts -->
                        <div class="col-12">
                            <label class="form-label" for="spareparts">Spareparts</label>
                            <select class="form-control" id="spareparts" name="spareparts[]" multiple="multiple">
                                @foreach ($spareparts as $sparepart)
                                    <option value="{{ $sparepart->id }}">{{ $sparepart->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" id="add-supplier" type="submit">Simpan</button>
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
            const form = document.querySelector('#supplierForm');

            // Apply Bootstrap validation on form submit
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Apply validation on blur (out of focus) for each input in the form
            form.querySelectorAll('input, textarea').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });

            // Initialize Select2 for multi-select Spareparts without mandatory validation
            $('#spareparts').select2({
                placeholder: "Pilih Sparepart",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 0,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });
        })();
    </script>
@endpush
