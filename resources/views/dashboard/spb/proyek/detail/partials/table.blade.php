@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            vertical-align: middle;
            text-align: center;
        }

        .currency-value {
            text-align: right !important;
            padding-right: 10px !important;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Nama Alat</th>
                    <th class="text-center">Kode Alat</th>
                    <th class="text-center">Kategori</th>
                    <th class="text-center">Sparepart PO</th>
                    <th class="text-center">Merk</th>
                    <th class="text-center">Supplier</th>
                    <th class="text-center">Quantity PO</th>
                    <th class="text-center">Quantity Diterima</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    @php
                        $details = isset($item->originalSpb) ? $item->originalSpb->linkSpbDetailSpb : $item->linkSpbDetailSpb;
                    @endphp

                    @forelse ($details as $detail)
                        <tr>
                            <td class="text-center">{{ $detail->detailSPB->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->kategoriSparepart->kode }}: {{ $detail->detailSPB->masterDataSparepart->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item->masterDataSupplier->nama }}</td>
                            <td class="text-center">{{ $item->linkSpbDetailSpb[$loop->index]->detailSPB->quantity_po }}</td>
                            <td class="text-center">{{ $detail->detailSPB->atbs->sum('quantity') }}</td>
                            <td class="text-center">{{ $detail->detailSPB->satuan }}</td>
                            <td class="currency-value">{{ number_format($detail->detailSPB->harga, 0, ',', '.') }}</td>
                            <td class="currency-value">{{ number_format($detail->detailSPB->harga * $detail->detailSPB->quantity_po, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="11">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data SPB
                            </td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="11">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SPB
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-primary">
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="9">Jumlah</th>
                    <th class="currency-value" id="totalHarga">0</th>
                    <th class="currency-value" id="totalJumlahHarga">0</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="10">PPN 11%</th>
                    <th class="currency-value" id="ppn11">0</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="10">Grand Total</th>
                    <th class="currency-value" id="grandTotal">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            function formatRibuan(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatRibuan(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            function updateJumlahHarga(row) {
                const harga = unformatRibuan(row.find('input[name^="harga"]').val());
                const quantity = parseFloat(row.find('input[name^="qty"]').val()) || 0;
                const jumlahHarga = harga * quantity;
                row.find('td:nth-child(10) input').val(formatRibuan(jumlahHarga));
                updateTotalFooter();
            }

            function updateTotalFooter() {
                let totalHarga = 0;
                let totalJumlahHarga = 0;

                $('#table-data tbody tr').each(function() {
                    const harga = unformatRibuan($(this).find('td:nth-child(10)').text());
                    const quantity = parseInt($(this).find('td:nth-child(7)').text()) || 0;
                    const jumlahHarga = harga * quantity;

                    totalHarga += harga;
                    totalJumlahHarga += jumlahHarga;
                });

                const ppn11 = totalJumlahHarga * 0.11;
                const grandTotal = totalJumlahHarga + ppn11;

                $('#totalHarga').text(formatRibuan(totalHarga));
                $('#totalJumlahHarga').text(formatRibuan(totalJumlahHarga));
                $('#ppn11').text(formatRibuan(ppn11));
                $('#grandTotal').text(formatRibuan(grandTotal));
            }

            // Event handler for harga input
            $(document).on('blur', 'input[name^="harga"]', function() {
                const row = $(this).closest('tr');
                const harga = unformatRibuan($(this).val());
                $(this).val(formatRibuan(harga));
                updateJumlahHarga(row);
            });

            // Event handler for quantity input
            $(document).on('input', 'input[name^="qty"]', function() {
                const row = $(this).closest('tr');
                const max = parseInt($(this).attr('max'));
                let val = parseInt($(this).val()) || 0;

                if (val > max) {
                    alert('Quantity PO tidak boleh melebihi Quantity Sisa');
                    $(this).val(max);
                    val = max;
                }

                if (val < 0) {
                    $(this).val(0);
                    val = 0;
                }

                updateJumlahHarga(row);
            });

            // Add event handler for sparepart select change
            $(document).on('change', '.sparepart-select', function() {
                const row = $(this).closest('tr');
                const qtyInput = row.find('input[name^="qty"]');
                const hargaInput = row.find('input[name^="harga"]');

                if ($(this).val()) {
                    qtyInput.prop('disabled', false);
                    hargaInput.prop('disabled', false);
                } else {
                    qtyInput.prop('disabled', true).val(0);
                    hargaInput.prop('disabled', true).val('0');
                    updateJumlahHarga(row);
                }
            });

            // Initialize sparepart selects with Select2
            $('.sparepart-select').select2({
                placeholder: 'Pilih Sparepart',
                width: '100%',
                allowClear: true
            });

            updateTotalFooter();
        });
    </script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
