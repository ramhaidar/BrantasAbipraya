<div class="fade modal" id="modalForAddBypass" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddBypassLabel">Tambah Data APB (Bypass)</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto need-validation" id="addDataBypassForm" method="POST" action="{{ route('apb.post.store.bypass') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="id_proyek_bypass" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12" hidden>
                            <label class="form-label" for="tipe_bypass">Tipe ATB</label>
                            <select class="form-control" name="tipe">
                                <option value="hutang-unit-alat" {{ $page == 'Data APB EX Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                <option value="panjar-unit-alat" {{ $page == 'Data APB EX Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                <option value="mutasi-proyek" {{ $page == 'Data APB EX Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                <option value="panjar-proyek" {{ $page == 'Data APB EX Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal_bypass">Tanggal</label>
                            <input class="form-control datepicker" id="tanggal_bypass" name="tanggal" type="text" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="mekanik_bypass">Mekanik</label>
                            <input class="form-control" id="mekanik_bypass" name="mekanik" type="text" required>
                            <div class="invalid-feedback">Mekanik diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_master_data_alat">Pilih Alat</label>
                            <select class="form-control" id="id_master_data_alat" name="id_master_data_alat" required>
                                <option value="">Pilih Alat</option>
                                <option value="workshop">WORKSHOP</option>
                                @foreach ($masterDataAlats as $masterDataAlat)
                                    @if ($masterDataAlat->jenis_alat != 'Workshop')
                                        <option value="{{ $masterDataAlat->id }}">{{ $masterDataAlat->jenis_alat }} - {{ $masterDataAlat->kode_alat }} - {{ $masterDataAlat->merek_alat }} - {{ $masterDataAlat->tipe_alat }} - {{ $masterDataAlat->serial_number }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Alat diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_saldo_bypass">Pilih Sparepart</label>
                            <select class="form-control" id="id_saldo_bypass" name="id_saldo" required>
                                <option value="">Pilih Sparepart</option>
                                @foreach ($spareparts as $saldo)
                                    <option data-satuan="{{ $saldo->satuan ?? ($saldo->masterDataSparepart->satuan ?? '') }}" value="{{ $saldo->id }}">
                                        {{ $saldo->masterDataSparepart->nama }} -
                                        {{ $saldo->masterDataSparepart->merk }} -
                                        {{ $saldo->masterDataSparepart->part_number }}
                                        (Stok: {{ $saldo->quantity }})
                                        [Masuk: {{ \Carbon\Carbon::parse($saldo->atb->tanggal)->format('d/m/Y') }}]
                                        {Harga: Rp{{ number_format($saldo->harga, 2, ',', '.') }}}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="quantity_bypass">
                                <span class="p-0 m-0">Quantity</span><span id="SatuanPlaceholderBypass"></span>
                            </label>
                            <input class="form-control" id="quantity_bypass" name="quantity" type="number" min="1" max="1" disabled required>
                            <div class="invalid-feedback">Quantity diperlukan dan tidak boleh melebihi stok yang tersedia.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" id="resetButtonBypass" type="button">Reset</button>
                    <button class="btn btn-success w-25" id="submitButtonBypass" type="submit">Tambah Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Add date restriction logic at the beginning
            const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};

            // Destroy existing datepicker to reinitialize with our settings
            $('#tanggal_bypass').datepicker('destroy');

            if (isKoordinatorProyek) {
                // Calculate valid date range (26th of previous month to 25th of current month)
                const today = new Date();
                const startDate = new Date(today.getFullYear(), today.getMonth() - 1, 26);
                const endDate = new Date(today.getFullYear(), today.getMonth(), 25);

                // Format dates for display
                const formatDate = (date) => {
                    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
                };

                // Initialize datepicker with strict constraints
                $('#tanggal_bypass').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    minDate: startDate,
                    maxDate: endDate,
                    beforeShowDay: function(date) {
                        return [date >= startDate && date <= endDate, ''];
                    },
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set initial date
                let initialDate;
                if (today >= startDate && today <= endDate) {
                    initialDate = today;
                } else if (today > endDate) {
                    initialDate = endDate;
                } else {
                    initialDate = startDate;
                }
                $('#tanggal_bypass').datepicker('setDate', initialDate);

                // Update placeholder and validation message
                $('#tanggal_bypass').attr('placeholder',
                    `Tanggal antara ${formatDate(startDate)} - ${formatDate(endDate)}`);
                $('#tanggal_bypass').closest('div').find('.invalid-feedback').text(
                    `Tanggal harus antara ${formatDate(startDate)} - ${formatDate(endDate)}`);

                // Add custom validation
                $('#tanggal_bypass').on('change', function() {
                    try {
                        const input = $(this).val();
                        if (!input) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const parts = input.split('-');
                        if (parts.length !== 3) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const inputDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));

                        if (isNaN(inputDate) || inputDate < startDate || inputDate > endDate) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            $(this).datepicker('setDate', initialDate);
                            return false;
                        } else {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                            return true;
                        }
                    } catch (e) {
                        console.error('Date validation error:', e);
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                });
            } else {
                // FOR OTHER ROLES - Initialize without date restrictions
                $('#tanggal_bypass').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set today as default date
                $('#tanggal_bypass').datepicker('setDate', new Date());
            }

            // Add Reset button functionality
            $('#resetButtonBypass').on('click', function() {
                // Reset all form fields
                $('#addDataBypassForm')[0].reset();

                // Reset Select2 fields
                $('#id_master_data_alat').val('').trigger('change');
                $('#id_saldo_bypass').val('').trigger('change');

                // Disable quantity input
                $('#quantity_bypass').prop('disabled', true).val('');

                // Remove validation classes
                $('#addDataBypassForm').find('.is-invalid').removeClass('is-invalid');
                $('#addDataBypassForm').find('.is-valid').removeClass('is-valid');
            });

            // Initialize alat select
            $('#id_master_data_alat').select2({
                placeholder: "Pilih Alat",
                allowClear: true,
                dropdownParent: $('#modalForAddBypass'),
                width: '100%'
            });

            // Initialize sparepart select
            $('#id_saldo_bypass').select2({
                placeholder: "Pilih Sparepart",
                allowClear: true,
                dropdownParent: $('#modalForAddBypass'),
                width: '100%'
            });

            // Add validation classes on change for Select2 elements
            ['#id_master_data_alat', '#id_saldo_bypass'].forEach(function(selector) {
                $(selector).on('change', function() {
                    if ($(this).val()) {
                        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $(this).next('.select2-container').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
                    }
                });
            });

            // Handle sparepart selection change
            $('#id_saldo_bypass').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const quantityInput = $('#quantity_bypass');
                const SatuanPlaceholder = $('#SatuanPlaceholderBypass');

                // Disable quantity input if no sparepart selected
                if (!$(this).val()) {
                    quantityInput.prop('disabled', true).val('');
                    SatuanPlaceholder.text('');
                    return;
                }

                let maxQuantity = 0;
                const stockMatch = selectedOption.text().match(/\(Stok: (\d+)\)/);
                if (stockMatch && stockMatch[1]) {
                    maxQuantity = parseInt(stockMatch[1]);
                }

                // Get satuan from the data attribute
                const satuan = selectedOption.data('satuan');
                SatuanPlaceholder.text(satuan ? ` (dalam ${satuan})` : '');

                // Enable and update quantity input constraints
                quantityInput.prop('disabled', false);
                quantityInput.attr('max', maxQuantity);
                quantityInput.val('');
                quantityInput.attr('title', `Quantity harus antara 1 dan ${maxQuantity}${satuan ? ' '+satuan : ''}`);
            });

            // Add quantity validation on input
            $('#quantity_bypass').on('input', function() {
                const max = parseInt($(this).attr('max'));
                const value = parseInt($(this).val());

                if (value > max) {
                    $(this).val(max);
                    alert('Quantity tidak boleh melebihi stok yang tersedia (' + max + ')');
                }
            });

            // Form submission handler
            $('#addDataBypassForm').on('submit', function(e) {
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
                const quantityInput = $('#quantity_bypass');
                const max = parseInt(quantityInput.attr('max'));
                const value = parseInt(quantityInput.val());

                if (value > max || value < 1 || !value) {
                    quantityInput.addClass('is-invalid');
                    isValid = false;
                }

                if (isValid) {
                    $('#submitButtonBypass').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    this.submit();
                }
            });
        });
    </script>
@endpush
