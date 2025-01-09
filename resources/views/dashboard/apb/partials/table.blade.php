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
                <th class="text-center">Root Cause</th>
                <th class="text-center">Mekanik</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apbs as $apb)
                <tr>
                    <td class="text-center">{{ formatTanggal($apb->tanggal) }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->kategoriSparepart->kode }}: {{ $apb->masterDataSparepart->kategoriSparepart->nama }}</td>
                    <td class="text-center">{{ $apb->masterDataSupplier->nama ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->nama ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->merk ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->part_number ?? '-' }}</td>
                    <td class="text-center">{{ $apb->quantity }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->satuan ?? '-' }}</td>
                    <td class="text-center">{{ formatRupiah($apb->saldo->harga ?? 0) }}</td>
                    <td class="text-center">{{ formatRupiah(($apb->saldo->harga ?? 0) * $apb->quantity) }}</td>
                    <td class="text-center">{{ ucfirst($apb->root_cause) ?? '-' }}</td>
                    <td class="text-center">{{ $apb->mekanik ?? '-' }}</td>
                    <td class="text-center">
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $apb->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
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
