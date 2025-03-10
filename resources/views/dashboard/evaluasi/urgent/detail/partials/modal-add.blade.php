@push('styles_3')
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Rencana Kebutuhan Barang</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="detailrkburgentForm" style="overflow-y: auto" novalidate method="POST" action="{{ route('rkb_urgent.detail.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Hidden ID RKB Input --}}
                        <input id="id_rkb" name="id_rkb" type="hidden" value="{{ $rkb->id }}">

                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_alat">Alat</label>
                            <select class="form-control" id="id_master_data_alat" name="id_master_data_alat" required>
                                <option value="">Pilih Alat</option>
                                @foreach ($available_alat as $alat)
                                    <option value="{{ $alat->id }}">{{ $alat->kode_alat }} - {{ $alat->jenis_alat }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Alat diperlukan.</div>
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

                        <!-- New Fields for Detail RKB Urgent -->
                        <div class="col-12">
                            <label class="form-label required" for="quantity_requested">Quantity</label>
                            <input class="form-control" id="quantity_requested" name="quantity_requested" type="number" min="1" placeholder="Quantity" required>
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
        $(document).ready(function() {
            'use strict';

            const $form = $('#detailrkburgentForm');

            $form.on('submit', function(event) {
                if (!$form[0].checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    $('#add-sparepart').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
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

            // Handle kategori_sparepart change
            $('#id_kategori_sparepart_sparepart').on('change', function() {
                const kategoriId = $(this).val();
                const $sparepartSelect = $('#id_master_data_sparepart');

                // Clear the sparepart select
                $sparepartSelect.empty().trigger('change');

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
                        },
                        error: function() {
                            alert('Gagal memuat sparepart');
                        }
                    });
                }
            });

            // Handle reset button click
            $('button[type="reset"]').on('click', function() {
                // Reset all select2 elements to default placeholder
                $select2Elements.each(function() {
                    $(this).val(null).trigger('change');
                });
            });
        });
    </script>
@endpush
