<style>
    #table-data-modal-add {
        font-size: 0.9em;
        white-space: nowrap;
    }

    #table-data-modal-add td,
    #table-data-modal-add th {
        /* padding: 4px 8px; */
        vertical-align: middle;
    }
</style>

<div class="col-12 table-responsive" id="spb-details-table">
    <label class="form-label">Detail SPB</label>
    <table class="table table-bordered table-striped" id="table-data-modal-add">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Nama Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity PO</th>
                <th class="text-center">Quantity Diterima</th>
                <th class="text-center">Foto Dokumentasi</th>
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
                    <td class="text-center">
                        <button class="btn btn-primary" id="upload-btn-{{ $item->id }}" type="button" onclick="document.getElementById('documentation_photos_{{ $item->id }}').click()">
                            <i class="bi bi-upload"></i>
                        </button>
                        <input class="form-control d-none" id="documentation_photos_{{ $item->id }}" name="documentation_photos[{{ $item->id }}][]" type="file" accept="image/*" multiple onchange="updateButtonClass({{ $item->id }})">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function updateButtonClass(itemId) {
        const input = document.getElementById(`documentation_photos_${itemId}`);
        const button = document.getElementById(`upload-btn-${itemId}`);
        if (input.files.length > 0) {
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
        } else {
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
        }
    }
</script>
