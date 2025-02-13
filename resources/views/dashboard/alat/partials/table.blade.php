@push('styles_3')
    @include('styles.tables')
@endpush

@php
    // Define table headers in a clean and structured way
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

    // Determine if any filter is applied dynamically from the headers
    $appliedFilters = collect($headers)
        ->filter(fn($header) => $header['filter']) // Only check headers with filters
        ->some(fn($header) => request("selected_{$header['paramName']}"));

    // Build query parameters for resetting filters
    $resetUrl = request()->url();
    $queryParams = request()->hasAny(['search', 'id_proyek']) ? '?' . http_build_query(request()->only(['search', 'id_proyek'])) : '';
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

        <input id="selected-jenis-alat" name="selected_jenis_alat" type="hidden" value="{{ request('selected_jenis_alat') }}">
        <input id="selected-kode-alat" name="selected_kode_alat" type="hidden" value="{{ request('selected_kode_alat') }}">
        <input id="selected-merek-alat" name="selected_merek_alat" type="hidden" value="{{ request('selected_merek_alat') }}">
        <input id="selected-tipe-alat" name="selected_tipe_alat" type="hidden" value="{{ request('selected_tipe_alat') }}">
        <input id="selected-serial-number" name="selected_serial_number" type="hidden" value="{{ request('selected_serial_number') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
