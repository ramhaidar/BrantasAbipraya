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
                    <th>Nama Proyek</th>
                    <th>Detail</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td>{{ $item->nama }}</td>
                        <td class="text-center">
                            <button class="btn btn-primary detailBtn" data-id="{{ $item->id }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" onclick="fillFormEdit({{ $item->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="7">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No projects found
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
