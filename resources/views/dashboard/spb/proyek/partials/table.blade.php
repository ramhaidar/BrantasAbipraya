@php
    use Carbon\Carbon;
@endphp

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
                    <th class="text-center">No RKB</th>
                    <th class="text-center">Proyek</th>
                    <th class="text-center">Periode</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $rkb)
                    <tr>
                        <td class="text-center">{{ $rkb->nomor }}</td>
                        <td class="text-center">{{ $rkb->proyek->nama ?? '-' }}</td>
                        <td class="text-center">{{ Carbon::parse($rkb->periode)->translatedFormat('F Y') }}</td>
                        <td class="text-center">{{ ucfirst($rkb->tipe) }}</td>
                        <td class="text-center">
                            <a class="btn btn-primary mx-1 detailBtn" data-id="{{ $rkb->id }}" href="{{ route('spb.proyek.detail.index', $rkb->id) }}">
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
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
