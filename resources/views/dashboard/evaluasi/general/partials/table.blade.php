@push('styles_3')
    @include('styles.tables')
@endpush

@php
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
            'title' => 'Status',
            'filterId' => 'status',
            'paramName' => 'status',
            'filter' => true,
            'customUniqueValues' => ['Pengajuan', 'Evaluasi', 'Menunggu Approval VP', 'Menunggu Approval SVP', 'Disetujui', 'Tidak Diketahui'],
        ],
        [
            'title' => 'Evaluasi',
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
    if (request()->hasAny(['search'])) {
        $queryParams = '?' . http_build_query(request()->only(['search']));
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
                    @forelse ($TableData as $rkb)
                        <tr>
                            <td class="text-center">{{ $rkb->nomor }}</td>
                            <td class="text-center">{{ $rkb->proyek->nama }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($rkb->periode)->isoFormat('MMMM Y') }}</td>
                            <td class="text-center">
                                @if (!$rkb->is_finalized && !$rkb->is_evaluated && !$rkb->is_approved_vp && !$rkb->is_approved_svp)
                                    <span class="badge bg-primary w-100">Pengajuan</span>
                                @elseif($rkb->is_finalized && !$rkb->is_evaluated && !$rkb->is_approved_vp && !$rkb->is_approved_svp)
                                    <span class="badge bg-warning w-100">Evaluasi</span>
                                @elseif($rkb->is_finalized && $rkb->is_evaluated && !$rkb->is_approved_vp)
                                    <span class="badge bg-info w-100">Menunggu Approval VP</span>
                                @elseif($rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && !$rkb->is_approved_svp)
                                    <span class="badge bg-secondary w-100">Menunggu Approval SVP</span>
                                @elseif($rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && $rkb->is_approved_svp)
                                    <span class="badge bg-success w-100">Disetujui</span>
                                @else
                                    <span class="badge bg-dark w-100">Tidak Diketahui</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn {{ !$rkb->is_finalized ? 'btn-secondary' : ($rkb->is_finalized && !$rkb->is_evaluated ? 'btn-warning' : 'btn-primary') }} mx-1 detailBtn {{ !$rkb->is_finalized ? 'disabled' : '' }}" href="{{ route('evaluasi_rkb_general.detail.index', ['id' => $rkb->id]) }}">
                                    <i class="fa-solid {{ !$rkb->is_finalized ? 'fa-eye-slash' : ($rkb->is_finalized && !$rkb->is_evaluated ? 'fa-stamp' : 'fa-eye') }}"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No RKB found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
