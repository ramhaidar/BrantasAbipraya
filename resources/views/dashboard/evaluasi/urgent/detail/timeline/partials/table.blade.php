@push('styles_3')
    @include('styles.tables')
@endpush

@php
    $headers = [
        [
            'title' => 'Uraian Pekerjaan',
            'filterId' => 'uraian',
            'paramName' => 'uraian',
            'filter' => true,
        ],
        [
            'title' => 'Waktu Penyelesaian (Rencana)',
            'filterId' => 'durasi-rencana',
            'paramName' => 'durasi_rencana',
            'filter' => true,
            'type' => 'number_of_days',
        ],
        [
            'title' => 'Tanggal Awal Rencana',
            'filterId' => 'tanggal-awal-rencana',
            'paramName' => 'tanggal_awal_rencana',
            'filter' => true,
            'type' => 'date',
        ],
        [
            'title' => 'Tanggal Akhir Rencana',
            'filterId' => 'tanggal-akhir-rencana',
            'paramName' => 'tanggal_akhir_rencana',
            'filter' => true,
            'type' => 'date',
        ],
        [
            'title' => 'Waktu Penyelesaian (Actual)',
            'filterId' => 'durasi-actual',
            'paramName' => 'durasi_actual',
            'filter' => true,
            'type' => 'number_of_days',
        ],
        [
            'title' => 'Tanggal Awal Actual',
            'filterId' => 'tanggal-awal-actual',
            'paramName' => 'tanggal_awal_actual',
            'filter' => true,
            'type' => 'date',
        ],
        [
            'title' => 'Tanggal Akhir Actual',
            'filterId' => 'tanggal-akhir-actual',
            'paramName' => 'tanggal_akhir_actual',
            'filter' => true,
            'type' => 'date',
        ],
        [
            'title' => 'Status',
            'filterId' => 'status',
            'paramName' => 'status',
            'filter' => true,
            'customUniqueValues' => ['Sudah Selesai', 'Belum Selesai'],
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
                        <td class="text-center">{{ $item->nama_rencana }}</td>
                        <td class="text-center">{{ $item->diff_in_days_rencana ? $item->diff_in_days_rencana . ' Hari' : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_awal_rencana ? $item->tanggal_awal_rencana->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_akhir_rencana ? $item->tanggal_akhir_rencana->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $item->diff_in_days_actual ? $item->diff_in_days_actual . ' Hari' : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_awal_actual ? $item->tanggal_awal_actual->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_akhir_actual ? $item->tanggal_akhir_actual->format('Y-m-d') : '-' }}</td>
                        <td class="text-center"><span class="badge {{ $item->is_done ? 'bg-success' : 'bg-warning' }} w-100">{{ $item->is_done ? 'Sudah Selesai' : 'Belum Selesai' }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3" colspan="9">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <span class="text-muted">No data found</span>
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
