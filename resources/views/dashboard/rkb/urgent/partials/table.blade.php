@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>No RKB</th>
                    <th>Proyek</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Detail</th>
                    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                        <th>Aksi</th>
                    @endif
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
                            @elseif($rkb->is_finalized && !$rkb->is_approved_svp)
                                <span class="badge bg-warning w-100">Evaluasi</span>
                            @elseif($rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && $rkb->is_approved_svp)
                                <span class="badge bg-success w-100">Disetujui</span>
                            @else
                                <span class="badge bg-secondary w-100">Tidak Diketahui</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a class="btn btn-primary mx-1 detailBtn" href="{{ route('rkb_urgent.detail.index', ['id' => $rkb->id]) }}">
                                <i class="fa-solid fa-file-pen"></i>
                            </a>
                        </td>
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-warning mx-1 ubahBtn" {{ $rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditRKB({{ $rkb->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $rkb->id }}" {{ $rkb->is_finalized ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="6">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No RKB found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
