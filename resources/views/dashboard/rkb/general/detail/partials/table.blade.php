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
        ],
        [
            'title' => 'Quantity Approved',
            'filterId' => 'quantity-approved',
            'paramName' => 'quantity_approved',
            'filter' => true,
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
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
                        @forelse ($item->linkRkbDetails as $detail)
                            <tr>
                                <td class="text-center">{{ $detail->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-' }}</td>
                                <td class="text-center">{{ $detail->linkAlatDetailRkb->masterDataAlat->kode_alat ?? '-' }}</td>
                                <td class="text-center">{{ $item->kategoriSparepart->kode ?? '-' }}: {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                                <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                                <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                                <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity_requested }}</td>
                                <td class="text-center">{{ $item->quantity_approved ?? '-' }}</td>
                                <td class="text-center">{{ $item->satuan }}</td>
                                @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                                    <td class="text-center">
                                        <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" type="button" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditDetailRKB({{ $item->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" type="button" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-3 text-muted" colspan="10">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No RKB details found
                                </td>
                            </tr>
                        @endforelse
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

        <input id="selected-jenis-alat" name="selected_jenis_alat" type="hidden" value="{{ request('selected_jenis_alat') }}">
        <input id="selected-kode-alat" name="selected_kode_alat" type="hidden" value="{{ request('selected_kode_alat') }}">
        <input id="selected-kategori-sparepart" name="selected_kategori_sparepart" type="hidden" value="{{ request('selected_kategori_sparepart') }}">
        <input id="selected-sparepart" name="selected_sparepart" type="hidden" value="{{ request('selected_sparepart') }}">
        <input id="selected-part-number" name="selected_part_number" type="hidden" value="{{ request('selected_part_number') }}">
        <input id="selected-merk" name="selected_merk" type="hidden" value="{{ request('selected_merk') }}">
        <input id="selected-satuan" name="selected_satuan" type="hidden" value="{{ request('selected_satuan') }}">
        <input id="selected-quantity-requested" name="selected_quantity_requested" type="hidden" value="{{ request('selected_quantity_requested') }}">
        <input id="selected-quantity-approved" name="selected_quantity_approved" type="hidden" value="{{ request('selected_quantity_approved') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
