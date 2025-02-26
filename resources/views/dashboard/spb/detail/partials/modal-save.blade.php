<!-- Modal for saving SPB -->
<div class="fade modal" id="modalForSaveSPB" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForSaveSPBLabel">Konfirmasi Penyimpanan SPB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="spb_addendum">Pilih SPB untuk Addendum (Opsional)</label>
                    <select class="form-select" id="spb_addendum" name="spb_addendum">
                        <option value="">Tidak Ada</option>
                        @foreach ($spbAddendumEd as $spb)
                            <option value="{{ $spb->id }}">{{ $spb->nomor }}</option>
                        @endforeach
                    </select>
                    <input id="spb_addendum_input" name="spb_addendum_id" type="hidden" value="">
                </div>
                <p class="p-0 m-0">Apakah Anda yakin ingin menyimpan SPB dari Detail RKB Urgent ini?</p>
                <p class="p-0 m-0">Pastikan semua data telah diisi dengan benar sebelum menyimpan SPB.</p>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success w-25" id="confirmSaveSPBButton">Buat SPB</button>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('#spb_addendum').select2({
                width: '100%',
                placeholder: 'Pilih SPB Addendum',
                dropdownParent: $('#modalForSaveSPB'),
                allowClear: true
            }).on('change', function() {
                // Update hidden input when spb_addendum selection changes
                $('#spb_addendum_input').val($(this).val());
            });

            $(document).on('click', '.saveSPBBtn', function() {
                const id = $(this).data('id');
                showModalSaveSPB(id);
            });

            function showModalSaveSPB(id) {
                $('#confirmSaveSPBButton').data('id', id);
                $('#modalForSaveSPB').modal('show');
            }

            $('#confirmSaveSPBButton').on('click', function() {
                // Get the form
                const form = $('#detailSpbForm');

                // Cek apakah supplier sudah dipilih
                if (!$('#supplier_main').val()) {
                    alert('Silakan pilih supplier terlebih dahulu');
                    $('#modalForSaveSPB').modal('hide');
                    return;
                }

                // Flag to track if at least one sparepart is completely filled
                let hasValidRow = false;

                // We'll track any validation errors here
                let validationErrors = [];

                // Process each row to check for valid entries
                $('.sparepart-select').each(function() {
                    const row = $(this).closest('tr');
                    const detailId = $(this).attr('id').replace('sparepart-', '');
                    const qtyInput = row.find('input[name^="qty"]');
                    const hargaInput = row.find('.harga-input');
                    const sparepartValue = $(this).val();

                    // Only validate rows where sparepart is selected
                    if (sparepartValue) {
                        const qtyValue = parseFloat(qtyInput.val());
                        const hargaValue = parseRupiahValue(hargaInput.val());

                        // Check if this row has all required values filled
                        if (qtyValue > 0 && hargaValue > 0) {
                            hasValidRow = true;

                            // Clear any error highlighting
                            qtyInput.removeClass('is-invalid');
                            hargaInput.removeClass('is-invalid');
                        } else {
                            // Mark the specific issues for this row
                            if (qtyValue <= 0) {
                                qtyInput.addClass('is-invalid');
                                validationErrors.push(`Quantity harus lebih besar dari 0`);
                            }

                            if (hargaValue <= 0) {
                                hargaInput.addClass('is-invalid');
                                validationErrors.push(`Harga harus lebih besar dari 0`);
                            }
                        }
                    }
                });

                // Check if we have at least one valid row
                if (!hasValidRow) {
                    if ($('.sparepart-select option:selected[value]').length === 0) {
                        // No spareparts selected at all
                        alert('Silakan pilih minimal satu sparepart');
                    } else {
                        // Spareparts selected but not completely filled
                        let errorMessage = 'Untuk setiap sparepart yang dipilih:';
                        if (validationErrors.length > 0) {
                            // Show unique errors only
                            const uniqueErrors = [...new Set(validationErrors)];
                            errorMessage += '\n- ' + uniqueErrors.join('\n- ');
                        }
                        alert(errorMessage);
                    }

                    $('#modalForSaveSPB').modal('hide');
                    return;
                }

                // If we got here, we have at least one valid row, proceed with submission
                $('#confirmSaveSPBButton').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

                // Update all hidden values for harga inputs before submission
                $('.harga-input').each(function() {
                    const detailId = $(this).attr('id').replace('harga-', '');
                    const sparepartSelected = $('#sparepart-' + detailId).val();

                    // Only update hidden values for rows with selected spareparts
                    if (sparepartSelected) {
                        const hargaValue = parseRupiahValue($(this).val());
                        $('#harga-hidden-' + detailId).val(hargaValue);
                    }
                });

                // Submit the form
                form.submit();
            });

            // Helper function to parse rupiah formatted values
            function parseRupiahValue(rupiahString) {
                if (!rupiahString) return 0;

                // Remove prefix and clean the string
                var cleanStr = rupiahString.replace(/^Rp\s+/, '').replace(/[^\d,\.]/g, '').trim();

                // Replace thousand separators and convert decimal separator
                return parseFloat(cleanStr.replace(/\./g, '').replace(',', '.')) || 0;
            }
        });
    </script>
@endpush
