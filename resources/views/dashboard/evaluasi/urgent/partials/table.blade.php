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
        <table class="m-0 table table-bordered table-striped" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>No RKB</th>
                    <th>Proyek</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Evaluasi</th>
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
                            <a class="btn {{ !$rkb->is_finalized ? 'btn-secondary' : 'btn-primary' }} mx-1 detailBtn {{ !$rkb->is_finalized ? 'disabled' : '' }}" href="{{ route('evaluasi_rkb_urgent.detail.index', ['id' => $rkb->id]) }}">
                                <i class="fa-solid {{ $rkb->is_finalized && !$rkb->is_evaluated ? 'fa-stamp' : 'fa-eye' }}"></i>
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
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            const $table = $('#table-data');
            const $headers = $table.find('thead th');
            const textsToCheck = ['Detail', 'Aksi', 'Supplier'];
            let indices = {};

            // Find the indices of the headers that match the texts in textsToCheck array
            $headers.each(function(index) {
                const headerText = $(this).text().trim();
                if (textsToCheck.includes(headerText)) {
                    indices[headerText] = index;
                }
            });

            // Set the width of the corresponding columns in tbody
            $.each(indices, function(text, index) {
                $table.find('tbody tr').each(function() {
                    $(this).find('td').eq(index).css('width', '1%');
                });
            });
        });
    </script>
@endpush
