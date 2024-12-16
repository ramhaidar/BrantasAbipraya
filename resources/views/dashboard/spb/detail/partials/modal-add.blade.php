@push('styles_3')
    <style>
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            /* Ensure it's above modal content */
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
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
                            value="{{ $rkb->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="alat">Alat</label>
                            <select class="form-control" id="alat" name="alat" required>
                                <option value="" selected disabled>Pilih Alat</option>
                                @foreach ($rkb->linkAlatDetailRkbs as $linkAlatDetailRkb)
                                    <option value="{{ $linkAlatDetailRkb->masterDataAlat->id }}">
                                        {{ $linkAlatDetailRkb->masterDataAlat->jenis_alat }} -
                                        {{ $linkAlatDetailRkb->masterDataAlat->kode_alat }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Alat diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="sparepart">Sparepart</label>
                            <select class="form-control" id="sparepart" name="sparepart" required disabled>
                                <option value="" selected disabled>Pilih Sparepart</option>
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="supplier">Supplier</label>
                            <select class="form-control" id="supplier" name="supplier" required disabled>
                                <option value="" selected disabled>Pilih Supplier</option>
                            </select>
                            <div class="invalid-feedback">Supplier diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="merk">Merk</label>
                            <select class="form-control" id="merk" name="merk" required disabled>
                                <option value="" selected disabled>Pilih Merk</option>
                            </select>
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

    <script>
        $(document).ready(function() {
            function showLoading() {
                const $modal = $('#modalForAdd');
                return $(
                    '<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                ).appendTo($modal);
            }

            // Handle Alat change
            $('#alat').on('change', function() {
                const alatId = $(this).val();
                const $sparepart = $('#sparepart');

                // Reset and disable dependent dropdowns
                $('#sparepart, #supplier, #merk').each(function() {
                    $(this).html('<option value="" selected disabled>Pilih ' + $(this).prev('label')
                        .text().replace(' *', '') + '</option>').prop('disabled', true);
                });

                if (!alatId) return;

                const $loadingOverlay = showLoading();

                $.ajax({
                    url: "{{ route('spb.detail.getSparepart', ':id') }}".replace(':id', alatId),
                    type: 'GET',
                    success: function(response) {
                        response.forEach(function(sparepart) {
                            $sparepart.append(
                                `<option value="${sparepart.id}">${sparepart.nama} - ${sparepart.merk}</option>`
                            );
                        });
                        $sparepart.prop('disabled', false);
                    },
                    error: function() {
                        alert('Gagal memuat sparepart');
                    },
                    complete: function() {
                        $loadingOverlay.remove();
                    }
                });
            });

            // Handle Sparepart change
            $('#sparepart').on('change', function() {
                const sparepartId = $(this).val();
                const $supplier = $('#supplier');

                // Reset and disable dependent dropdowns
                $('#supplier, #merk').each(function() {
                    $(this).html('<option value="" selected disabled>Pilih ' + $(this).prev('label')
                        .text().replace(' *', '') + '</option>').prop('disabled', true);
                });

                if (!sparepartId) return;

                const $loadingOverlay = showLoading();

                $.ajax({
                    url: "{{ route('spb.detail.getSupplier', ':id') }}".replace(':id', sparepartId),
                    type: 'GET',
                    success: function(response) {
                        response.forEach(function(supplier) {
                            $supplier.append(
                                `<option value="${supplier.id}">${supplier.nama}</option>`
                            );
                        });
                        $supplier.prop('disabled', false);
                    },
                    error: function() {
                        alert('Gagal memuat supplier');
                    },
                    complete: function() {
                        $loadingOverlay.remove();
                    }
                });
            });

            // Handle Supplier change
            $('#supplier').on('change', function() {
                const supplierId = $(this).val();
                const sparepartId = $('#sparepart').val();
                const $merk = $('#merk');

                $merk.html('<option value="" selected disabled>Pilih Merk</option>').prop('disabled', true);

                if (!supplierId || !sparepartId) return;

                const $loadingOverlay = showLoading();

                $.ajax({
                    url: "{{ route('spb.detail.getMerk', ':id') }}".replace(':id', sparepartId),
                    type: 'GET',
                    success: function(response) {
                        response.forEach(function(merk) {
                            $merk.append(
                                `<option value="${merk.id}">${merk.merk}</option>`
                            );
                        });
                        $merk.prop('disabled', false);
                    },
                    error: function() {
                        alert('Gagal memuat merk');
                    },
                    complete: function() {
                        $loadingOverlay.remove();
                    }
                });
            });
        });
    </script>
@endpush
