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
        <thead class="table-primary">
            <tr>
                <th class="text-center">Nomor SPB</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Net</th>
                <th class="text-center">PPN</th>
                <th class="text-center">Bruto</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($atbs as $atb)
                <tr>
                    <td class="text-center spb-number" data-spb="{{ $atb->spb->nomor }}" data-id="{{ $atb->id }}">{{ $atb->spb->nomor }}</td>
                    <td class="text-center">{{ $atb->tanggal }}</td>
                    <td class="text-center">{{ $atb->id_master_data_sparepart }}</td>
                    <td class="text-center">{{ $atb->masterDataSupplier->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->part_number }}</td>
                    <td class="text-center">{{ $atb->quantity }}</td>
                    <td class="text-center">{{ $atb->detailSpb->satuan }}</td>
                    <td class="text-center">{{ $atb->harga }}</td>
                    <td class="text-center">{{ $atb->quantity * $atb->harga }}</td>
                    <td class="text-center">{{ $atb->quantity * $atb->harga * 0.11 }}</td>
                    <td class="text-center">{{ $atb->quantity * $atb->harga * 1.11 }}</td>
                    <td class="text-center action-cell">
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $atb->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });

            // Merge cells and handle action buttons
            var spbNumbers = {};
            $('#table-data tbody tr').each(function() {
                var spbNumber = $(this).find('.spb-number').data('spb');
                var id = $(this).find('.spb-number').data('id');

                if (spbNumbers[spbNumber]) {
                    spbNumbers[spbNumber].ids.push(id);
                    $(this).find('.spb-number').remove();
                    $(this).find('.action-cell').remove();
                } else {
                    spbNumbers[spbNumber] = {
                        ids: [id],
                        row: $(this),
                        actionCell: $(this).find('.action-cell')
                    };
                }
            });

            $.each(spbNumbers, function(spbNumber, data) {
                data.row.find('.spb-number').attr('rowspan', data.ids.length);
                data.actionCell.attr('rowspan', data.ids.length);
                data.row.find('.action-cell button').data('ids', data.ids.join(','));
            });

        });
    </script>
@endpush
