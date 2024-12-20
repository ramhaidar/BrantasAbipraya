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
            $(document).on('click', '.saveSPBBtn', function() {
                const id = $(this).data('id');
                showModalSaveSPB(id);
            });

            function showModalSaveSPB(id) {
                $('#confirmSaveSPBButton').data('id', id);
                $('#modalForSaveSPB').modal('show');
            }

            $('#confirmSaveSPBButton').on('click', function() {
                // Validasi form sebelum submit
                const form = $('#detailSpbForm');

                // Cek apakah supplier sudah dipilih
                if (!$('#supplier_main').val()) {
                    alert('Silakan pilih supplier terlebih dahulu');
                    return;
                }

                // Cek apakah ada sparepart yang dipilih
                let hasSelectedSparepart = false;
                $('.sparepart-select').each(function() {
                    if ($(this).val()) {
                        hasSelectedSparepart = true;
                        return false; // break the loop
                    }
                });

                if (!hasSelectedSparepart) {
                    alert('Silakan pilih minimal satu sparepart');
                    return;
                }

                // Cek apakah semua quantity yang diisi valid
                let isQuantityValid = true;
                $('input[name^="qty"]').each(function() {
                    if (!$(this).prop('disabled') && ($(this).val() <= 0 || $(this).val() > $(this)
                            .attr('max'))) {
                        isQuantityValid = false;
                        return false; // break the loop
                    }
                });

                if (!isQuantityValid) {
                    alert('Pastikan quantity yang diisi valid dan tidak melebihi batas maksimum');
                    return;
                }

                // Cek apakah harga sudah diisi untuk setiap item yang dipilih
                let isPriceValid = true;
                $('input[name^="harga"]').each(function() {
                    if (!$(this).prop('disabled') && ($(this).val() === 'Rp 0' || $(this).val() === 'Rp0')) {
                        isPriceValid = false;
                        return false; // break the loop
                    }
                });

                if (!isPriceValid) {
                    alert('Pastikan harga sudah diisi untuk setiap item yang dipilih');
                    return;
                }

                // Submit form jika semua validasi berhasil
                form.submit();
                $('#modalForSaveSPB').modal('hide');
            });
        });
    </script>
@endpush
