<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data APB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto needs-validation" id="addDataFormAdd" method="POST" action="{{ route('apb.post.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="id_proyek_add" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12" hidden>
                            <label class="form-label" for="tipe_add">Tipe ATB</label>
                            <select class="form-control" id="pilihan-proyek-add" name="tipe">
                                <option value="hutang-unit-alat" {{ $page == 'Data APB EX Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                <option value="panjar-unit-alat" {{ $page == 'Data APB EX Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                <option value="mutasi-proyek" {{ $page == 'Data APB EX Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                <option value="panjar-proyek" {{ $page == 'Data APB EX Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal_add">Tanggal</label>
                            <input class="form-control datepicker" id="tanggal_add" name="tanggal" type="text" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal diperlukan.</div>
                        </div>

                        <!-- Removed root_cause select field -->

                        <div class="col-12">
                            <label class="form-label required" for="mekanik_add">Mekanik</label>
                            <input class="form-control" id="mekanik_add" name="mekanik" type="text" required>
                            <div class="invalid-feedback">Mekanik diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_alat_add">Pilih Alat</label>
                            <select class="form-control" id="id_alat_add" name="id_alat" required>
                                <option value="">Pilih Alat</option>
                                @foreach ($alats as $alat)
                                    <option value="{{ $alat->id }}">{{ $alat->MasterDataAlat->jenis_alat }} - {{ $alat->MasterDataAlat->kode_alat }} - {{ $alat->MasterDataAlat->merek_alat }} - {{ $alat->MasterDataAlat->tipe_alat }} - {{ $alat->MasterDataAlat->serial_number }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Alat diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_saldo_add">Pilih Sparepart</label>
                            <select class="form-control" id="id_saldo_add" name="id_saldo" required>
                                <option value="">Pilih Sparepart</option>
                                @foreach ($spareparts as $saldo)
                                    <option data-satuan="{{ $saldo->satuan }}" value="{{ $saldo->id }}">
                                        {{ $saldo->masterDataSparepart->nama }} -
                                        {{ $saldo->masterDataSparepart->merk }} -
                                        {{ $saldo->masterDataSparepart->part_number }}
                                        (Stok: {{ $saldo->quantity }})
                                        [Masuk: {{ \Carbon\Carbon::parse($saldo->atb->tanggal)->format('d/m/Y') }}]
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity_add">Quantity <span id="SatuanPlaceholderAdd"></span></label>
                            <input class="form-control" id="quantity_add" name="quantity" type="number" min="1" max="1" disabled required>
                            <div class="invalid-feedback">Quantity diperlukan dan tidak boleh melebihi stok yang tersedia.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" id="resetButtonAdd" type="button">Reset</button>
                    <button class="btn btn-success w-25" id="submitButtonAdd" type="submit">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize alat select
            $('#id_alat_add').select2({
                placeholder: "Pilih Alat",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Initialize sparepart select
            $('#id_saldo_add').select2({
                placeholder: "Pilih Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Add validation classes on change for Select2 elements
            ['#id_alat_add', '#id_saldo_add'].forEach(function(selector) {
                $(selector).on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $(this).next('.select2-container').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
                    }
                });
            });

            // Handle sparepart selection change
            $('#id_saldo_add').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const quantityInput = $('#quantity_add');
                const satuanPlaceholder = $('#SatuanPlaceholderAdd');

                // Disable quantity input if no sparepart selected
                if (!$(this).val()) {
                    quantityInput.prop('disabled', true).val('');
                    satuanPlaceholder.text('');
                    return;
                }

                let maxQuantity = 0;
                const stockMatch = selectedOption.text().match(/\(Stok: (\d+)\)/);
                if (stockMatch && stockMatch[1]) {
                    maxQuantity = parseInt(stockMatch[1]);
                }

                // Get satuan from the data attribute
                const satuan = selectedOption.data('satuan');
                if (satuan && satuan.trim() !== '') {
                    satuanPlaceholder.text(`(dalam ${satuan})`);
                } else {
                    satuanPlaceholder.text('');
                }

                // Enable and update quantity input constraints
                quantityInput.prop('disabled', false);
                quantityInput.attr('max', maxQuantity);
                quantityInput.val('');
                quantityInput.attr('title', `Quantity harus antara 1 dan ${maxQuantity}${satuan ? ' '+satuan : ''}`);
            });

            // Add quantity validation on input
            $('#quantity_add').on('input', function() {
                const max = parseInt($(this).attr('max'));
                const value = parseInt($(this).val());

                if (value > max) {
                    $(this).val(max);
                    alert('Quantity tidak boleh melebihi stok yang tersedia (' + max + ')');
                }
            });

            // Form submission handler
            $('#addDataFormAdd').on('submit', function(e) {
                e.preventDefault();
                let isValid = true;

                // Validate all required fields
                $(this).find('[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                // Check quantity validation
                const quantityInput = $('#quantity_add');
                const max = parseInt(quantityInput.attr('max'));
                const value = parseInt(quantityInput.val());

                if (value > max || value < 1 || !value) {
                    quantityInput.addClass('is-invalid');
                    isValid = false;
                }

                if (isValid) {
                    // Disable button and show spinner
                    $('#submitButtonAdd').prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');

                    // Use setTimeout to ensure UI updates before submission
                    setTimeout(() => {
                        this.submit();
                    }, 50);
                }
            });

            // Reset button handler
            $('#resetButtonAdd').on('click', function() {
                const form = $('#addDataFormAdd')[0];
                form.reset();
                $('#submitButtonAdd').prop('disabled', false).html('Tambah Data');
                $('#id_alat_add, #id_saldo_add').val('').trigger('change');
            });
        });
    </script>
@endpush
