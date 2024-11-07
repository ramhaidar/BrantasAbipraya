<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style="width:100%">
        <thead class="table-primary">
            <tr>
                <th scope=col>Supplier</th>
                <th scope=col>Detail</th>
                <th scope=col></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proyeks as $proyek)
                <tr>
                    <td scope=col>{{ $proyek->nama_proyek }}
                    <td class="m-0 p-0">
                        <button class="btn text-primary m-0 ps-2" onclick='getDetailProyek("{{ $proyek->id }}")'>Detail</button>
                    </td>
                    <td class=center scope=col>
                        <button class="btn btn-danger" data-bs-target=#modalForDelete data-bs-toggle=modal onclick="validationSecond({{ $proyek->id }},'{{ $proyek->nama_proyek }}')"><i class="bi bi-trash3"></i></button>
                        <a class="btn btn-warning ms-3" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $proyek->id }})"><i class="bi bi-pencil-square"></i></a>
                </tr>
            @endforeach
    </table>
</div>
