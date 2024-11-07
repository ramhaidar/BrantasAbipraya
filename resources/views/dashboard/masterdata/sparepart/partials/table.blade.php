<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style="width:100%">
        <thead class="table-primary">
            <tr>
                <th class="text-start">Sparepart</th>
                <th class="text-start">Part Number</th>
                <th class="text-start">Buffer Stock</th>
                @if (Auth::user()->role == 'Pegawai')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($masterData as $data)
                <tr>
                    <td>{{ $data->sparepart }}</td>
                    <td>{{ $data->part_number }}</td>
                    <td>{{ $data->buffer_stock }}</td>
                    @if (Auth::user()->role == 'Pegawai')
                        <td class="center space-nowrap">
                            <button class="btn btn-danger deleteBtn" data-id="{{ $data->id }}"><i class="bi bi-trash"></i></button>
                            <button class="btn btn-warning ms-3 ubahBtn" data-id="{{ $data->id }}" onclick='fillFormEdit("{{ $data->id }}")'><i class="bi bi-pencil-square"></i></button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
