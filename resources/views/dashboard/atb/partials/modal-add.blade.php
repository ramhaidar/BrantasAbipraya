@push('styles_3')
    <style>

    </style>
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data ATB</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">

            <form class="w-100 align-items-center flex-column gap-0" id="addDataForm" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="p-0 m-0" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <div class="input-group">
                                <select class="form-control" id="pilihan-proyek1" name="tipe">
                                    <option value="hutang-unit-alat" {{ $page == 'Data ATB Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                    <option value="panjar-unit-alat" {{ $page == 'Data ATB Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                    <option value="mutasi-proyek" {{ $page == 'Data ATB Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                    <option value="panjar-proyek" {{ $page == 'Data ATB Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                                </select>
                            </div>
                        </div>

                        {{-- @if ($page == 'Data ATB Mutasi Proyek')
                            <div class="">
                                <label class="form-label" for="asal_proyek">Asal Proyek</label>
                                <input class="form-control" id="asal_proyek_display" value="{{ $proyek->nama_proyek }}" readonly>
                                <input id="asal_proyek" name="asal_proyek" type="hidden" value="{{ $proyek->id }}">
                            </div>
                        @endif --}}

                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal" name="tanggal" type="text" placeholder="Tanggal Masuk Sparepart" required>
                            <div class="invalid-feedback">Tanggal Masuk Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_spb">Pilih SPB</label>
                            <select class="form-control" id="id_spb" name="id_spb">
                                <option value="">Pilih SPB</option>

                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_detail_spb">Pilih Item</label>
                            <select class="form-control" id="id_detail_spb" name="id_detail_spb">
                                <option value="">Pilih Item</option>

                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity">Quantity</label>
                            <input class="form-control" id="quantity" name="quantity" type="number" value="1" min="1" placeholder="Quantity" required>
                            <div class="invalid-feedback">Quantity diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="dokumentasi">Upload Dokumentasi</label>
                            <input class="form-control" id="dokumentasi" name="dokumentasi" type="file" required accept="image/jpeg,image/jpg,image/png,image/heic,image/heif">
                            <div class="invalid-feedback">Dokumentasi diperlukan.</div>
                        </div>

                        <div class="modal-footer d-flex w-100 justify-content-end">
                            <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                            <button class="btn btn-success w-25" id="add-sparepart" type="submit">Tambah Data</button>
                        </div>
                    </div>
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

            // Initialize Select2 for #pilihan-kode1
            $('#pilihan-kode1').select2({
                placeholder: "Pilih Kode",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Initialize datepicker for #tanggal
            $(document).ready(function() {
                var dateFormat = 'yy-mm-dd';
                var options = {
                    dateFormat: dateFormat,
                    changeMonth: true,
                    changeYear: true,
                    regional: 'id'
                };

                $('#tanggal').datepicker(options);

                $.datepicker.setDefaults($.datepicker.regional['id']);
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
