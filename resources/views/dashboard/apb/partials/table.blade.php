@push('styles_3')
    @include('styles.tables')
@endpush

@php
    if (!function_exists('formatRibuan')) {
        function formatRibuan($number)
        {
            return number_format($number, 2, ',', '.');
        }
    }

    if (!function_exists('formatTanggal')) {
        function formatTanggal($date)
        {
            setlocale(LC_TIME, 'id_ID');
            return \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
        }
    }

    $headers = [
        [
            'title' => 'Tanggal',
            'filterId' => 'tanggal',
            'paramName' => 'tanggal',
            'filter' => true,
            'type' => 'date',
        ],
        [
            'title' => 'Jenis Alat',
            'filterId' => 'jenis-alat',
            'paramName' => 'jenis_alat',
            'filter' => true,
        ],
        [
            'title' => 'Kode Alat',
            'filterId' => 'kode-alat',
            'paramName' => 'kode_alat',
            'filter' => true,
        ],
        [
            'title' => 'Merek Alat',
            'filterId' => 'merek-alat',
            'paramName' => 'merek_alat',
            'filter' => true,
        ],
        [
            'title' => 'Tipe Alat',
            'filterId' => 'tipe-alat',
            'paramName' => 'tipe_alat',
            'filter' => true,
        ],
        [
            'title' => 'Serial Number Alat',
            'filterId' => 'serial-number',
            'paramName' => 'serial_number',
            'filter' => true,
        ],
        [
            'title' => 'Kode',
            'filterId' => 'kode',
            'paramName' => 'kode',
            'filter' => true,
        ],
        [
            'title' => 'Supplier',
            'filterId' => 'supplier',
            'paramName' => 'supplier',
            'filter' => true,
        ],
        [
            'title' => 'Sparepart',
            'filterId' => 'sparepart',
            'paramName' => 'sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Merk',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'Part Number',
            'filterId' => 'part-number',
            'paramName' => 'part_number',
            'filter' => true,
        ],
        [
            'title' => 'Quantity',
            'filterId' => 'quantity',
            'paramName' => 'quantity',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
        [
            'title' => 'Harga',
            'filterId' => 'harga',
            'paramName' => 'harga',
            'filter' => true,
            'type' => 'price',
        ],
        [
            'title' => 'Jumlah Harga',
            'filterId' => 'jumlah-harga',
            'paramName' => 'jumlah_harga',
            'filter' => true,
            'type' => 'price',
        ],
        [
            'title' => 'Mekanik',
            'filterId' => 'mekanik',
            'paramName' => 'mekanik',
            'filter' => true,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
            'role' => ['koordinator_proyek', 'superadmin'],
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
    if (request()->hasAny(['search', 'id_proyek'])) {
        $queryParams = '?' . http_build_query(request()->only(['search', 'id_proyek']));
    }
@endphp

<div class="ibox-body ms-0 ps-0">
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
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ formatTanggal($item->tanggal) }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->jenis_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->kode_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->merek_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->tipe_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->serial_number }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}</td>
                        <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td> {{-- Pastikan ini mengambil dari APB --}}
                        <td class="text-center">{{ $item->saldo->satuan ?? '-' }}</td>
                        <td class="currency-value">{{ formatRibuan($item->saldo->harga ?? 0) }}</td>
                        <td class="currency-value">{{ formatRibuan(($item->saldo->harga ?? 0) * $item->quantity) }}</td>
                        <!-- Removed root_cause cell -->
                        <td class="text-center">{{ $item->mekanik ?? '-' }}</td>
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="17">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No ATB records found
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($TableData->currentPage() === $TableData->lastPage())
                <tfoot>
                    <tr class="table-primary">
                        <td class="text-center fw-bold" colspan="14">Grand Total</td>
                        <td class="text-center fw-bold currency-value" id="total-harga">{{ formatRibuan($TableData->total_amount) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
