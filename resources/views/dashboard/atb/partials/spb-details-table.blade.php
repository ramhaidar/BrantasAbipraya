<div class="col-12" id="spb-details-table">
    <label class="form-label">Detail SPB</label>
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Nama Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity PO</th>
                <th class="text-center">Quantity Diterima</th>
            </tr>
        </thead>
        <tbody id="spb-details-body">
            @foreach ($spbDetails as $item)
                <tr>
                    <td class="text-center">{{ $item->masterDataSparepart->nama }}</td>
                    <td class="text-center">{{ $item->masterDataSparepart->merk }}</td>
                    <td class="text-center">{{ $item->masterDataSparepart->part_number }}</td>
                    <td class="text-center">{{ $item->quantity_po }}</td>
                    <td class="text-center">
                        <input class="form-control text-center quantity-input" name="quantity_diterima[{{ $item->id }}]" type="number" value="0" max="{{ $item->quantity_po }}" min="0" required>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
