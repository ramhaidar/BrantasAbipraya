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

<div class="ibox-body table-responsive p-0 m-0">
    <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
        <thead class="table-primary">
            <tr>
                <th>Jenis Alat</th>
                <th>Kode Alat</th>
                <th>Merek Alat</th>
                <th>Tipe Alat</th>
                <th>Serial Number</th>
                <th>Lokasi Proyek Sekarang</th>
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
                    <td>{{ $item->current_project ? $item->current_project->nama : 'Not Assigned' }}</td>
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
                        No tools found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
