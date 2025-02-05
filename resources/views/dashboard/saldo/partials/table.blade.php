@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
            width: 100%;
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

@php
    function formatRibuan($number)
    {
        return number_format($number, 0, ',', '.');
    }

    function formatTanggal($date)
    {
        setlocale(LC_TIME, 'id_ID');
        return \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Tanggal Penerimaan</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Supplier</th>
                    <th class="text-center">Sparepart</th>
                    <th class="text-center">Merk</th>
                    <th class="text-center">Part Number</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ formatTanggal($item->atb->tanggal) }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}</td>
                        <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->satuan ?? '-' }}</td>
                        <td class="currency-value">{{ formatRibuan($item->harga) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="16">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No Saldo records found
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($TableData->currentPage() === $TableData->lastPage())
            <tfoot>
                <tr class="table-primary">
                    <td class="text-center fw-bold" colspan="9">Grand Total (Keseluruhan)</td>
                    <td class="text-center fw-bold currency-value" id="total-harga">{{ formatRibuan($TableData->total_amount) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Calculate total function
            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('#table-data tbody tr').forEach(function(row) {
                    let value = row.querySelector('td:last-child').textContent
                        .replace(/\./g, '');
                    total += parseInt(value) || 0;
                });

                // Format the total
                let formattedTotal = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                document.getElementById('total-harga').textContent = formattedTotal;
            }

            // Initial calculation
            calculateTotal();
        });
    </script>
@endpush
