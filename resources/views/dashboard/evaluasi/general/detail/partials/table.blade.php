@push('styles_3')
    @include('styles.tables')
@endpush

@php
    $headers = [
        [
            'title' => 'Nama Alat',
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
            'title' => 'Kategori Sparepart',
            'filterId' => 'kategori-sparepart',
            'paramName' => 'kategori_sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Sparepart',
            'filterId' => 'sparepart',
            'paramName' => 'sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Part Number',
            'filterId' => 'part-number',
            'paramName' => 'part_number',
            'filter' => true,
        ],
        [
            'title' => 'Merk',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'Quantity Requested',
            'filterId' => 'quantity-requested',
            'paramName' => 'quantity_requested',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Quantity Approved',
            'filter' => false,
        ],
        [
            'title' => 'Quantity in Stock',
            'filterId' => 'stock-quantity',
            'paramName' => 'stock_quantity',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
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

<form id="approveRkbForm" method="POST" action="">
    @csrf
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
                    @php
                        // Group items by part number first for stock quantities
                        $groupedByPartNumber = $TableData->groupBy(function ($item) {
                            return $item->masterDataSparepart->part_number;
                        });

                        // Then group by part number AND equipment details
                        $groupedItems = $TableData->groupBy(function ($item) {
                            $detail = $item->linkRkbDetails->first();
                            return $item->masterDataSparepart->part_number . '|' . $detail->linkAlatDetailRkb->masterDataAlat->jenis_alat . '|' . $detail->linkAlatDetailRkb->masterDataAlat->kode_alat;
                        });
                    @endphp

                    @forelse ( $groupedByPartNumber as $partNumber => $partNumberGroup )
                        @php
                            $firstItemInGroup = $partNumberGroup->first();
                            $rowspanCount = $groupedItems
                                ->filter(function ($items, $key) use ($partNumber) {
                                    return explode('|', $key)[0] === $partNumber;
                                })
                                ->count();
                        @endphp

                        @foreach ($groupedItems->filter(fn($items, $key) => explode('|', $key)[0] === $partNumber) as $group)
                            @php
                                $firstItem = $group->first();
                                $detail = $firstItem->linkRkbDetails->first();
                                $sparepart = $firstItem->masterDataSparepart;
                                $alat = $detail->linkAlatDetailRkb->masterDataAlat;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $alat->jenis_alat ?? '-' }}</td>
                                <td class="text-center">{{ $alat->kode_alat ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $firstItem->kategoriSparepart->kode ?? '-' }}:
                                    {{ $firstItem->kategoriSparepart->nama ?? '-' }}
                                </td>
                                <td class="text-center">{{ $sparepart->nama ?? '-' }}</td>
                                <td class="text-center">{{ $sparepart->part_number ?? '-' }}</td>
                                <td class="text-center">{{ $sparepart->merk ?? '-' }}</td>
                                <td class="text-center">{{ $firstItem->quantity_requested }}</td>
                                <td class="text-center">
                                    @php
                                        $bgClass = match (true) {
                                            $rkb->is_approved_svp => 'bg-primary-subtle',
                                            $rkb->is_approved_vp => 'bg-info-subtle',
                                            $rkb->is_evaluated => 'bg-success-subtle',
                                            default => 'bg-warning-subtle',
                                        };
                                    @endphp
                                    <input class="form-control text-center {{ $bgClass }}" name="quantity_approved[{{ $firstItem->id }}]" type="number" value="{{ $firstItem->quantity_approved ?? $firstItem->quantity_requested }}" min="0" {{ $rkb->is_evaluated ? 'disabled' : '' }}>
                                </td>
                                @if ($loop->first)
                                    <td class="text-center" rowspan="{{ $rowspanCount }}">
                                        {{ $stockQuantities[$sparepart->id] ?? '-' }}
                                    </td>
                                @endif
                                <td class="text-center">{{ $firstItem->satuan }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="10">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <button class="btn btn-success btn-sm approveBtn" id="hiddenApproveRkbButton" type="submit" hidden></button>
</form>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
