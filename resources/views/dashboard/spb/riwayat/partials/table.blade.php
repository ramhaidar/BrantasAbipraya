@push('styles_3')
    <style>
        .table th {
            text-align: center;
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
@endpush

@php
    // Calculate totals
    $totalHarga = 0;
    $totalJumlahHarga = 0;

    foreach ($spb->linkSpbDetailSpb as $item) {
        $totalHarga += $item->detailSpb->harga;
        $totalJumlahHarga += $item->detailSpb->quantity * $item->detailSpb->harga;
    }

    $ppn = $totalJumlahHarga * 0.11;
    $grandTotal = $totalJumlahHarga + $ppn;
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="container-fluid py-0 my-0">
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
        <table class="table table-striped table-bordered table-hover" id="table-data">
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
                @forelse ($spb->linkSpbDetailSpb as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->detailSpb->masterDataSparepart->nama }}</td>
                        <td class="text-center">{{ $item->detailSpb->masterDataSparepart->merk }}</td>
                        <td class="text-center">{{ $item->detailSpb->masterDataSparepart->part_number }}</td>
                        <td class="text-center">{{ $item->detailSpb->quantity }}</td>
                        <td class="text-center">{{ $item->detailSpb->satuan }}</td>
                        <td class="text-center">Rp {{ number_format($item->detailSpb->harga, 0, ',', '.') }}</td>
                        <td class="text-center">Rp
                            {{ number_format($item->detailSpb->quantity * $item->detailSpb->harga, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-4" colspan="7">
                            <i class="bi bi-inbox fs-1 text-secondary d-block"></i>
                            <p class="text-secondary mt-2 mb-0">Belum ada detail SPB</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot class="table-primary">
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="6">Jumlah</th>
                    <th style="text-align: center;">Rp {{ number_format($totalHarga, 0, ',', '.') }}</th>
                    <th style="text-align: center;">Rp {{ number_format($totalJumlahHarga, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="7">PPN 11%</th>
                    <th style="text-align: center;">Rp {{ number_format($ppn, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="7">Grand Total</th>
                    <th style="text-align: center;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
                order: [],
                searching: false,
            });
        });
    </script>
@endpush
