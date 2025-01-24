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
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" onclick="fillFormEdit({{ $item->id }})">
                            <i class="bi bi-pencil-square"></i>
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

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#table-data');
            const headers = table.querySelectorAll('thead th');
            let detailIndex, aksiIndex;

            headers.forEach((header, index) => {
                if (header.textContent.trim() === 'Detail') {
                    detailIndex = index;
                }
                if (header.textContent.trim() === 'Aksi') {
                    aksiIndex = index;
                }
            });

            if (detailIndex !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[detailIndex].style.width = '1%';
                });
            }

            if (aksiIndex !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[aksiIndex].style.width = '1%';
                });
            }
        });
    </script>
@endpush
