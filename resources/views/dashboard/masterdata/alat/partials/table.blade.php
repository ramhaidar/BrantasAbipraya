@push('styles_3')
    @include('styles.tables')
@endpush

@php
    // Step 1: Define table headers in a clean and structured way
    $headers = [
        [
            'title' => 'Jenis Alat',
            'filterId' => 'jenis',
            'paramName' => 'jenis',
            'filter' => true,
        ],
        [
            'title' => 'Kode Alat',
            'filterId' => 'kode',
            'paramName' => 'kode',
            'filter' => true,
        ],
        [
            'title' => 'Merek Alat',
            'filterId' => 'merek',
            'paramName' => 'merek',
            'filter' => true,
        ],
        [
            'title' => 'Tipe Alat',
            'filterId' => 'tipe',
            'paramName' => 'tipe',
            'filter' => true,
        ],
        [
            'title' => 'Serial Number',
            'filterId' => 'serial',
            'paramName' => 'serial',
            'filter' => true,
        ],
        [
            'title' => 'Lokasi Proyek',
            'filterId' => 'proyek',
            'paramName' => 'proyek',
            'filter' => true,
        ],
        [
            'title' => 'Riwayat',
            'filter' => false,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
            'roles' => ['admin_divisi', 'superadmin'],
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
                        <td>{{ $item->jenis_alat }}</td>
                        <td>{{ $item->kode_alat }}</td>
                        <td>{{ $item->merek_alat }}</td>
                        <td>{{ $item->tipe_alat }}</td>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ isset($item->current_project) ? $item->current_project->nama : 'Belum Ditugaskan' }}</td>
                        <td>
                            <button class="btn btn-info" type="button" title="Lihat Riwayat" onclick="showHistory({{ $item->id }})">
                                <i class="bi bi-clock-history"></i>
                            </button>
                        </td>
                        @if (auth()->user()->role == 'admin_divisi' || auth()->user()->role == 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" type="button" onclick="fillFormEdit({{ $item->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" type="button">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="{{ auth()->user()->role == 'admin_divisi' || auth()->user()->role == 'superadmin' ? '8' : '7' }}">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak Ada Data Master Data Alat
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
