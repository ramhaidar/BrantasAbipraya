@include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.modal-preview')

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data ATB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto" id="addDataForm" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="p-0 m-0" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <select class="form-control" id="pilihan-proyek1" name="tipe">
                                <option value="hutang-unit-alat" {{ $page == 'Data ATB Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                <option value="panjar-unit-alat" {{ $page == 'Data ATB Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                <option value="mutasi-proyek" {{ $page == 'Data ATB Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                <option value="panjar-proyek" {{ $page == 'Data ATB Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                            </select>
                        </div>

                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal" name="tanggal" type="text" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" autocomplete="off" placeholder="Tanggal Masuk Sparepart" required>
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
                                <option value="Box">Box</option>
                                <option value="Btl">Btl</option>
                                <option value="Drum">Drum</option>
                                <option value="Ken">Ken</option>
                                <option value="Kg">Kg</option>
                                <option value="Ls">Ls</option>
                                <option value="Ltr">Ltr</option>
                                <option value="Pack">Pack</option>
                                <option value="Pail">Pail</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Set">Set</option>
                            </select>
                            <div class="invalid-feedback">Satuan diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="harga">Harga (Rp.)</label>
                            <input class="form-control" id="harga" name="harga" type="number" min="0" placeholder="Harga" required>
                            <div class="invalid-feedback">Harga diperlukan.</div>
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

            // Validate form on submit button click
            $('#submitButton').on('click', function() {
                if ($('#addDataForm')[0].checkValidity()) {
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
