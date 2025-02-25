@push('styles_3')
@endpush

<div class="fade modal" id="modalForEdit" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Rencana Kebutuhan Barang</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editRKBUrgentForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_alat_edit">Alat</label>
                            <select class="form-control" id="id_master_data_alat_edit" name="id_master_data_alat" required>
                                <option value="">Pilih Alat</option>
                                @foreach ($available_alat as $alat)
                                    <option value="{{ $alat->id }}">{{ $alat->kode_alat }} - {{ $alat->jenis_alat }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Alat diperlukan.</div>
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
                                <option value="Btl">Btl</option>
                                <option value="Ltr">Ltr</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Kg">Kg</option>
                                <option value="Pail">Pail</option>
                                <option value="Drum">Drum</option>
                                <option value="Set">Set</option>
                                <option value="Pack">Pack</option>
                                <option value="Box">Box</option>
                                <option value="Ls">Ls</option>
                                <option value="Ken">Ken</option>
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
                        url: `/spareparts-by-category/${kategoriId}`,
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
                        },
                        error: function() {
                            alert('Gagal memuat sparepart');
                        }
                    });
                }
            });

            // Event listener for edit buttons
            $(document).on('click', '.ubahBtn', function() {
                const id = $(this).data('id'); // Get ID from the button
                fillFormEditDetailRKB(id); // Populate and show the modal
            });
        });

        // Function to display modal for editing and populate form with server data
        function fillFormEditDetailRKB(id) {
            const showUrl = "{{ route('rkb_urgent.detail.show', ':id') }}".replace(':id', id);
            const updateUrl = "{{ route('rkb_urgent.detail.update', ':id') }}".replace(':id', id);

            // console.log("Fetching data for ID:", id);

            // AJAX GET request to fetch data
            $.ajax({
                url: showUrl,
                type: 'GET',
                success: function(response) {
                    // console.log("Data fetched successfully:", response);

                    // Populate fields with data
                    $('#id_master_data_alat_edit').val(response.data.id_master_data_alat).trigger('change');
                    $('#id_kategori_sparepart_sparepart_edit').val(response.data.id_kategori_sparepart_sparepart).trigger('change');

                    $('#quantity_requested_edit').val(response.data.quantity_requested);
                    $('#satuan_edit').val(response.data.satuan).trigger('change');

                    // Set action form to update the specific record with PUT method
                    $('#editRKBUrgentForm').attr('action', updateUrl);

                    // Display the edit modal
                    $('#modalForEdit').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                }
            });
        }
    </script>
@endpush
