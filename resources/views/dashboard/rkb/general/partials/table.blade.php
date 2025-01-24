@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            /* padding: 4px 8px; */
            vertical-align: middle;
        }

        #table-data th {
            text-align: center;
        }
    </style>
@endpush

<div class="ibox-body table-responsive p-0 m-0">
    <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
        <thead class="table-primary">
            <tr>
                <th>No RKB</th>
                <th>Proyek</th>
                <th>Periode</th>
                <th>Status</th>
                <th></th>
                <th></th>
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
                        @elseif($rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && $rkb->is_approved_svp)
                            <span class="badge bg-success w-100">Disetujui</span>
                        @else
                            <span class="badge bg-secondary w-100">Tidak Diketahui</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a class="btn btn-info mx-1 detailBtn" href="{{ route('rkb_general.detail.index', ['id' => $rkb->id]) }}">
                            <i class="fa-solid fa-file-pen"></i>
                        </a>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning mx-1 ubahBtn" {{ $rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditRKB({{ $rkb->id }})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $rkb->id }}" {{ $rkb->is_finalized ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
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

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#table-data');
            const headers = table.querySelectorAll('thead th');
            let aksiIndex1, aksiIndex2;

            headers.forEach((header, index) => {
                if (header.textContent.trim() === '') {
                    if (aksiIndex1 === undefined) {
                        aksiIndex1 = index;
                    } else {
                        aksiIndex2 = index;
                    }
                }
            });

            if (aksiIndex1 !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[aksiIndex1].style.width = '1%';
                });
            }
            if (aksiIndex2 !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[aksiIndex2].style.width = '1%';
                });
            }
        });
    </script>
@endpush
