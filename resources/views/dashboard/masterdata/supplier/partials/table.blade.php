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

<div class="ibox-body table-responsive p-0 m-0" style="overflow: auto">
    <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
        <thead class="table-primary">
            <tr>
                <th>Nama Supplier</th>
                <th>Alamat Supplier</th>
                <th>Contact Person</th>
                <th>Detail</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suppliers as $supplier)
                <tr>
                    <td class="text-center">{{ $supplier->nama }}</td>
                    <td class="text-center">{{ $supplier->alamat }}</td>
                    <td class="text-center">{{ $supplier->contact_person }}</td>
                    <td class="text-center">
                        <button class="btn btn-primary detailBtn" data-id="{{ $supplier->id }}">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $supplier->id }})">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $supplier->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center py-3 text-muted" colspan="5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No suppliers found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
