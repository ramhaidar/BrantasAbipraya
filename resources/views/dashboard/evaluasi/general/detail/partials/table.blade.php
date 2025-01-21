@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
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
                @foreach ($alat_detail_rkbs as $alat_detail_rkb)
                    @foreach ($alat_detail_rkb->linkRkbDetails as $rkb_detail)
                        @php
                            $detail = $rkb_detail->detailRkbGeneral;
                            $sparepart = $detail->masterDataSparepart;
                            $kategori = $detail->kategoriSparepart;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $alat_detail_rkb->masterDataAlat->jenis_alat ?? '-' }}</td>
                            <td class="text-center">{{ $alat_detail_rkb->masterDataAlat->kode_alat ?? '-' }}</td>
                            <td class="text-center">{{ $kategori ? "{$kategori->kode}: {$kategori->nama}" : '-' }}</td>
                            <td class="text-center">{{ $sparepart->nama ?? '-' }}</td>
                            <td class="text-center">{{ $sparepart->part_number ?? '-' }}</td>
                            <td class="text-center">{{ $sparepart->merk ?? '-' }}</td>
                            <td class="text-center">{{ $detail->quantity_requested }}</td>
                            <td class="text-center">
                                @php
                                    $backgroundColor = $rkb->is_approved ? 'bg-primary-subtle' : ($detail->quantity_approved !== null ? 'bg-success-subtle' : 'bg-warning-subtle');
                                    $disabled = $rkb->is_approved || $rkb->is_evaluated ? 'disabled' : '';
                                @endphp
                                <input class="form-control text-center {{ $backgroundColor }}" name="quantity_approved[{{ $detail->id }}]" type="number" value="{{ $detail->quantity_approved ?? $detail->quantity_requested }}" {{ $disabled }} min="0">
                            </td>
                            <td class="text-center">{{ $detail->quantity_in_stock ?? 0 }}</td>
                            <td class="text-center">{{ $detail->satuan }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
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
