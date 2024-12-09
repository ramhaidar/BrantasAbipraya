@push('styles_3')
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Rencana Kebutuhan Barang</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="rkburgentForm" style="overflow-y: auto" novalidate method="POST" action="{{ route('rkb_urgent.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="nomor">No RKB</label>
                            <input class="form-control" id="nomor" name="nomor" type="text" placeholder="No RKB" required>
                            <div class="invalid-feedback">No RKB diperlukan.</div>
                        </div>

                        <!-- Single-select for Proyek -->
                        <div class="col-12">
                            <label class="form-label required" for="proyek">Proyek</label>
                            <select class="form-control" id="proyek" name="proyek" required>
                                <option value="">Pilih Proyek</option>
                                @foreach ($proyeks as $proyek)
                                    <option value="{{ $proyek->id }}">{{ $proyek->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Proyek diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="periode">Periode</label>
                            <input class="form-control" id="periode" name="periode" type="month" placeholder="Periode" required>
                            <div class="invalid-feedback">Periode diperlukan.</div>
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
            const form = document.querySelector('#rkburgentForm');

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

            // Initialize Select2 for Proyek
            $('#proyek').select2({
                placeholder: "Pilih Proyek",
                allowClear: true,
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
                    $('#proyeks').val('').trigger('change'); // Reset proyeks
                    form.classList.remove('was-validated'); // Remove validation styles
                }, 0); // Ensure this runs after the form reset
            });

            // Set default value for Periode
            document.addEventListener('DOMContentLoaded', () => {
                const periodeInput = document.getElementById('periode');

                // Get the current year and month
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0'); // Add leading zero to month if needed

                // Set the default value to current year and month
                periodeInput.value = `${year}-${month}`;
            });

            document.getElementById('periode').addEventListener('click', function() {
                this.showPicker();
            })
        })();
    </script>
@endpush
