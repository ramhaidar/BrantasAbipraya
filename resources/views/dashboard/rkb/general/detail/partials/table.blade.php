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
                    @forelse ($item->linkRkbDetails as $detail)
                        <tr>
                            <td class="text-center">{{ $detail->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-' }}</td>
                            <td class="text-center">{{ $detail->linkAlatDetailRkb->masterDataAlat->kode_alat ?? '-' }}</td>
                            <td class="text-center">{{ $item->kategoriSparepart->kode ?? '-' }}: {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                            <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                            <td class="text-center">{{ $item->quantity_requested }}</td>
                            <td class="text-center">{{ $item->quantity_approved ?? '-' }}</td>
                            <td class="text-center">{{ $item->satuan }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditDetailRKB({{ $item->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="10">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No RKB details found
                            </td>
                        </tr>
                    @endforelse
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
