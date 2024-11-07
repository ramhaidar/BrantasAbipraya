<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style="width:100%">
        <thead class="table-primary">
            <tr>
                <th>Nama Proyek</th>
                <th>Jenis Alat</th>
                <th>Kode Alat</th>
                <th>Merek Alat</th>
                <th>Tipe Alat</th>
                @if (Auth::user()->role == 'Pegawai')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($alat as $alt)
                <tr>
                    <td>{{ $alt->nama_proyek }}</td>
                    <td>{{ $alt->jenis_alat }}</td>
                    <td>{{ $alt->kode_alat }}</td>
                    <td>{{ $alt->merek_alat }}</td>
                    <td>{{ $alt->tipe_alat }}</td>
                    @if (Auth::user()->role == 'Pegawai')
                        <td class="center space-nowrap">
                            <button class="btn btn-danger deleteBtn" data-id="{{ $alt->id }}"><i class="bi bi-trash"></i></button>
                            <button class="btn btn-warning ms-3 ubahBtn" data-id="{{ $alt->id }}" onclick='fillFormEdit("{{ $alt->id }}")'><i class="bi bi-pencil-square"></i></button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
