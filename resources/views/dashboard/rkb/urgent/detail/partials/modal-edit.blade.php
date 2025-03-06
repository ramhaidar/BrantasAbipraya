<div class="fade modal" id="modalForEdit" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title" id="modalForEditLabel">Ubah Rencana Kebutuhan Barang</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editRKBUrgentForm" style="overflow-y: auto" novalidate method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <input id="id_rkb" name="id_rkb" type="hidden" value="{{ $rkb->id }}">

                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_alat_edit">Alat</label>
                            <select class="form-control" id="id_master_data_alat_edit" name="id_master_data_alat" required>
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
                            <label class="form-label required" for="nama_koordinator_edit">Nama Koordinator</label>
                            <input class="form-control" id="nama_koordinator_edit" name="nama_koordinator" type="text" placeholder="Nama Koordinator" required>
                            <div class="invalid-feedback">Nama Koordinator diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="kronologi_edit">Kronologi</label>
                            <textarea class="form-control" id="kronologi_edit" name="kronologi" rows="3" placeholder="Kronologi" required></textarea>
                            <div class="invalid-feedback">Kronologi diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="dokumentasi_edit">Dokumentasi Baru</label>
                            <input class="form-control" id="dokumentasiInputEdit" name="dokumentasi[]" type="file" accept="image/*" multiple>
                            <div class="invalid-feedback" id="dokumentasi-invalid-feedback-edit">
                                Dokumentasi diperlukan.
                            </div>
                            <div class="mt-3 d-flex flex-wrap gap-2" id="dokumentasiPreviewEdit"></div>

                            <div class="mt-3">
                                <label class="form-label">Dokumentasi Saat Ini</label>
                                <div class="d-flex flex-wrap gap-2" id="existing-dokumentasi"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_kategori_sparepart_sparepart_edit">Kategori Sparepart</label>
                            <select class="form-control" id="id_kategori_sparepart_sparepart_edit" name="id_kategori_sparepart_sparepart" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategori_sparepart as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->kode }}: {{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Kategori diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_sparepart_edit">Sparepart</label>
                            <select class="form-control" id="id_master_data_sparepart_edit" name="id_master_data_sparepart" required>
                                <option value="">Pilih Sparepart</option>
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity_requested_edit">Quantity</label>
                            <input class="form-control" id="quantity_requested_edit" name="quantity_requested" type="number" min="1" placeholder="Quantity" required>
                            <div class="invalid-feedback">Quantity diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="satuan_edit">Satuan</label>
                            <select class="form-control" id="satuan_edit" name="satuan" required>
                                <option value="">Pilih Satuan</option>
                                <option value="Box">Box</option>
                                <option value="Btl">Btg</option>
                                <option value="Btl">Btl</option>
                                <option value="Drum">Drum</option>
                                <option value="Ken">Ken</option>
                                <option value="Kg">Kg</option>
                                <option value="Ktk">Ktk</option>
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
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-rkburgent" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        // Define clearPreviewEdit function globally
        function clearPreviewEdit() {
            const dokumentasiPreview = document.getElementById('dokumentasiPreviewEdit');
            if (dokumentasiPreview) {
                dokumentasiPreview.innerHTML = '';
            }
        }

        const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        $(document).ready(function() {
            'use strict';

            // Initialize select2 for dropdowns
            const $select2Elements = $('#id_master_data_alat_edit, #id_kategori_sparepart_sparepart_edit, #id_master_data_sparepart_edit, #satuan_edit');
            $select2Elements.select2({
                placeholder: "Pilih",
                allowClear: true,
                dropdownParent: $('#modalForEdit'),
                width: '100%'
            });

            // Bind change event to kategori_sparepart_edit using event delegation
            $(document).on('change', '#id_kategori_sparepart_sparepart_edit', function() {
                const kategoriId = $(this).val();
                const $sparepartSelect = $('#id_master_data_sparepart_edit');

                // console.log("Kategori Sparepart changed:", kategoriId);

                // Clear the sparepart select and reset
                $sparepartSelect.append(new Option('Pilih Sparepart', '', false, false));
                // $sparepartSelect.val(null).trigger('change');

                if (kategoriId) {
                    $.ajax({
                        url: "{{ route('spareparts-by-category', ':id') }}".replace(':id', kategoriId),
                        type: 'GET',
                        success: function(data) {
                            // console.log("Spareparts loaded:", data);

                            $sparepartSelect.empty();

                            // Populate the select with new data
                            $.each(data, function(index, sparepart) {
                                $sparepartSelect.append(new Option(
                                    `${sparepart.nama} - ${sparepart.part_number} - ${sparepart.merk}`,
                                    sparepart.id
                                ));
                            });

                            // Refresh the select2 dropdown
                            $sparepartSelect.trigger('change');

                            $loadingOverlay.remove();
                        },
                        error: function() {
                            alert('Gagal memuat sparepart');

                            $loadingOverlay.remove();
                        }
                    });
                }
            });

            // Add file handling for edit form
            const dokumentasiInputEdit = document.getElementById('dokumentasiInputEdit');
            const dokumentasiPreviewEdit = document.getElementById('dokumentasiPreviewEdit');

            dokumentasiInputEdit.addEventListener('change', function() {
                clearPreviewEdit();

                const files = Array.from(this.files);
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

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewElement = document.createElement('div');
                        previewElement.classList.add('d-flex', 'flex-column', 'align-items-center', 'me-2', 'mb-2');

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        img.classList.add('img-thumbnail', 'mb-1', 'preview-image');
                        img.style.maxWidth = '100px';
                        img.style.maxHeight = '100px';
                        img.style.objectFit = 'cover';
                        img.title = file.name;

                        // Add click event for preview modal
                        img.addEventListener('click', function() {
                            $('#largeImagePreviewForEdit').attr('src', e.target.result);
                            $('#imagePreviewTitleForEdit').text(file.name);
                            $('#modalForEdit').modal('hide');
                            $('#imagePreviewModalforEdit').modal('show');
                        });

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.classList.add('btn', 'btn-danger', 'btn-sm');
                        removeBtn.textContent = 'Remove';
                        removeBtn.onclick = function() {
                            previewElement.remove();
                            const remainingFiles = Array.from(dokumentasiInputEdit.files)
                                .filter(f => f !== file);
                            const dataTransfer = new DataTransfer();
                            remainingFiles.forEach(f => dataTransfer.items.add(f));
                            dokumentasiInputEdit.files = dataTransfer.files;
                        };

                        previewElement.appendChild(img);
                        previewElement.appendChild(removeBtn);
                        dokumentasiPreviewEdit.appendChild(previewElement);
                    };
                    reader.readAsDataURL(file);
                });
            });

            // Add event listener for existing images preview
            $(document).on('click', '#existing-dokumentasi img', function() {
                $('#largeImagePreviewForEdit').attr('src', this.src);
                $('#imagePreviewTitleForEdit').text(this.alt);
                $('#modalForEdit').modal('hide');
                $('#imagePreviewModalforEdit').modal('show');
            });

            // Handle modal transitions
            $('#imagePreviewModalforEdit').on('hidden.bs.modal', function() {
                $('#modalForEdit').modal('show');
            });

            $('#editRKBUrgentForm').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            });
        });

        // Function to display modal for editing and populate form with server data
        function fillFormEditDetailRKB(id) {
            const showUrl = "{{ route('rkb_urgent.detail.show', ':id') }}".replace(':id', id);
            const updateUrl = "{{ route('rkb_urgent.detail.update', ':id') }}".replace(':id', id);

            // console.log("Fetching data for ID:", id);

            $('#modalForEdit').append($loadingOverlay);

            // Display the edit modal
            $('#modalForEdit').modal('show');

            // AJAX GET request to fetch data
            $.ajax({
                url: showUrl,
                type: 'GET',
                success: function(response) {
                    // console.log("Data fetched successfully:", response);

                    // Populate fields with data
                    $('#id_master_data_alat_edit').val(response.data.id_master_data_alat).trigger('change');
                    $('#nama_koordinator_edit').val(response.data.nama_koordinator); // Fix this line
                    $('#kronologi_edit').val(response.data.kronologi); // Fix this line
                    $('#id_kategori_sparepart_sparepart_edit').val(response.data.id_kategori_sparepart_sparepart).trigger('change');
                    $('#quantity_requested_edit').val(response.data.quantity_requested);
                    $('#satuan_edit').val(response.data.satuan).trigger('change');

                    // Display existing dokumentasi
                    const existingDokumentasi = document.getElementById('existing-dokumentasi');
                    existingDokumentasi.innerHTML = '';

                    if (response.data.dokumentasi && response.data.dokumentasi.length > 0) {
                        response.data.dokumentasi.forEach(img => {
                            const imgContainer = document.createElement('div');
                            imgContainer.className = 'd-flex flex-column align-items-center me-2 mb-2';

                            const imgElement = document.createElement('img');
                            imgElement.src = img.url;
                            imgElement.alt = img.name;
                            imgElement.title = img.name;
                            imgElement.className = 'img-thumbnail mb-1';
                            imgElement.style.maxWidth = '100px';
                            imgElement.style.maxHeight = '100px';
                            imgElement.style.cursor = 'pointer';

                            // Add click event for preview modal
                            imgElement.onclick = function() {
                                $('#largeImagePreviewForEdit').attr('src', img.url);
                                $('#imagePreviewTitleForEdit').text(img.name);
                                $('#modalForEdit').modal('hide');
                                $('#imagePreviewModalforEdit').modal('show');
                            };

                            imgContainer.appendChild(imgElement);
                            existingDokumentasi.appendChild(imgContainer);
                        });
                    } else {
                        existingDokumentasi.innerHTML = '<p class="text-muted">No existing dokumentasi</p>';
                    }

                    // Set action form to update the specific record with PUT method
                    $('#editRKBUrgentForm').attr('action', updateUrl);
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                    $loadingOverlay.remove();
                }
            });
        }

        // Handle modal transitions for edit preview
        $('#imagePreviewModalforEdit').on('hidden.bs.modal', function() {
            $('#modalForEdit').modal('show');
        });
    </script>
@endpush
