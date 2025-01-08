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
                <th class="text-center">Quantity Belum Diterima</th>
                <th class="text-center">Quantity Diterima</th>
                <th class="text-center">Foto Dokumentasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($spbDetails as $detail)
                @if ($detail->quantity_belum_diterima > 0)
                    @php $validIndex = $loop->index; @endphp
                    @foreach ($detail->linkSpbDetailSpb as $linkSpbDetailSpb)
                        <tr>
                            <td class="text-center">{{ $detail->MasterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->MasterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $detail->MasterDataSparepart->part_number }}</td>
                            <td class="text-center">{{ $detail->quantity_belum_diterima }}</td>
                            <td class="text-center">
                                <input name="id_detail_spb[]" type="hidden" value="{{ $detail->id }}">
                                <input name="id_master_data_sparepart[]" type="hidden" value="{{ $detail->id_master_data_sparepart }}">
                                <input name="id_master_data_supplier[]" type="hidden" value="{{ $detail->linkSpbDetailSpb[0]->spb->masterDataSupplier->id }}">
                                <input name="harga[]" type="hidden" value="{{ $detail->harga }}">
                                <input class="form-control text-center quantity-input" name="quantity[]" type="number" value="0" required>
                            </td>

                            <input class="form-control text-center" name="satuan[]" type="text" value="{{ $linkSpbDetailSpb->detailSpb->satuan }}" hidden required>

                            <td class="text-center">
                                <button class="btn btn-primary" id="upload-btn-{{ $detail->id }}" type="button" onclick="document.getElementById('documentation_photos_{{ $detail->id }}').click()">
                                    <i class="bi bi-upload"></i>
                                </button>
                                <input class="form-control d-none documentation-photos" id="documentation_photos_{{ $detail->id }}" name="documentation_photos[{{ $validIndex }}][]" type="file" accept="image/*" multiple onchange="updateButtonClass({{ $detail->id }})">
                            </td>
                        </tr>
                    @endforeach
                @endif
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
