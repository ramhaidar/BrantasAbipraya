@push('styles_3')
    @include('styles.tables')

    <style>
        #table-data th:nth-child(7),
        #table-data th:nth-child(8),
        #table-data td:nth-child(7),
        #table-data td:nth-child(8) {
            min-width: 10dvw;
            width: 10dvw;
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

    $headers = [
        [
            'title' => 'NO',
            'filter' => false,
        ],
        [
            'title' => 'JENIS BARANG',
            'filterId' => 'jenis-barang',
            'paramName' => 'jenis_barang',
            'filter' => true,
        ],
        [
            'title' => 'MERK',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'SPESIFIKASI/TIPE/NO SERI',
            'filterId' => 'spesifikasi',
            'paramName' => 'spesifikasi',
            'filter' => true,
        ],
        [
            'title' => 'JUMLAH',
            'filterId' => 'quantity',
            'paramName' => 'quantity',
            'filter' => true,
        ],
        [
            'title' => 'SAT',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
        [
            'title' => 'HARGA',
            'filterId' => 'harga',
            'paramName' => 'harga',
            'filter' => true,
        ],
        [
            'title' => 'JUMLAH HARGA',
            'filterId' => 'jumlah-harga',
            'paramName' => 'jumlah_harga',
            'filter' => true,
        ],
    ];

    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    $resetUrl = request()->url();
    $queryParams = '';
    if (request()->hasAny(['search'])) {
        $queryParams = '?' . http_build_query(request()->only(['search']));
    }
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

    <div class="mb-3 d-flex justify-content-end">
        @if ($appliedFilters)
            <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ $resetUrl . $queryParams }}">
                <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    @foreach ($headers as $header)
                        @include(
                            'components.table-header-filter',
                            array_merge($header, [
                                'uniqueValues' => $uniqueValues ?? [],
                            ]))
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @forelse ($TableData as $spb)
                    @forelse ($spb->linkSpbDetailSpb as $index => $item)
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
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="8">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data SPB
                            </td>
                        </tr>
                    @endforelse
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
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
