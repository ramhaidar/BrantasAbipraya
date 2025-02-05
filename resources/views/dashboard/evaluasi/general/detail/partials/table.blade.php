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

<form id="approveRkbForm" method="POST" action="">
    @csrf
    <div class="ibox-body table-responsive p-0 m-0">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>Nama Alat</th>
                    <th>Kode Alat</th>
                    <th>Kategori Sparepart</th>
                    <th>Sparepart</th>
                    <th>Part Number</th>
                    <th>Merk</th>
                    <th>Quantity Requested</th>
                    <th>Quantity Approved</th>
                    <th>Quantity in Stock</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Group items by part number
                    $groupedItems = $TableData->groupBy(function ($item) {
                        return $item->masterDataSparepart->part_number;
                    });
                @endphp

                @forelse ( $groupedItems as $partNumber => $items )
                    @php
                        $firstItem = $items->first();
                        $rowspan = $items->count();
                    @endphp

                    @foreach ($items as $index => $item)
                        @forelse ($item->linkRkbDetails as $detail)
                            <tr>
                                <td class="text-center">{{ $detail->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-' }}</td>
                                <td class="text-center">{{ $detail->linkAlatDetailRkb->masterDataAlat->kode_alat ?? '-' }}</td>
                                <td class="text-center">{{ $item->kategoriSparepart->kode ?? '-' }}:
                                    {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                                <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                                <td class="text-center">{{ $partNumber ?? '-' }}</td>
                                <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity_requested }}</td>
                                <td class="text-center">
                                    <input class="form-control text-center
                                    @if ($rkb->is_approved_svp) bg-primary-subtle
                                    @elseif ($rkb->is_approved_vp) bg-info-subtle
                                    @elseif($rkb->is_evaluated) bg-success-subtle 
                                    @else bg-warning-subtle @endif" name="quantity_approved[{{ $item->id }}]" type="number" value="{{ $item->quantity_approved ?? $item->quantity_requested }}" min="0" {{ $rkb->is_evaluated ? 'disabled' : '' }}>
                                </td>
                                @if ($index === 0)
                                    <td class="text-center" rowspan="{{ $rowspan }}">
                                        {{ $stockQuantities[$item->masterDataSparepart->id] ?? 0 }}</td>
                                @endif
                                <td class="text-center">{{ $item->satuan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-3 text-muted" colspan="10">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No RKB details found
                                </td>
                            </tr>
                        @endforelse
                    @endforeach
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="10">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <button class="btn btn-success btn-sm approveBtn" id="hiddenApproveRkbButton" type="submit" hidden></button>
    </form>

    @push('scripts_3')
        @include('scripts.adjustTableColumnWidthByHeaderText')
    @endpush
