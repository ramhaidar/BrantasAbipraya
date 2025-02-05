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
                    <th>Jenis Alat</th>
                    <th>Kode Alat</th>
                    <th>Merek Alat</th>
                    <th>Tipe Alat</th>
                    <th>Serial Number</th>
                    <th>Lokasi Proyek (Sekarang)</th>
                    <th>Riwayat</th>
                    <th>Aksi</th>
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
                        <td>{{ $item->current_project ? $item->current_project->nama : 'Belum Ditugaskan' }}</td>
                        <td>
                            <button class="btn btn-info historyBtn" data-id="{{ $item->id }}" title="Lihat Riwayat">
                                <i class="bi bi-clock-history"></i>
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
                        <td class="text-center py-3 text-muted" colspan="8">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No tools found
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
