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
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
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

                        <div class="col-12 w-100">
                            <label class="form-label w-100 required" for="id_kategori_sparepart">Pilih Kategori Sparepart</label>
                            <select class="form-control w-100" id="id_kategori_sparepart" name="id_kategori_sparepart" required>
                                <option value="">Pilih Kategori Sparepart</option>
                                @foreach ($kategoriSpareparts as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->kode }}: {{ ucfirst(strtolower($kategori->nama)) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Kategori Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12 w-100">
                            <label class="form-label w-100 required" for="id_master_data_sparepart">Pilih Sparepart</label>
                            <select class="form-control w-100" id="id_master_data_sparepart" name="id_master_data_sparepart" required>
                                <option value="">Pilih Sparepart</option>
                                @foreach ($spareparts as $sparepart)
                                    <option data-kategori="{{ $sparepart->KategoriSparepart->id }}" value="{{ $sparepart->id }}">
                                        {{ $sparepart->nama }} -
                                        {{ $sparepart->merk }} -
                                        {{ $sparepart->part_number }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity">Quantity</label>
                            <input class="form-control" id="quantity" name="quantity" type="number" min="1" placeholder="Quantity" required>
                            <div class="invalid-feedback">Quantity diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="harga">Harga</label>
                            <input class="form-control" id="harga" name="harga" type="number" min="0" placeholder="Harga" required>
                            <div class="invalid-feedback">Harga diperlukan.</div>
                        </div>

                    </div>
                </div>
            </form>

            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" id="resetButton" type="button">Reset</button>
                <button class="btn btn-success w-25" id="submitButton" type="button">Tambah Data</button>
            </div>

        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js" integrity="sha512-Nr6NV16pWOefJbWJiT8SrmZwOomToo/84CNd0MN6DxhP5yk8UAoPUjNuBj9KyRYVpESUb14RTef7FKxLVA4WGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            // Initialize kategori sparepart select
            $('#id_kategori_sparepart').select2({
                placeholder: "Pilih Kategori Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Initialize sparepart select and disable it initially
            $('#id_master_data_sparepart').select2({
                placeholder: "Pilih Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            }).prop('disabled', true);

            // Add loading overlay to body
            $('body').append('<div class="loading-overlay" style="display: none;"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Enable sparepart select when kategori is selected
            $('#id_kategori_sparepart').on('change', function() {
                var selectedKategori = $(this).val();
                if (selectedKategori) {
                    $('#id_master_data_sparepart').prop('disabled', false);
                    // Show loading overlay
                    $('.loading-overlay').show();
                    // Fetch spareparts based on selected kategori
                    $.ajax({
                        url: '{{ route('spareparts-by-category', ':id') }}'.replace(':id', selectedKategori),
                        type: 'GET',
                        success: function(data) {
                            var sparepartSelect = $('#id_master_data_sparepart');
                            sparepartSelect.empty();
                            if (data.length > 0) {
                                sparepartSelect.append('<option value="">Pilih Sparepart</option>');
                                $.each(data, function(key, sparepart) {
                                    sparepartSelect.append('<option value="' + sparepart.id + '">' + sparepart.nama + ' - ' + sparepart.merk + ' - ' + sparepart.part_number + '</option>');
                                });
                                sparepartSelect.select2({
                                    placeholder: "Pilih Sparepart",
                                    allowClear: true,
                                    dropdownParent: $('#modalForAdd'),
                                    width: '100%'
                                }).prop('disabled', false);
                            } else {
                                sparepartSelect.append('<option value="">Tidak ada sparepart tersedia</option>');
                                sparepartSelect.select2({
                                    placeholder: "Tidak ada sparepart tersedia",
                                    allowClear: true,
                                    dropdownParent: $('#modalForAdd'),
                                    width: '100%'
                                }).prop('disabled', true);
                            }
                            sparepartSelect.val(null).trigger('change');
                        },
                        complete: function() {
                            // Hide loading overlay
                            $('.loading-overlay').hide();
                        }
                    });
                } else {
                    $('#id_master_data_sparepart').prop('disabled', true).val(null).trigger('change');
                }
            });

            // Initialize datepicker for #tanggal
            var dateFormat = 'yy-mm-dd';
            var options = {
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                regional: 'id'
            };

            $('#tanggal').datepicker(options);
            $.datepicker.setDefaults($.datepicker.regional['id']);

            // Reset form on reset button click
            $('#resetButton').on('click', function() {
                $('#addDataForm')[0].reset();
                $('#id_kategori_sparepart').val(null).trigger('change');
                $('#id_master_data_sparepart').val(null).trigger('change');
            });

            // Validate form on submit button click
            $('#submitButton').on('click', function() {
                if ($('#addDataForm')[0].checkValidity()) {
                    $('#addDataForm').submit();
                } else {
                    $('#addDataForm').addClass('was-validated');
                }
            });

            // Remove validation classes on change
            $('#id_master_data_sparepart').on('change', function() {
                if ($(this).val() !== '') {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
                }
            });
        });
    </script>
@endpush
