@push('styles_3')
    @include('styles.tables')
@endpush

@php
    if (!function_exists('formatRibuan')) {
        function formatRibuan($number)
        {
            return number_format($number, 0, ',', '.');
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
            'title' => 'Tujuan Proyek',
            'filterId' => 'tujuan-proyek',
            'paramName' => 'tujuan_proyek',
            'filter' => true,
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
            'title' => 'Quantity Dikirim',
            'filterId' => 'quantity-dikirim',
            'paramName' => 'quantity_dikirim',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Quantity Diterima',
            'filterId' => 'quantity-diterima',
            'paramName' => 'quantity_diterima',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Quantity Digunakan',
            'filterId' => 'quantity-digunakan',
            'paramName' => 'quantity_digunakan',
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
            'type' => 'number',
        ],
        [
            'title' => 'Jumlah Harga',
            'filterId' => 'jumlah-harga',
            'paramName' => 'jumlah_harga',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Mekanik',
            'filterId' => 'mekanik',
            'paramName' => 'mekanik',
            'filter' => true,
        ],
        [
            'title' => 'Status',
            'filterId' => 'status',
            'paramName' => 'status',
            'filter' => true,
            'customUniqueValues' => ['Penggunaan', 'Pending', 'Accepted', 'Rejected'],
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
    <form class="mb-3" id="filter-form" method="GET">
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
                            <td class="text-center">{{ $item->tujuanProyek->nama ?? '-' }}</td>
                            <td class="text-center">{{ $item->alatProyek->masterDataAlat->jenis_alat ?? '-' }}</td>
                            <td class="text-center">{{ $item->alatProyek->masterDataAlat->kode_alat ?? '-' }}</td>
                            <td class="text-center">{{ $item->alatProyek->masterDataAlat->merek_alat ?? '-' }}</td>
                            <td class="text-center">{{ $item->alatProyek->masterDataAlat->tipe_alat ?? '-' }}</td>
                            <td class="text-center">{{ $item->alatProyek->masterDataAlat->serial_number ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->kategoriSparepart ? $item->masterDataSparepart->kategoriSparepart->kode . ': ' . $item->masterDataSparepart->kategoriSparepart->nama : '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                            <td class="text-center">{{ $item->status !== null ? $item->quantity : '-' }}</td>
                            <td class="text-center">{{ $item->status !== null ? $item->atbMutasi->quantity ?? '-' : '-' }}</td>
                            <td class="text-center">{{ $item->status === null ? $item->quantity : '-' }}</td>
                            <td class="text-center">{{ $item->saldo->satuan ?? '-' }}</td>
                            <td class="currency-value">{{ formatRibuan($item->saldo->harga ?? 0) }}</td>
                            <td class="currency-value">{{ formatRibuan(($item->saldo->harga ?? 0) * $item->quantity) }}</td>
                            <td class="text-center">{{ $item->mekanik ?? '-' }}</td>
                            <td class="text-center">
                                @if ($item->status === 'pending')
                                    <span class="badge bg-warning w-100">Pending</span>
                                @elseif($item->status === 'rejected')
                                    <span class="badge bg-danger w-100">Rejected</span>
                                @elseif($item->status === 'accepted')
                                    <span class="badge bg-success w-100">Accepted</span>
                                @elseif($item->status === null)
                                    <span class="badge bg-dark w-100">Penggunaan</span>
                                @else
                                    <span class="badge bg-secondary w-100">{{ ucfirst($item->status ?? '-') }}</span>
                                @endif
                            </td>
                            @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" {{ in_array($item->status, ['accepted', 'rejected']) ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="20">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No ATB records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($TableData->currentPage() === $TableData->lastPage())
                    <tfoot>
                        <tr class="table-primary">
                            <td class="text-center fw-bold" colspan="17">Grand Total (Accepted & Penggunaan Only)</td>
                            <td class="text-center fw-bold currency-value" id="total-harga">{{ formatRibuan($TableData->total_amount) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @foreach ($headers as $header)
            @if ($header['filter'])
                <input id="selected-{{ $header['paramName'] }}" name="selected_{{ $header['paramName'] }}" type="hidden" value="{{ request('selected_' . $header['paramName']) }}">
            @endif
        @endforeach
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
