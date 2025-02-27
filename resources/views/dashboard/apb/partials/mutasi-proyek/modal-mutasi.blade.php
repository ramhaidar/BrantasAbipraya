<div class="fade modal" id="modalForMutasi" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForMutasiLabel">Tambah Data APB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto" id="addDataFormMutasi" method="POST" action="{{ route('apb.post.mutasi') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="id_proyek_mutasi" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12" hidden>
                            <label class="form-label" for="tipe_mutasi">Tipe ATB</label>
                            <select class="form-control" id="pilihan-proyek-mutasi" name="tipe">
                                <option value="hutang-unit-alat" {{ $page == 'Data APB EX Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                <option value="panjar-unit-alat" {{ $page == 'Data APB EX Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                <option value="mutasi-proyek" {{ $page == 'Data APB EX Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                <option value="panjar-proyek" {{ $page == 'Data APB EX Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal_mutasi">Tanggal</label>
                            <input class="form-control datepicker" id="tanggal_mutasi" name="tanggal" type="text" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_saldo_mutasi">Pilih Sparepart</label>
                            <select class="form-control" id="id_saldo_mutasi" name="id_saldo" required>
                                <option value="">Pilih Sparepart</option>
                                @foreach ($sparepartsForMutasi as $saldo)
                                    @php
                                        $pendingQuantity = \App\Models\APB::where('id_saldo', $saldo->id)->where('tipe', 'mutasi-proyek')->where('status', 'pending')->sum('quantity');

                                        $availableQuantity = $saldo->quantity - $pendingQuantity;
                                    @endphp
                                    @if ($availableQuantity > 0)
                                        <option data-available="{{ $availableQuantity }}" data-satuan="{{ $saldo->satuan }}" value="{{ $saldo->id }}">
                                            {{ $saldo->masterDataSparepart->nama }} -
                                            {{ $saldo->masterDataSparepart->merk }} -
                                            {{ $saldo->masterDataSparepart->part_number }} -
                                            (Stok Tersedia: {{ $availableQuantity }})
                                            [Masuk: {{ \Carbon\Carbon::parse($saldo->atb->tanggal)->format('d/m/Y') }}]
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity_mutasi">
                                <span class="p-0 m-0">Quantity</span><span id="SatuanPlaceholder"></span>
                            </label>
                            <input class="form-control" id="quantity_mutasi" name="quantity" type="number" min="1" max="1" disabled required>
                            <div class="invalid-feedback">Quantity diperlukan dan tidak boleh melebihi stok yang tersedia.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_proyek_tujuan">Tujuan Proyek</label>
                            <select class="form-control" id="id_proyek_tujuan" name="id_proyek_tujuan" required>
                                <option value="">Pilih Proyek Tujuan</option>
                                @foreach ($proyeks as $p)
                                    @if ($p->id != $proyek->id)
                                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Proyek tujuan diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" id="resetButtonMutasi" type="button">Reset</button>
                    <button class="btn btn-success w-25" id="submitButtonMutasi" type="submit">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize datepicker for #tanggal
            var dateFormat = 'yy-mm-dd';
            var options = {
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                regional: 'id'
            };

            $('#tanggal_mutasi').datepicker(options);
            $.datepicker.setDefaults($.datepicker.regional['id']);

            // Initialize sparepart select
            $('#id_saldo_mutasi, #id_proyek_tujuan').select2({
                placeholder: "Pilih",
                allowClear: true,
                dropdownParent: $('#modalForMutasi'),
                width: '100%'
            });

            // Add validation classes on change for Select2 elements
            ['#id_saldo_mutasi', '#id_proyek_tujuan'].forEach(function(selector) {
                $(selector).on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $(this).next('.select2-container').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
                    }
                });
            });

            // Handle sparepart selection change
            $('#id_saldo_mutasi').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const quantityInput = $('#quantity_mutasi');
                const SatuanPlaceholder = $('#SatuanPlaceholder');

                // Disable quantity input if no sparepart selected
                if (!$(this).val()) {
                    quantityInput.prop('disabled', true).val('');
                    SatuanPlaceholder.text('');
                    return;
                }

                let maxQuantity = 0;
                const availableQuantity = selectedOption.data('available');
                if (availableQuantity) {
                    maxQuantity = parseInt(availableQuantity);
                }

                // Get satuan directly from the option's data attribute
                const satuan = selectedOption.data('satuan');
                console.log('Satuan:', satuan); // For debugging
                SatuanPlaceholder.text(` (dalam ${satuan})`);

                // Enable and update quantity input constraints
                quantityInput.prop('disabled', false);
                quantityInput.attr('max', maxQuantity);
                quantityInput.val('');
                quantityInput.attr('title', `Quantity harus antara 1 dan ${maxQuantity} ${satuan}`);
            });

            // Add quantity validation on input
            $('#quantity_mutasi').on('input', function() {
                const max = parseInt($(this).attr('max'));
                const value = parseInt($(this).val());

                if (value > max) {
                    $(this).val(max);
                    alert('Quantity tidak boleh melebihi stok yang tersedia (' + max + ')');
                }
            });

            // Form submission handler
            $('#addDataFormMutasi').on('submit', function(e) {
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
                const quantityInput = $('#quantity_mutasi');
                const max = parseInt(quantityInput.attr('max'));
                const value = parseInt(quantityInput.val());

                if (value > max || value < 1 || !value) {
                    quantityInput.addClass('is-invalid');
                    isValid = false;
                }

                if (isValid) {
                    // Disable the button and show spinner before form submission
                    $('#submitButtonMutasi').prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');

                    // Use setTimeout to ensure the UI updates before form submission
                    setTimeout(() => {
                        // Use the DOM element's submit method to avoid triggering this handler again
                        this.submit();
                    }, 50);
                }
            });

            // Reset button handler
            $('#resetButtonMutasi').on('click', function() {
                const form = $('#addDataFormMutasi')[0];
                form.reset();
                $('#submitButtonMutasi').prop('disabled', false).html('Tambah Data');
                $('#id_saldo_mutasi, #id_proyek_tujuan').val('').trigger('change');
                $('#SatuanPlaceholder').text('');
            });
        });
    </script>
@endpush
