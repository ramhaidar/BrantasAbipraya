@push('styles_3')
    @include('styles.tables')
@endpush

@php
    // Step 1: Define table headers in a clean and structured way
    $headers = [
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
            'title' => 'Serial Number',
            'filterId' => 'serial-number',
            'paramName' => 'serial_number',
            'filter' => true,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
        ],
    ];

    // Step 2: Check if any filter is applied
    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    // Step 3: Build query parameters for resetting filters
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
                        <td class="text-center">{{ $item->masterDataAlat->jenis_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->kode_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->merek_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->tipe_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->serial_number }}</td>
                        <td class="text-center">
                            <button class="btn btn-danger deleteBtn" data-id="{{ $item->id }}" type="button">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="16">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data alat yang tersedia untuk proyek ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
