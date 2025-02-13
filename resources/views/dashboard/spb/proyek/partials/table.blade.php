@php
    use Carbon\Carbon;

    $headers = [
        [
            'title' => 'No RKB',
            'filterId' => 'nomor',
            'paramName' => 'nomor',
            'filter' => true,
        ],
        [
            'title' => 'Proyek',
            'filterId' => 'proyek',
            'paramName' => 'proyek',
            'filter' => true,
        ],
        [
            'title' => 'Periode',
            'filterId' => 'periode',
            'paramName' => 'periode',
            'filter' => true,
        ],
        [
            'title' => 'Tipe',
            'filterId' => 'tipe',
            'paramName' => 'tipe',
            'filter' => true,
        ],
        [
            'title' => 'Detail',
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
    if (request()->has('search')) {
        $queryParams = '?search=' . request('search');
    }
@endphp

@push('styles_3')
    @include('styles.tables')
@endpush

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
                            <td>{{ $item->nomor }}</td>
                            <td>{{ $item->proyek->nama ?? '-' }}</td>
                            <td>{{ Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
                            <td>
                                @if ($item->tipe == 'general')
                                    <span class="badge bg-primary w-100">General</span>
                                @else
                                    <span class="badge bg-danger w-100">Urgent</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn btn-primary mx-1 detailBtn" data-id="{{ $item->id }}" href="{{ route('spb.proyek.detail.index', $item->id) }}">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data RKB
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-nomor" name="selected_nomor" type="hidden" value="{{ request('selected_nomor') }}">
        <input id="selected-proyek" name="selected_proyek" type="hidden" value="{{ request('selected_proyek') }}">
        <input id="selected-periode" name="selected_periode" type="hidden" value="{{ request('selected_periode') }}">
        <input id="selected-tipe" name="selected_tipe" type="hidden" value="{{ request('selected_tipe') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
