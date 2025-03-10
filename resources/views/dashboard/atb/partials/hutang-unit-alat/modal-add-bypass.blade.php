<div class="fade modal" id="modalForAddBypass" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddBypassLabel">Tambah Data ATB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto needs-validation" id="addDataFormBypass" data-has-price-conversion="true" method="POST" action="{{ route('atb.post.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="p-0 m-0" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <select class="form-control" id="pilihan-proyek1" name="tipe">
                                <option value="hutang-unit-alat-bypass" selected>Hutang Unit Alat Bypass</option>
                            </select>
                        </div>

                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal_bypass">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal_bypass" name="tanggal" type="text" autocomplete="off" placeholder="Tanggal Masuk Sparepart" required>
                            <div class="invalid-feedback">Tanggal Masuk Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12 w-100">
                            <label class="form-label w-100 required" for="id_master_data_supplier">Pilih Supplier</label>
                            <select class="form-control w-100" id="id_master_data_supplier" name="id_master_data_supplier" required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Supplier diperlukan.</div>
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
                            <label class="form-label required" for="satuan">Satuan</label>
                            <select class="form-control" id="satuan" name="satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="Assy">Assy</option>
                                <option value="Box">Box</option>
                                <option value="Btl">Btg</option>
                                <option value="Btl">Btl</option>
                                <option value="Drum">Drum</option>
                                <option value="Ken">Ken</option>
                                <option value="Kg">Kg</option>
                                <option value="Ktk">Ktk</option>
                                <option value="Ls">Ls</option>
                                <option value="Ltr">Ltr</option>
                                <option value="Ltr">M</option>
                                <option value="Pack">Pack</option>
                                <option value="Pail">Pail</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Set">Set</option>
                            </select>
                            <div class="invalid-feedback">Satuan diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="harga_display">Harga (Rp.)</label>
                            <input class="form-control" id="harga_display" type="text" placeholder="Harga" required>
                            <!-- Hidden field that will hold the actual value to be submitted -->
                            <input id="harga" name="harga" type="hidden" value="">
                            <div class="invalid-feedback">Harga diperlukan dan harus berupa angka dengan format yang benar.</div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" id="resetButtonBypass" type="button">Reset</button>
                    <button class="btn btn-success w-25" id="submitButtonBypass" type="submit">Tambah Data</button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js" integrity="sha512-Nr6NV16pWOefJbWJiT8SrmZwOomToo/84CNd0MN6DxhP5yk8UAoPUjNuBj9KyRYVpESUb14RTef7FKxLVA4WGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        // Define clearPreview function globally
        function clearPreview() {
            const dokumentasiPreview = document.getElementById('dokumentasiPreview');
            if (dokumentasiPreview) {
                dokumentasiPreview.innerHTML = '';
            }
        }

        $(document).ready(function() {
            // Add loading overlay to body
            $('body').append('<div class="loading-overlay" style="display: none;"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Format number function for Indonesian locale - UPDATED TO SUPPORT NEGATIVE VALUES
            function formatRupiah(angka, prefix) {
                // Check if string starts with minus sign
                const isNegative = angka.startsWith('-');

                // Remove the minus sign for processing
                if (isNegative) {
                    angka = angka.substring(1);
                }

                // Format as usual
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1].substr(0, 3) : rupiah;

                // Add the minus sign back if the original was negative
                if (isNegative) {
                    rupiah = '-' + rupiah;
                }

                return prefix === undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
            }

            // Parse Indonesian formatted number back to standard decimal - IMPROVED FOR NEGATIVE VALUES
            function parseRupiah(rupiahString) {
                // Check if string contains minus sign
                const isNegative = rupiahString.includes('-');

                // Remove any non-numeric characters except dots, comma and minus sign
                var cleanStr = rupiahString.replace(/[^\d.,\-]/g, '');

                // Ensure minus sign is only at the beginning if present
                if (isNegative) {
                    cleanStr = '-' + cleanStr.replace(/\-/g, '');
                }

                // Replace dots (thousand separators) with empty string and comma with dot
                var normalizedStr = cleanStr.replace(/\./g, '').replace(',', '.');

                // Convert to float
                var number = parseFloat(normalizedStr);

                // Return the number or 0 if parsing failed
                return isNaN(number) ? 0 : number;
            }

            // Handle harga input formatting and update hidden field immediately
            $('#harga_display').on('input blur', function() {
                var value = $(this).val();

                // Check if input starts with minus sign
                const isNegative = value.startsWith('-');

                // Remove non-numeric characters except comma and minus
                value = value.replace(/[^\d,\-]/g, '');

                // Ensure minus sign is only at the beginning
                if (isNegative) {
                    // Remove all minus signs and add one at beginning
                    value = '-' + value.replace(/\-/g, '');
                } else {
                    // Remove any minus signs
                    value = value.replace(/\-/g, '');
                }

                // Ensure only one comma exists
                var commaCount = (value.match(/,/g) || []).length;
                if (commaCount > 1) {
                    value = value.replace(/,/g, function(match, offset, string) {
                        return offset === string.indexOf(',') ? match : '';
                    });
                }

                // Limit to 3 decimal places after comma
                if (value.indexOf(',') !== -1) {
                    var parts = value.split(',');
                    if (parts[1].length > 3) {
                        parts[1] = parts[1].substring(0, 3);
                        value = parts.join(',');
                    }
                }

                // Format the display value with thousand separators
                $(this).val(formatRupiah(value));

                // Update the hidden field with the parsed numeric value
                $('#harga').val(parseRupiah(formatRupiah(value)));
            });

            // Initialize select2 components
            $('#id_kategori_sparepart').select2({
                placeholder: "Pilih Kategori Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAddBypass'),
                width: '100%'
            });

            $('#id_master_data_supplier').select2({
                placeholder: "Pilih Supplier",
                allowClear: true,
                dropdownParent: $('#modalForAddBypass'),
                width: '100%'
            });

            $('#satuan').select2({
                placeholder: "Pilih Satuan",
                allowClear: true,
                dropdownParent: $('#modalForAddBypass'),
                width: '100%'
            });

            // Initialize sparepart select and disable it initially
            $('#id_master_data_sparepart').select2({
                placeholder: "Pilih Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAddBypass'),
                width: '100%'
            }).prop('disabled', true);

            // Function to check if both supplier and category are selected
            function checkDependencies() {
                var supplierId = $('#id_master_data_supplier').val();
                var kategoriId = $('#id_kategori_sparepart').val();
                return supplierId && kategoriId;
            }

            // Function to load spareparts
            function loadSpareparts() {
                var supplierId = $('#id_master_data_supplier').val();
                var kategoriId = $('#id_kategori_sparepart').val();

                $('#id_master_data_sparepart').prop('disabled', true).val(null).trigger('change');

                if (checkDependencies()) {
                    $('.loading-overlay').show();
                    $.ajax({
                        url: '{{ route('spareparts-by-supplier-and-category', ['supplier_id' => ':supplier_id', 'kategori_id' => ':kategori_id']) }}'
                            .replace(':supplier_id', supplierId)
                            .replace(':kategori_id', kategoriId),
                        type: 'GET',
                        success: function(data) {
                            var sparepartSelect = $('#id_master_data_sparepart');
                            sparepartSelect.empty();

                            if (data.length > 0) {
                                sparepartSelect.append('<option value="">Pilih Sparepart</option>');
                                $.each(data, function(key, sparepart) {
                                    sparepartSelect.append('<option value="' + sparepart.id + '">' +
                                        sparepart.nama + ' - ' + sparepart.merk + ' - ' + sparepart.part_number +
                                        '</option>');
                                });
                                sparepartSelect.prop('disabled', false);
                            } else {
                                sparepartSelect.append('<option value="">Tidak ada sparepart tersedia</option>');
                                // Keep dropdown disabled when no spareparts available
                                sparepartSelect.prop('disabled', true);
                            }
                            sparepartSelect.val(null).trigger('change');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            $('#id_master_data_sparepart')
                                .empty()
                                .append('<option value="">Error loading spareparts</option>');
                        },
                        complete: function() {
                            $('.loading-overlay').hide();
                        }
                    });
                }
            }

            // Event handlers for supplier and category changes
            $('#id_master_data_supplier, #id_kategori_sparepart').on('change', function() {
                loadSpareparts();
            });

            // Reset form handler - simplified
            $('#resetButtonBypass').on('click', function() {
                $('#addDataFormBypass')[0].reset();
                $('#id_master_data_supplier, #id_kategori_sparepart, #id_master_data_sparepart').val(null).trigger('change');
                $('#harga').val(''); // Clear hidden field too
                $('.is-invalid').removeClass('is-invalid');
                $('.was-validated').removeClass('was-validated');
            });

            // Handle submit button click - only sets validation but doesn't submit
            $('#submitButtonBypass').on('click', function() {
                const form = $('#addDataFormBypass');
                form.addClass('was-validated');

                // Ensure price field is updated one more time before validation
                const hargaFormatted = $('#harga_display').val();
                const hargaValue = parseRupiah(hargaFormatted);
                $('#harga').val(hargaValue);

                // Only validate - let the native form submission or form-submit-handler do the rest
                if (!validateForm()) {
                    return false;
                }
            });

            // Function to validate form - UPDATED TO ACCEPT NEGATIVE VALUES
            function validateForm() {
                let isValid = true;
                const form = $('#addDataFormBypass');

                // Reset previous validation states
                form.find('.is-invalid').removeClass('is-invalid');

                // Validate required fields
                form.find('[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    }
                });

                // Validate numeric fields
                if (parseInt($('#quantity').val()) < 1) {
                    $('#quantity').addClass('is-invalid');
                    isValid = false;
                }

                // Validate harga - removed the greater than zero check
                const harga = $('#harga_display').val();
                const hargaValue = $('#harga').val();

                if (!harga) {
                    $('#harga_display').addClass('is-invalid');
                    isValid = false;
                } else if (isNaN(parseFloat(hargaValue))) {
                    // Only check if it's a valid number, not if it's positive
                    $('#harga_display').addClass('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Mohon lengkapi semua field yang wajib diisi.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }

                return isValid;
            }

            // Special handler for Enter key in the whole form
            $('#addDataFormBypass').on('keypress', function(e) {
                if (e.which === 13 || e.keyCode === 13) {
                    // Make sure price is updated before validation/submission
                    $('#harga_display').trigger('blur');
                }
            });
        });
    </script>
@endpush
