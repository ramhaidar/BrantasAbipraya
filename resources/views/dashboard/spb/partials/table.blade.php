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
                    <th class="text-center">No RKB</th>
                    <th class="text-center">Proyek</th>
                    <th class="text-center">Periode</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td>{{ $item->nomor }}</td>
                        <td>{{ $item->proyek->nama ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
                        <td>
                            @if ($item->tipe == 'general')
                                <span class="badge bg-primary w-100">General</span>
                            @else
                                <span class="badge bg-danger w-100">Urgent</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-primary mx-1 detailBtn" data-id="{{ $item->id }}" href="{{ route('spb.detail.index', $item->id) }}">
                                <i class="fa-solid fa-eye"></i>
                            </a>
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
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
