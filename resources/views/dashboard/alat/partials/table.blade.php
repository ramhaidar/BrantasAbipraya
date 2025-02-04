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
                    <th class="text-center">Jenis Alat</th>
                    <th class="text-center">Kode Alat</th>
                    <th class="text-center">Merek Alat</th>
                    <th class="text-center">Tipe Alat</th>
                    <th class="text-center">Serial Number</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ $item->masterDataAlat->jenis_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->kode_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->merek_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->tipe_alat }}</td>
                        <td class="text-center">{{ $item->masterDataAlat->serial_number }}</td>
                        <td class="text-center">
                            <button class="btn btn-danger deleteBtn" data-id="{{ $item->id }}" type="button">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="16">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data alat yang tersedia untuk proyek ini.
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
