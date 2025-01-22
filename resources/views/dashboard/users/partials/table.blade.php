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
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class=table-primary>
            <tr>
                <th class="text-center">Name</th>
                <th class="text-center">Username</th>
                <th class="text-center">Jenis Kelamin</th>
                <th class="text-center">Role</th>
                <th class="text-center">Phone</th>
                <th class="text-center">Email</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody id=body-table>
            @foreach ($users as $user)
                <tr>
                    <td class="text-center">{{ $user->name }}</td>
                    <td class="text-center">{{ $user->username }}</td>
                    <td class="text-center">{{ $user->sex }}</td>
                    <td class="text-center">{{ $user->role }}</td>
                    <td class="text-center">{{ $user->phone }}</td>
                    <td class="text-center">{{ $user->email }}</td>
                    <td class="text-center">
                        <button class="btn btn-danger deleteBtn" data-id="{{ $user->id }}" type="button">
                            <i class="bi bi-trash3"></i>
                        </button>
                        <a class="btn btn-warning ms-3" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $user->id }})">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            new DataTable('#table-data', {
                language: {
                    paginate: {
                        previous: '<i class="bi bi-caret-left"></i>',
                        next: '<i class="bi bi-caret-right"></i>'
                    }
                },
                pageLength: -1,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [],
                ordering: false,
            });
        });
    </script>
@endpush
