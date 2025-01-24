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

<div class="ibox-body table-responsive p-0 m-0">
    <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
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

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#table-data');
            const headers = table.querySelectorAll('thead th');
            let detailIndex, aksiIndex;

            headers.forEach((header, index) => {
                if (header.textContent.trim() === 'Detail') {
                    detailIndex = index;
                }
                if (header.textContent.trim() === 'Aksi') {
                    aksiIndex = index;
                }
            });

            if (detailIndex !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[detailIndex].style.width = '1%';
                });
            }

            if (aksiIndex !== undefined) {
                table.querySelectorAll('tbody tr').forEach(row => {
                    row.cells[aksiIndex].style.width = '1%';
                });
            }
        });
    </script>
@endpush
