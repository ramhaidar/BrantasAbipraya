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

        #table-data th:nth-child(7),
        #table-data th:nth-child(8),
        #table-data td:nth-child(7),
        #table-data td:nth-child(8) {
            min-width: 10dvw;
            width: 10dvw;
        }

        .currency-value {
            text-align: right !important;
            padding-right: 10px !important;
        }
    </style>
@endpush

@php
    // Calculate totals
    $totalHarga = 0;
    $totalJumlahHarga = 0;

    if (isset($TableData)) {
        foreach ($TableData as $spb) {
            foreach ($spb->linkSpbDetailSpb as $item) {
                $totalHarga += $item->detailSpb->harga;
                $totalJumlahHarga += $item->detailSpb->quantity_po * $item->detailSpb->harga;
            }
        }
    }

    $ppn = $totalJumlahHarga * 0.11;
    $grandTotal = $totalJumlahHarga + $ppn;
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="container-fluid p-0 m-0 pb-3">
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Lampiran:</span>
            </div>
            <div class="col">
                <span>Surat Pemesanan Barang</span>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Nomor:</span>
            </div>
            <div class="col">
                <span>{{ $spb->nomor }}</span>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Tanggal:</span>
            </div>
            <div class="col">
                <span>{{ \Carbon\Carbon::parse($spb->tanggal)->isoFormat('DD MMMM YYYY') }}</span>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Supplier:</span>
            </div>
            <div class="col">
                <span>{{ $spb->masterDataSupplier->nama }}</span>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="m-0 table table-bordered table-striped" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">NO</th>
                    <th class="text-center">JENIS BARANG</th>
                    <th class="text-center">Merk</th>
                    <th class="text-center">SPESIFIKASI/TIPE/NO SERI</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Sat</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Harga</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($TableData as $spb)
                    @foreach ($spb->linkSpbDetailSpb as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $item->detailSpb->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $item->detailSpb->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item->detailSpb->masterDataSparepart->part_number }}</td>
                            <td class="text-center">{{ $item->detailSpb->quantity_po }}</td>
                            <td class="text-center">{{ $item->detailSpb->satuan }}</td>
                            <td class="currency-value">{{ number_format($item->detailSpb->harga, 0, ',', '.') }}</td>
                            <td class="currency-value">{{ number_format($item->detailSpb->quantity_po * $item->detailSpb->harga, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="8">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SPB
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot class="table-primary">
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="6">Jumlah</th>
                    <th class="currency-value">{{ number_format($totalHarga, 0, ',', '.') }}</th>
                    <th class="currency-value">{{ number_format($totalJumlahHarga, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="7">PPN 11%</th>
                    <th class="currency-value">{{ number_format($ppn, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="7">Grand Total</th>
                    <th class="currency-value">{{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            const $table = $('#table-data');
            const $headers = $table.find('thead th');
            const textsToCheck = ['Detail', 'Aksi', 'Supplier'];
            let indices = {};

            // Find the indices of the headers that match the texts in textsToCheck array
            $headers.each(function(index) {
                const headerText = $(this).text().trim();
                if (textsToCheck.includes(headerText)) {
                    indices[headerText] = index;
                }
            });

            // Set the width of the corresponding columns in tbody
            $.each(indices, function(text, index) {
                $table.find('tbody tr').each(function() {
                    $(this).find('td').eq(index).css('width', '1%');
                });
            });
        });
    </script>
@endpush
