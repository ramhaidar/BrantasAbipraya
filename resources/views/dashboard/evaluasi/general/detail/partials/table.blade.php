@push('styles_3')
    <style>
        .table-container {
            font-size: 0.9em;
            overflow-x: auto;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endpush

<form id="approveRkbForm" method="POST" action="{{ route('evaluasi_rkb_general.detail.approve', $rkb->id) }}">
    @csrf
    <div class="ibox-body ms-0 ps-0 table-responsive">
        <table class="m-0 table table-bordered table-striped" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Nama Alat</th>
                    <th class="text-center">Kode Alat</th>
                    <th class="text-center">Kategori Sparepart</th>
                    <th class="text-center">Sparepart</th>
                    <th class="text-center">Part Number</th>
                    <th class="text-center">Merk</th>
                    <th class="text-center">Quantity Requested</th>
                    <th class="text-center">Quantity Approved</th>
                    <th class="text-center">Quantity in Stock</th>
                    <th class="text-center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $item)
                    <tr>
                        <td class="text-center">{{ $item->master_data_alat ?? '-' }}</td>
                        <td class="text-center">{{ $item->kode_alat ?? '-' }}</td>
                        <td class="text-center">{{ $item->kategori_sparepart ?? '-' }}</td>
                        <td class="text-center">{{ $item->sparepart_nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->part_number ?? '-' }}</td>
                        <td class="text-center">{{ $item->merk ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity_requested }}</td>
                        <td class="text-center">
                            @php
                                $backgroundColor = $rkb->is_approved ? 'bg-primary-subtle' : ($item->quantity_approved !== null ? 'bg-success-subtle' : 'bg-warning-subtle');
                                $disabled = $rkb->is_approved || $rkb->is_evaluated ? 'disabled' : '';
                            @endphp
                            <input class="form-control text-center {{ $backgroundColor }}" name="quantity_approved[{{ $item->id }}]" type="number" value="{{ $item->quantity_approved ?? $item->quantity_requested }}" {{ $disabled }} min="0">
                        </td>
                        <td class="text-center">{{ $item->quantity_in_stock ?? 0 }}</td>
                        <td class="text-center">{{ $item->satuan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $details->links() }}
    </div>

    <button class="btn btn-success btn-sm approveBtn" id="hiddenApproveRkbButton" type="submit" hidden></button>
</form>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });
        });
    </script>
@endpush
