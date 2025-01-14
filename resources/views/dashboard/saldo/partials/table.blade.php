@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            vertical-align: middle;
        }
    </style>
@endpush

@php
    function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    function formatTanggal($date)
    {
        setlocale(LC_TIME, 'id_ID');
        return \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
    }
@endphp

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($saldos as $saldo)
                <tr>
                    <td class="text-center">{{ formatTanggal($saldo->atb->tanggal) }}</td>
                    <td class="text-center">{{ $saldo->masterDataSparepart->kategoriSparepart->kode }}: {{ $saldo->masterDataSparepart->kategoriSparepart->nama }}</td>
                    <td class="text-center">{{ $saldo->masterDataSupplier->nama ?? '-' }}</td>
                    <td class="text-center">{{ $saldo->masterDataSparepart->nama ?? '-' }}</td>
                    <td class="text-center">{{ $saldo->masterDataSparepart->merk ?? '-' }}</td>
                    <td class="text-center">{{ $saldo->masterDataSparepart->part_number ?? '-' }}</td>
                    <td class="text-center">{{ $saldo->quantity }}</td>
                    <td class="text-center">{{ $saldo->satuan ?? '-' }}</td>
                    <td class="text-center">{{ formatRupiah($saldo->harga) }}</td>
                    <td class="text-center">{{ formatRupiah($saldo->quantity * $saldo->harga) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });
        });
    </script>
@endpush
