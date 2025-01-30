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

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <form id="detailSpbForm" action="{{ route('spb.detail.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input name="id_rkb" type="hidden" value="{{ $rkb->id }}">

        @php
            $sparepartGroups = [];
            $totalItems = 0;
            foreach ($rkb->linkAlatDetailRkbs as $detail1) {
                foreach ($detail1->linkRkbDetails as $detail2) {
                    $remainder = $detail2->detailRkbUrgent?->quantity_remainder ?? ($detail2->detailRkbGeneral?->quantity_remainder ?? 0);

                    // Changed this condition to only include items where remainder is 0
                    if ($remainder > 0) {
                        continue;
                    }
                    $totalItems++;

                    $sparepartName = $detail2->detailRkbUrgent->masterDataSparepart->nama ?? $detail2->detailRkbGeneral->masterDataSparepart->nama;
                    $satuan = $detail2->detailRkbUrgent->satuan ?? $detail2->detailRkbGeneral->satuan;
                    $kategori = $detail2->detailRkbUrgent->kategoriSparepart->nama ?? $detail2->detailRkbGeneral->kategoriSparepart->nama;

                    $groupKey = $sparepartName;

                    if (!isset($sparepartGroups[$groupKey])) {
                        $sparepartGroups[$groupKey] = [];
                    }

                    $sparepartGroups[$groupKey][] = [
                        'alat' => $detail1->masterDataAlat,
                        'detail' => $detail2,
                        'satuan' => $satuan,
                        'kategori' => $kategori,
                        'alat_detail_id' => $detail1->masterDataAlat->id,
                    ];
                }
            }
        @endphp

        <!-- Removed supplier selection section -->

        <div class="table-responsive pe-3">
            <table class="table table-bordered table-striped" id="table-data">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">Nama Alat</th>
                        <th class="text-center">Kode Alat</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Sparepart PO</th>
                        <th class="text-center">Merk</th>
                        <th class="text-center">Supplier</th>
                        <th class="text-center">Quantity PO</th>
                        <th class="text-center">Quantity Diterima</th> <!-- Tambah kolom ini -->
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Jumlah Harga</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($riwayatSpbs as $riwayatSpb)
                        @foreach ($riwayatSpb->linkSpbDetailSpb as $linkSpbDetailSpb)
                            <tr>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSPb->masterDataAlat->jenis_alat }}</td>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSPb->masterDataAlat->kode_alat }}</td>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSPb->masterDataSparepart->kategoriSparepart->kode }}: {{ $linkSpbDetailSpb->detailSPb->masterDataSparepart->kategoriSparepart->nama }}</td>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSPb->masterDataSparepart->nama }}</td>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSPb->masterDataSparepart->merk }}</td>
                                <td class="text-center">{{ $riwayatSpb->masterDataSupplier->nama }}</td>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSpb->quantity_po }}</td>
                                <td class="text-center">
                                    @php
                                        $totalQuantityDiterima = $linkSpbDetailSpb->detailSpb->atbs->sum('quantity');
                                    @endphp
                                    {{ $totalQuantityDiterima }}
                                </td>
                                <td class="text-center">{{ $linkSpbDetailSpb->detailSPb->satuan }}</td>
                                <td class="currency-value">{{ number_format($linkSpbDetailSpb->detailSPb->harga, 0, ',', '.') }}</td>
                                <td class="currency-value">{{ number_format($linkSpbDetailSpb->detailSPb->harga * $linkSpbDetailSpb->detailSpb->quantity_po, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
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
    </form>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Remove supplier select2 initialization
            $('.sparepart-select').select2({
                placeholder: 'Pilih Supplier',
                width: '100%',
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                paginate: false,
                ordering: false,
                order: [],
                searching: false,
            });

            function formatRupiah(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            function updateJumlahHarga(row) {
                const harga = unformatRupiah(row.find('input[name^="harga"]').val());
                const quantity = parseFloat(row.find('input[name^="qty"]').val()) || 0;
                const jumlahHarga = harga * quantity;
                row.find('td:nth-child(10) input').val(formatRupiah(jumlahHarga));
                updateTotalFooter();
            }

            function updateTotalFooter() {
                let totalHarga = 0;
                let totalJumlahHarga = 0;

                $('#table-data tbody tr').each(function() {
                    const harga = unformatRupiah($(this).find('td:nth-child(10)').text());
                    const quantity = parseInt($(this).find('td:nth-child(7)').text()) || 0;
                    const jumlahHarga = harga * quantity;

                    totalHarga += harga;
                    totalJumlahHarga += jumlahHarga;
                });

                const ppn11 = totalJumlahHarga * 0.11;
                const grandTotal = totalJumlahHarga + ppn11;

                $('#totalHarga').text(formatRupiah(totalHarga));
                $('#totalJumlahHarga').text(formatRupiah(totalJumlahHarga));
                $('#ppn11').text(formatRupiah(ppn11));
                $('#grandTotal').text(formatRupiah(grandTotal));
            }

            // Event handler for harga input
            $(document).on('blur', 'input[name^="harga"]', function() {
                const row = $(this).closest('tr');
                const harga = unformatRupiah($(this).val());
                $(this).val(formatRupiah(harga));
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

    <!-- Remove supplier change event script -->
@endpush
