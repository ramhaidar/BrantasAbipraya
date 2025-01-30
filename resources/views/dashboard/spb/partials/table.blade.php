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

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">No RKB</th>
                <th class="text-center">Proyek</th>
                <th class="text-center">Periode</th>
                <th class="text-center">Tipe</th>
                <th class="text-center">Detail SPB</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($TableData as $item)
                <tr>
                    <td>{{ $item->nomor }}</td>
                    <td>{{ $item->proyek->nama ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
                    <td>{{ ucfirst($item->tipe) }}</td>
                    <td>
                        <a class="btn btn-primary mx-1 detailBtn" data-id="{{ $item->id }}" href="{{ route('spb.detail.index', $item->id) }}">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center py-3 text-muted" colspan="6">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No RKB found
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
            const textsToCheck = ['Detail SPB'];
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
