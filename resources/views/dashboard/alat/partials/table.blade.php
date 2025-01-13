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
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive">
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
            @foreach ($AlatAssigned as $alat)
                <tr>
                    <td class="text-center">{{ $alat->masterDataAlat->jenis_alat }}</td>
                    <td class="text-center">{{ $alat->masterDataAlat->kode_alat }}</td>
                    <td class="text-center">{{ $alat->masterDataAlat->merek_alat }}</td>
                    <td class="text-center">{{ $alat->masterDataAlat->tipe_alat }}</td>
                    <td class="text-center">{{ $alat->masterDataAlat->serial_number }}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $alat->id }}" type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });
        });
    </script>
@endpush
