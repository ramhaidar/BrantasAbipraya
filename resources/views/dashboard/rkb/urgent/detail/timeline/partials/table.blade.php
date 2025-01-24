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

        .img-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

<div class="ibox-body table-responsive p-0 m-0">
    <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
        <thead class="table-primary">
            <tr>
                <th>Uraian Pekerjaan</th>
                <th>Waktu Penyelesaian (Rencana)</th>
                <th>Tanggal Awal Rencana</th>
                <th>Tanggal Akhir Rencana</th>
                <th>Waktu Penyelesaian (Actual)</th>
                <th>Tanggal Awal Actual</th>
                <th>Tanggal Akhir Actual</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($TableData as $item)
                <tr>
                    <td class="text-center">{{ $item->nama_rencana }}</td>
                    <td class="text-center">{{ $item->diff_in_days_rencana ? $item->diff_in_days_rencana . ' Hari' : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_awal_rencana ? $item->tanggal_awal_rencana->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_akhir_rencana ? $item->tanggal_akhir_rencana->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $item->diff_in_days_actual ? $item->diff_in_days_actual . ' Hari' : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_awal_actual ? $item->tanggal_awal_actual->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_akhir_actual ? $item->tanggal_akhir_actual->format('Y-m-d') : '-' }}</td>
                    <td class="text-center"><span class="badge {{ $item->is_done ? 'bg-success' : 'bg-warning' }} w-100">{{ $item->is_done ? 'Sudah Selesai' : 'Belum Selesai' }}</span></td>
                    <td class="text-center">
                        <button class="btn btn-warning mx-1 editBtn" data-id="{{ $item->id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center py-3" colspan="9">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <span class="text-muted">No data found</span>
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
