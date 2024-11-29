@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            padding: 4px 8px;
            vertical-align: middle;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Nama Alat</th>
                <th class="text-center">Kode Alat</th>
                <th class="text-center">Kategori Sparepart</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Nama Mekanik</th>
                <th class="text-center">Quantity Requested</th>
                <th class="text-center">Quantity Approved</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                @foreach ($item->linkAlatDetailRkbs as $item2)
                    @foreach ($item2->linkRkbDetails as $item3)
                        <tr>
                            <td class="text-center">{{ $item2->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $item2->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->kategoriSparepart->kode }}: {{ $item3->detailRkbUrgent->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->part_number }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->nama_mekanik }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->quantity_requested }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->quantity_approved ?? '-' }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->satuan }}</td>
                            <td class="text-center"><button class="btn btn-warning mx-1 ubahBtn" data-id="${row.id}" ${disabled} onclick="fillFormEditDetailRKB(${row.id})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="${row.id}" ${disabled}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    {{-- <td>{{ $item->linkAlatDetailRkbs }}</td> --}}
                    {{-- <td>{{ $item->masterDataAlat->kode_alat }}</td>
                    <td>{{ $item->masterDataAlat->kategori_sparepart->kategori_sparepart }}</td>
                    <td>{{ $item->masterDataAlat->sparepart->sparepart }}</td>
                    <td>{{ $item->masterDataAlat->part_number }}</td>
                    <td>{{ $item->masterDataAlat->merk }}</td>
                    <td>{{ $item->masterDataAlat->mekanik->nama_mekanik }}</td>
                    <td>{{ $item->quantity_requested }}</td>
                    <td>{{ $item->quantity_approved }}</td>
                    <td>{{ $item->masterDataAlat->satuan->satuan }}</td>
                    <td class="text-center"> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3x')
    <script>
        $(document).ready(function() {
            const lastPageKey = 'lastPage_detail_rkb';

            var lastPage = localStorage.getItem(lastPageKey) ? parseInt(localStorage.getItem(lastPageKey)) : 0;

            var table = $('#table-data').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('detail_rkb_urgent.getData', $rkb->id) }}",
                    type: "GET"
                },
                language: {
                    paginate: {
                        previous: '<i class="bi bi-caret-left"></i>',
                        next: '<i class="bi bi-caret-right"></i>'
                    }
                },
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
                ordering: true,
                order: [],
                displayStart: lastPage * 10,
                columnDefs: [{
                    targets: 10, // Target column for actions
                    className: 'text-center nowrap-column',
                    orderable: false,
                    searchable: false,
                    width: "1%",
                    render: function(data, type, row) {
                        const disabled = row.is_finalized ? 'disabled' : '';
                        return `
<button class="btn btn-warning mx-1 ubahBtn" ${disabled} onclick="fillFormEditDetailRKB(${row.id})" data-id="${row.id}">
<i class="bi bi-pencil-square"></i>
</button>
<button class="btn btn-danger mx-1 deleteBtn" ${disabled} data-id="${row.id}">
<i class="bi bi-trash"></i>
</button>
`;
                    }
                }],
                columns: [{
                        data: 'namaAlat',
                        name: 'namaAlat',
                        className: 'text-center'
                    },
                    {
                        data: 'kodeAlat',
                        name: 'kodeAlat',
                        className: 'text-center'
                    },
                    {
                        data: 'kategoriSparepart',
                        name: 'kategoriSparepart',
                        className: 'text-center'
                    },
                    {
                        data: 'masterDataSparepart',
                        name: 'masterDataSparepart',
                        className: 'text-center'
                    },
                    {
                        data: 'partNumber',
                        name: 'partNumber',
                        className: 'text-center'
                    },
                    {
                        data: 'merk',
                        name: 'merk',
                        className: 'text-center'
                    },
                    {
                        data: 'nama_mekanik',
                        name: 'nama_mekanik',
                        className: 'text-center'
                    },
                    {
                        data: 'quantity_requested',
                        name: 'quantity_requested',
                        className: 'text-center'
                    },
                    {
                        data: 'quantity_approved',
                        name: 'quantity_approved',
                        className: 'text-center'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        className: 'text-center'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'text-center'
                    }
                ]
            });

            table.on('page', function() {
                var currentPage = table.page();
                localStorage.setItem(lastPageKey, currentPage);
            });
        });
    </script>
@endpush
