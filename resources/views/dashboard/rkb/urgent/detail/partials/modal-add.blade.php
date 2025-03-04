<!-- Modal Add -->
<div class="modal fade" id="modalForAdd" aria-hidden="true" aria-labelledby="modalForAddLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title" id="modalForAddLabel">Tambah Rencana Kebutuhan Barang</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="detailrkburgentForm" style="overflow-y: auto" novalidate method="POST" action="{{ route('rkb_urgent.detail.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input id="id_rkb" name="id_rkb" type="hidden" value="{{ $rkb->id }}">

                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_alat">Alat</label>
                            <select class="form-control" id="id_master_data_alat" name="id_master_data_alat" required>
                                <option value="">Pilih Alat</option>
                                @foreach ($available_alat as $alat)
                                    @if ($alat->kode_alat !== 'Workshop')
                                        <option value="{{ $alat->id }}">{{ $alat->kode_alat }} - {{ $alat->jenis_alat }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Alat diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="nama_koordinator">Nama Koordinator</label>
                            <input class="form-control" id="nama_koordinator" name="nama_koordinator" type="text" placeholder="Nama Koordinator" required>
                            <div class="invalid-feedback">Nama Koordinator diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="kronologi">Kronologi</label>
                            <textarea class="form-control" id="kronologi" name="kronologi" rows="3" placeholder="Kronologi" required></textarea>
                            <div class="invalid-feedback">Kronologi diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="dokumentasi">Dokumentasi</label>
                            <input class="form-control" id="dokumentasiInput" name="dokumentasi[]" type="file" accept="image/*" multiple required>
                            <div class="invalid-feedback" id="dokumentasi-invalid-feedback">
                                Dokumentasi diperlukan.
                            </div>
                            <div class="mt-3 d-flex flex-wrap gap-2" id="dokumentasiPreview"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_kategori_sparepart_sparepart">Kategori Sparepart</label>
                            <select class="form-control" id="id_kategori_sparepart_sparepart" name="id_kategori_sparepart_sparepart" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori_sparepart as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->kode }}: {{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Kategori diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_sparepart">Sparepart</label>
                            <select class="form-control" id="id_master_data_sparepart" name="id_master_data_sparepart" required>
                                <option value="">Pilih Sparepart</option>
                                @foreach ($master_data_sparepart as $sparepart)
                                    <option value="{{ $sparepart->id }}">{{ $sparepart->nama }} - {{ $sparepart->part_number }} - {{ $sparepart->merk }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity_requested">Quantity</label>
                            <input class="form-control" id="quantity_requested" name="quantity_requested" type="number" min="1" placeholder="Quantity" required>
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
        // Define clearPreview function globally
        function clearPreview() {
            const dokumentasiPreview = document.getElementById('dokumentasiPreview');
            if (dokumentasiPreview) {
                dokumentasiPreview.innerHTML = '';
            }
        }

        $(document).ready(function() {
            'use strict';

            const $form = $('#detailrkburgentForm');

            $form.on('submit', function(event) {
                if (!$form[0].checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                $form.addClass('was-validated');
            });

            // Initialize select2 for dropdowns
            const $select2Elements = $('#id_master_data_alat, #id_kategori_sparepart_sparepart, #id_master_data_sparepart, #satuan');
            $select2Elements.select2({
                placeholder: "Pilih",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Apply validation on blur (out of focus) for each input in the form
            const formElement = $form[0]; // Get the raw DOM element
            formElement.querySelectorAll('input').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });

            // Handle kategori_sparepart change
            $('#id_kategori_sparepart_sparepart').on('change', function() {
                const kategoriId = $(this).val();
                const $sparepartSelect = $('#id_master_data_sparepart');

                // Clear the sparepart select
                $sparepartSelect.empty().trigger('change');

                // Show loading spinner
                const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#modalForAdd').append($loadingOverlay);

                // Disable sparepart dropdown
                $sparepartSelect.prop('disabled', true);

                if (kategoriId) {
                    $.ajax({
                        url: `/spareparts-by-category/${kategoriId}`,
                        type: 'GET',
                        success: function(data) {
                            // Add a placeholder option
                            $sparepartSelect.append(new Option('Pilih Sparepart', '', true, true));

                            // Populate the select with new data
                            $.each(data, function(index, sparepart) {
                                $sparepartSelect.append(new Option(
                                    `${sparepart.nama} - ${sparepart.part_number} - ${sparepart.merk}`,
                                    sparepart.id
                                ));
                            });

                            // Refresh the select2 dropdown
                            $sparepartSelect.trigger('change');

                            // Enable sparepart dropdown
                            $sparepartSelect.prop('disabled', false);

                            // Remove loading spinner
                            $loadingOverlay.remove();
                        },
                        error: function() {
                            alert('Gagal memuat sparepart');

                            // Enable sparepart dropdown even if there's an error
                            $sparepartSelect.prop('disabled', false);

                            // Remove loading spinner
                            $loadingOverlay.remove();
                        }
                    });
                } else {
                    // Enable sparepart dropdown and remove spinner if no kategori is selected
                    $sparepartSelect.prop('disabled', false);
                    $loadingOverlay.remove();
                }
            });

            // Handle reset button click
            $('button[type="reset"]').on('click', function() {
                // Reset all select2 elements to default placeholder
                $select2Elements.each(function() {
                    $(this).val(null).trigger('change');
                });
                clearPreview(); // Add this line to clear the preview images
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
                        img.style.width = '100px';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        img.style.border = '1px solid #ccc';
                        img.style.borderRadius = '4px';
                        img.title = file.name;
                        img.classList.add('preview-image');

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
            const form = document.getElementById('detailrkburgentForm');
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

                if (target.tagName === 'IMG' && target.classList.contains('preview-image')) {
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

        $('#detailrkburgentForm').on('submit', function(event) {
            if (this.checkValidity()) {
                $(this).find('button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            }
        });
    </script>
@endpush
