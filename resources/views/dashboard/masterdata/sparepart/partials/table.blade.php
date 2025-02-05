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
                    <th>Nama Sparepart</th>
                    <th>Part Number</th>
                    <th>Merk Sparepart</th>
                    <th>Kode</th>
                    <th>Jenis</th>
                    <th>Sub Jenis</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $sparepart)
                    <tr>
                        <td class="text-center">{{ $sparepart['nama'] }}</td>
                        <td class="text-center">{{ $sparepart['part_number'] }}</td>
                        <td class="text-center">{{ $sparepart['merk'] }}</td>
                        <td class="text-center">{{ $sparepart->kategoriSparepart->kode }}</td>
                        <td class="text-center">{{ $sparepart->kategoriSparepart->jenis }}</td>
                        <td class="text-center">{{ $sparepart->kategoriSparepart->sub_jenis ?? '-' }}</td>
                        <td class="text-center">{{ $sparepart->kategoriSparepart->nama }}</td>
                        <td class="text-center">
                            <button class="btn btn-primary detailBtn" data-id="{{ $sparepart['id'] }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $sparepart['id'] }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $sparepart['id'] }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="9">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No spareparts found
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
