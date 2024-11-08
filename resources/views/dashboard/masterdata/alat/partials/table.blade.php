<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style="width:100%">
        <thead class="table-primary">
            <tr>
                <th>Jenis Alat</th>
                <th>Kode Alat</th>
                <th>Merek Alat</th>
                <th>Tipe Alat</th>
                <th class="col-action text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alat as $alt)
                <tr>
                    <td>{{ $alt->jenis_alat }}</td>
                    <td>{{ $alt->kode_alat }}</td>
                    <td>{{ $alt->merek_alat }}</td>
                    <td>{{ $alt->tipe_alat }}</td>
                    <td class="center space-nowrap p-0 m-0">
                        <button class="btn btn-danger deleteBtn p-0 m-0" data-id="{{ $alt->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="btn btn-warning ubahBtn p-0 m-0" data-id="{{ $alt->id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
    .col-action {
        width: 1%;
        white-space: nowrap;
    }
</style>

@push('scripts_3')
    <script>
        $('#table-data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master-data.alat.getData') }}",
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
            columns: [{
                    data: 'jenis_alat',
                    name: 'jenis_alat'
                },
                {
                    data: 'kode_alat',
                    name: 'kode_alat'
                },
                {
                    data: 'merek_alat',
                    name: 'merek_alat'
                },
                {
                    data: 'tipe_alat',
                    name: 'tipe_alat'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
        <button class="btn btn-danger deleteBtn" data-id="${row.id}">
            <i class="bi bi-trash"></i>
        </button>
        <button class="btn btn-warning ms-3 ubahBtn" data-id="${row.id}" onclick="fillFormEdit(${row.id})">
            <i class="bi bi-pencil-square"></i>
        </button>
    `;
                    }
                }
            ]
        });
    </script>
@endpush
