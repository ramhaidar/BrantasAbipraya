@push('styles_3')
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Rencana Kebutuhan Barang</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="detailSpbForm" style="overflow-y: auto" novalidate method="POST"
                action="#">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="id_rkb" name="id_rkb" type="text"
                            value="{{ $rkb->id }}" hiddenx required>

                        <div class="col-12">
                            <label class="form-label required" for="proyek">Sparepart</label>
                            <select class="form-control" id="proyek" name="proyek" required>
                                <option value="">Pilih Sparepart</option>
                                @foreach ($rkb->linkAlatDetailRkbs as $linkAlatDetailRkb)
                                    @foreach ($linkAlatDetailRkb->linkRkbDetails as $detail)
                                        @if ($detail->detailRkbGeneral)
                                            <option value="{{ $detail->detailRkbGeneral->masterDataSparepart->id }}"
                                                data-id="{{ $linkAlatDetailRkb->id }}">
                                                {{ $detail->detailRkbGeneral->masterDataSparepart->nama }} -
                                                {{ $detail->detailRkbGeneral->masterDataSparepart->part_number }}
                                            </option>
                                        @endif
                                        @if ($detail->detailRkbUrgent)
                                            <option value="{{ $detail->detailRkbUrgent->masterDataSparepart->id }}"
                                                data-id="{{ $linkAlatDetailRkb->id }}">
                                                {{ $detail->detailRkbUrgent->masterDataSparepart->nama }} -
                                                {{ $detail->detailRkbUrgent->masterDataSparepart->part_number }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="merk">Merk</label>
                            <input class="form-control" id="merk" name="merk" type="text" placeholder="Merk"
                                required>
                            <div class="invalid-feedback">Merk diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity">Quantity</label>
                            <input class="form-control" id="quantity" name="quantity" type="number"
                                placeholder="Quantity" required min="1" value="1">
                            <div class="invalid-feedback">Quantity diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="harga">Harga</label>
                            <input class="form-control" id="harga" name="harga" type="text" placeholder="Harga"
                                required min="1" value="Rp 15.000">
                            <div class="invalid-feedback">Harga diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" id="add-sparepart" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            const $modal = $('#modalForAdd');
            const $select = $('#proyek');

            $modal.on('show.bs.modal', function(event) {
                // Get the button that triggered the modal
                const $button = $(event.relatedTarget);
                const id = $button.data('id');

                // Show only options matching the data-id
                $select.find('option').each(function() {
                    const $option = $(this);
                    if ($option.data('id') === id) {
                        $option.show();
                    } else {
                        $option.hide();
                    }
                });

                // Reset selection
                $select.val('');
            });

            const $form = $('#detailSpbForm');

            $form.on('submit', function(event) {
                if (!$form[0].checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                $form.addClass('was-validated');
            });

            const formElement = $form[0]; // Get the raw DOM element
            formElement.querySelectorAll('input').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });
        });
    </script>
@endpush
