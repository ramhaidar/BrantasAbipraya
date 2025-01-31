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
                    <th>Name</th>
                    <th>Username</th>
                    <th>Jenis Kelamin</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->name }}</td>
                        <td class="text-center">{{ $user->username }}</td>
                        <td class="text-center">{{ $user->sex }}</td>
                        <td class="text-center">{{ $user->role }}</td>
                        <td class="text-center">{{ $user->phone }}</td>
                        <td class="text-center">{{ $user->email }}</td>
                        <td class="text-center">
                            <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $user->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $user->id }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="7">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No users found
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
