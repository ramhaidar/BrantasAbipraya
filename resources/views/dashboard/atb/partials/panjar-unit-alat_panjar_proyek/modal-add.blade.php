@include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.modal-preview')

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data ATB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto needs-validation" id="addDataForm" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="p-0 m-0" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <select class="form-control" name="tipe">
                                <option value="hutang-unit-alat" {{ $page == 'Data ATB Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                <option value="panjar-unit-alat" {{ $page == 'Data ATB Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                <option value="mutasi-proyek" {{ $page == 'Data ATB Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                <option value="panjar-proyek" {{ $page == 'Data ATB Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                            </select>
                        </div>

                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal" name="tanggal" type="text" autocomplete="off" placeholder="Tanggal Masuk Sparepart" required>
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
                                <option value="Assy">Assembly</option>
                                <option value="Box">Box</option>
                                <option value="Btg">Batang</option>
                                <option value="Btl">Botol</option>
                                <option value="Cm">Cm</option>
                                <option value="Drum">Drum</option>
                                <option value="Gln">Galon</option>
                                <option value="Ken">Ken</option>
                                <option value="Kg">Kilogram</option>
                                <option value="Krg">Karung</option>
                                <option value="Ktk">Kotak</option>
                                <option value="Lbr">Lebar</option>
                                <option value="Ls">Lump Sum</option>
                                <option value="Ltr">Liter</option>
                                <option value="M">Meter</option>
                                <option value="Pack">Pack</option>
                                <option value="Pail">Pail</option>
                                <option value="Pcs">Pieces</option>
                                <option value="Roll">Roll</option>
                                <option value="Set">Set</option>
                                <option value="Tbg">Tabung</option>
                            </select>
                            <div class="invalid-feedback">Satuan diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="harga">Harga (Rp.)</label>
                            <input class="form-control" id="harga" name="harga" type="text" placeholder="Harga" required>
                            <div class="invalid-feedback">Harga diperlukan dan harus berupa angka dengan format yang benar.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="dokumentasi">Dokumentasi</label>
                            <input class="form-control" id="dokumentasiInput" name="dokumentasi[]" type="file" accept="image/*" multiple required>
                            <div class="invalid-feedback" id="dokumentasi-invalid-feedback">
                                Dokumentasi diperlukan.
                            </div>
                            <div class="mt-3 d-flex flex-wrap gap-2" id="dokumentasiPreview"></div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" id="resetButton" type="button">Reset</button>
                    <button class="btn btn-success w-25" id="submitButton" type="button">Tambah Data</button>
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

            // Format number function for Indonesian locale
            function formatRupiah(angka, prefix) {
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
                return prefix === undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
            }

            // Parse Indonesian formatted number back to standard decimal
            function parseRupiah(rupiahString) {
                return parseFloat(rupiahString.replace(/\./g, '').replace(',', '.'));
            }

            // Handle harga input formatting
            $('#harga').on('input', function() {
                var value = $(this).val();

                // Remove non-numeric characters except comma
                value = value.replace(/[^\d,]/g, '');

                // Ensure only one comma exists
                var commaCount = (value.match(/,/g) || []).length;
                if (commaCount > 1) {
                    value = value.replace(/,/g, function(match, offset, string) {
                        return offset === string.indexOf(',') ? match : '';
                    });
                }

                // Limit to 3 decimal places after comma (changed from 2)
                if (value.indexOf(',') !== -1) {
                    var parts = value.split(',');
                    if (parts[1].length > 3) {
                        parts[1] = parts[1].substring(0, 3);
                        value = parts.join(',');
                    }
                }

                // Format the number with thousand separators
                $(this).val(formatRupiah(value));
            });

            // Initialize select2 components
            $('#id_kategori_sparepart').select2({
                placeholder: "Pilih Kategori Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            $('#id_master_data_supplier').select2({
                placeholder: "Pilih Supplier",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            $('#satuan').select2({
                placeholder: "Pilih Satuan",
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

            // Reset form handler
            $('#resetButton').on('click', function() {
                $('#addDataForm')[0].reset();
                $('#id_master_data_supplier, #id_kategori_sparepart, #id_master_data_sparepart').val(null).trigger('change');
                clearPreview();
            });

            // Validate form on submit button click
            $('#submitButton').on('click', function() {
                if ($('#addDataForm')[0].checkValidity()) {
                    // Convert the formatted price back to standard decimal before submit
                    const hargaFormatted = $('#harga').val();
                    const hargaValue = parseRupiah(hargaFormatted);

                    // Create a hidden input to store the converted value
                    if ($('#hargaHidden').length === 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'hargaHidden',
                            name: 'harga',
                            value: hargaValue
                        }).appendTo($('#addDataForm'));

                        // Remove the name attribute from the original field to avoid duplicates
                        $('#harga').removeAttr('name');
                    } else {
                        $('#hargaHidden').val(hargaValue);
                    }

                    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
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

        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            const dokumentasiInput = document.getElementById('dokumentasiInput');
            const dokumentasiPreview = document.getElementById('dokumentasiPreview');
            const invalidFeedback = document.getElementById('dokumentasi-invalid-feedback');
            const largeImagePreviewForAdd = document.getElementById('largeImagePreviewForAdd');
            const imagePreviewTitleForAdd = document.getElementById('imagePreviewTitleForAdd');
            const imagePreviewModalforAdd = new bootstrap.Modal(document.getElementById('imagePreviewModalforAdd'));

            // Validate the file input
            const validateFiles = () => {
                if (dokumentasiInput.files.length === 0) {
                    dokumentasiInput.classList.add('is-invalid');
                    invalidFeedback.style.display = 'block';
                } else {
                    dokumentasiInput.classList.remove('is-invalid');
                    invalidFeedback.style.display = 'none';
                }
            };

            // Handle file input change
            dokumentasiInput.addEventListener('change', function() {
                clearPreview(); // Clear existing previews

                const files = Array.from(dokumentasiInput.files);
                const maxFileSize = 2 * 1024 * 1024; // 2 MB

                files.forEach((file) => {
                    if (!file.type.startsWith('image/')) {
                        alert(`File "${file.name}" is not an image.`);
                        return;
                    }

                    if (file.size > maxFileSize) {
                        alert(`File "${file.name}" exceeds the 2 MB size limit.`);
                        return;
                    }

                    // Create preview for each valid image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        img.classList.add('img-thumbnail');
                        img.title = file.name;

                        // Add click event to open large preview modal
                        img.addEventListener('click', function() {
                            largeImagePreviewForAdd.src = e.target.result;
                            imagePreviewTitleForAdd.textContent = file.name;
                            $('#modalForAdd').modal('hide'); // Hide #modalForAdd
                            imagePreviewModalforAdd.show();
                        });

                        // Create a remove button
                        const removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.textContent = 'Remove';
                        removeButton.classList.add('btn', 'btn-sm', 'btn-danger', 'mt-2');
                        removeButton.onclick = () => {
                            img.remove();
                            removeButton.remove();
                            const remainingFiles = Array.from(dokumentasiInput.files).filter((f) => f !== file);
                            const dataTransfer = new DataTransfer();
                            remainingFiles.forEach((f) => dataTransfer.items.add(f));
                            dokumentasiInput.files = dataTransfer.files;
                            validateFiles();
                        };

                        const previewContainer = document.createElement('div');
                        previewContainer.classList.add('d-flex', 'flex-column', 'align-items-center', 'me-2');
                        previewContainer.appendChild(img);
                        previewContainer.appendChild(removeButton);

                        dokumentasiPreview.appendChild(previewContainer);
                    };
                    reader.readAsDataURL(file);
                });

                validateFiles();
            });

            // Validate on form submission
            const form = document.getElementById('addDataForm');
            form.addEventListener('submit', function(event) {
                validateFiles();
                if (!form.checkValidity() || dokumentasiInput.files.length === 0) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Event listener for image thumbnail clicks
            dokumentasiPreview.addEventListener('click', function(event) {
                const target = event.target;

                if (target.tagName === 'IMG' && target.classList.contains('img-thumbnail')) {
                    // Set the large image preview source
                    largeImagePreviewForAdd.src = target.src;

                    // Set the modal title with the image file name
                    imagePreviewTitleForAdd.textContent = target.title;

                    // Hide #modalForAdd using jQuery
                    $('#modalForAdd').modal('hide');
                }
            });

            // Event listener for when the preview modal is closed
            document.getElementById('imagePreviewModalforAdd').addEventListener('hidden.bs.modal', function() {
                // Reopen #modalForAdd using jQuery
                $('#modalForAdd').modal('show');
            });
        });
    </script>
@endpush

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Add date restriction logic at the beginning
            const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};

            // Destroy existing datepicker to reinitialize with our settings
            $('#tanggal').datepicker('destroy');

            if (isKoordinatorProyek) {
                // Calculate valid date range (26th of previous month to 25th of current month)
                const today = new Date();
                const startDate = new Date(today.getFullYear(), today.getMonth() - 1, 26);
                const endDate = new Date(today.getFullYear(), today.getMonth(), 25);

                // Format dates for display
                const formatDate = (date) => {
                    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
                };

                // Initialize datepicker with strict constraints
                $('#tanggal').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    minDate: startDate,
                    maxDate: endDate,
                    beforeShowDay: function(date) {
                        return [date >= startDate && date <= endDate, ''];
                    },
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set initial date
                let initialDate;
                if (today >= startDate && today <= endDate) {
                    initialDate = today;
                } else if (today > endDate) {
                    initialDate = endDate;
                } else {
                    initialDate = startDate;
                }
                $('#tanggal').datepicker('setDate', initialDate);

                // Update placeholder and validation message
                $('#tanggal').attr('placeholder',
                    `Tanggal antara ${formatDate(startDate)} - ${formatDate(endDate)}`);
                $('#tanggal').closest('div').find('.invalid-feedback').text(
                    `Tanggal harus antara ${formatDate(startDate)} - ${formatDate(endDate)}`);

                // Add custom validation
                $('#tanggal').on('change', function() {
                    try {
                        const input = $(this).val();
                        if (!input) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const parts = input.split('-');
                        if (parts.length !== 3) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const inputDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));

                        if (isNaN(inputDate) || inputDate < startDate || inputDate > endDate) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            $(this).datepicker('setDate', initialDate);
                            return false;
                        } else {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                            return true;
                        }
                    } catch (e) {
                        console.error('Date validation error:', e);
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                });
            } else {
                // FOR OTHER ROLES - Initialize without date restrictions
                $('#tanggal').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set today as default date
                $('#tanggal').datepicker('setDate', new Date());
            }

            // ...existing code...
        });
    </script>
@endpush
