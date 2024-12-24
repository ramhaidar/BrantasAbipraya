@push('styles_3')
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data ATB</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">

            <form class="w-100 align-items-center flex-column gap-0 overflow-auto" id="addDataForm" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
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

                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal" name="tanggal" type="text" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" autocomplete="off" placeholder="Tanggal Masuk Sparepart" required>
                            <div class="invalid-feedback">Tanggal Masuk Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_spb">Pilih SPB</label>
                            <select class="form-control" id="id_spb" name="id_spb">
                                <option value="">Pilih SPB</option>
                                @foreach ($spbs as $spb)
                                    <option value="{{ $spb->id }}">{{ $spb->nomor }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="dokumentasi">Upload Surat Tanda Terima</label>
                            <input class="form-control" id="dokumentasi" name="dokumentasi" type="file" required accept="image/jpeg,image/jpg,image/png,image.heic,image.heif">
                            <div class="invalid-feedback">Surat Tanda Terima diperlukan.</div>
                        </div>

                        <!-- Include the partials table for selected SPB details -->
                        <div class="col-12" id="tableContainer">
                            <!-- Table will be populated dynamically -->
                        </div>

                    </div>
                </div>
            </form>

            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" id="resetButton" type="button">Reset</button>
                <button class="btn btn-success w-25" id="add-sparepart" type="submit">Tambah Data</button>
            </div>

        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {

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

            // Initialize Select2 for #id_spb
            $('#id_spb').select2({
                placeholder: "Pilih SPB",
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

            if (form) {
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
            }

            // Fetch data when SPB is selected
            $('#id_spb').on('change', function() {
                const selectedValue = $(this).val();
                const tableContainer = $('#tableContainer');
                const loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                if (selectedValue) {
                    $('body').append(loadingOverlay);
                    $.ajax({
                        url: `/atb/getlinkSpbDetailSpbs/${selectedValue}`,
                        type: 'GET',
                        success: function(response) {
                            tableContainer.html(response.html);
                        },
                        error: function(error) {
                            console.error('Error fetching data:', error);
                            tableContainer.html('<p>Failed to load data. Please try again.</p>');
                        },
                        complete: function() {
                            loadingOverlay.remove();
                        }
                    });
                } else {
                    tableContainer.empty();
                }
            });

            // Reset form and clear table on reset button click
            $('#resetButton').on('click', function() {
                $('#addDataForm')[0].reset();
                $('#tableContainer').empty();
                $('#id_spb').val(null).trigger('change');
            });

            // Alert and set value to max if quantity_diterima exceeds max
            $(document).on('blur', '.quantity-input', function() {
                const max = $(this).attr('max');
                const value = $(this).val();
                if (parseInt(value) > parseInt(max)) {
                    alert('Quantity diterima tidak boleh melebihi Quantity PO.');
                    $(this).val(max);
                }
            });
        });
    </script>
@endpush
