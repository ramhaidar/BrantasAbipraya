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
            text-align: center;
        }
    </style>
@endpush

<div class="ibox-body table-responsive p-0 m-0">
    <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
        <thead class="table-primary">
            <tr>
                <th>Nama Alat</th>
                <th>Kode Alat</th>
                <th>Kategori Sparepart</th>
                <th>Sparepart</th>
                <th>Part Number</th>
                <th>Merk</th>
                <th>Quantity Requested</th>
                <th>Quantity Approved</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($TableData as $item)
                <tr>
                    <td class="text-center">{{ $item->linkRkbDetails[0]->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-' }}</td>
                    <td class="text-center">{{ $item->linkRkbDetails[0]->linkAlatDetailRkb->masterDataAlat->kode_alat ?? '-' }}</td>
                    <td class="text-center">{{ $item->kategoriSparepart->kode ?? '-' }}: {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                    <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                    <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                    <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity_requested }}</td>
                    <td class="text-center">{{ $item->quantity_approved ?? '-' }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center">
                        <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" {{ $item->linkRkbDetails[0]->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditDetailRKB({{ $item->id }})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" {{ $item->linkRkbDetails[0]->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center py-3 text-muted" colspan="10">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No data found
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
            let aksiIndex;

            headers.forEach((header, index) => {
                if (header.textContent.trim() === '') {
                    aksiIndex = index;
                }
            });

            if (aksiIndex !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[aksiIndex].style.width = '1%';
                });
            }
        });
    </script>
@endpush
