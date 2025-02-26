@push('styles_3')
    @include('styles.tables')

    <style>
        #table-data th:nth-child(9),
        #table-data th:nth-child(10),
        #table-data td:nth-child(9),
        #table-data td:nth-child(10) {
            min-width: 10dvw;
            width: 10dvw;
        }
    </style>
@endpush

@php
    $headers = [
        [
            'title' => 'Nama Alat',
            'filterId' => 'jenis-alat',
            'paramName' => 'jenis_alat',
            'filter' => true,
            'uniqueValues' => $uniqueValues,
        ],
        [
            'title' => 'Kode Alat',
            'filterId' => 'kode-alat',
            'paramName' => 'kode_alat',
            'filter' => true,
            'uniqueValues' => $uniqueValues,
        ],
        [
            'title' => 'Kategori',
            'filterId' => 'kategori',
            'paramName' => 'kategori',
            'filter' => true,
            'uniqueValues' => $uniqueValues,
        ],
        [
            'title' => 'Sparepart Requested',
            'filterId' => 'sparepart',
            'paramName' => 'sparepart',
            'filter' => true,
            'uniqueValues' => $uniqueValues,
        ],
        [
            'title' => 'Sparepart PO',
            'filter' => false,
        ],
        [
            'title' => 'Quantity Sisa',
            'filterId' => 'quantity',
            'paramName' => 'quantity',
            'filter' => true,
            'uniqueValues' => $uniqueValues,
        ],
        [
            'title' => 'Quantity PO',
            'filter' => false,
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
            'uniqueValues' => $uniqueValues,
        ],
        [
            'title' => 'Harga',
            'filter' => false,
        ],
        [
            'title' => 'Jumlah Harga',
            'filter' => false,
        ],
    ];

    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    $resetUrl = request()->url();
    $queryParams = '';
    if (request()->has('search')) {
        $queryParams = '?search=' . request('search');
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <form id="detailSpbForm" action="{{ route('spb.detail.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input name="id_rkb" type="hidden" value="{{ $TableData->first()->id }}">
        <input id="spb_addendum_input" name="spb_addendum_id" type="hidden">
        <input id="selected-jenis-alat" name="selected_jenis_alat" type="hidden" value="{{ request('selected_jenis_alat') }}">
        <input id="selected-kode-alat" name="selected_kode_alat" type="hidden" value="{{ request('selected_kode_alat') }}">
        <input id="selected-kategori" name="selected_kategori" type="hidden" value="{{ request('selected_kategori') }}">
        <input id="selected-sparepart" name="selected_sparepart" type="hidden" value="{{ request('selected_sparepart') }}">
        <input id="selected-quantity" name="selected_quantity" type="hidden" value="{{ request('selected_quantity') }}">
        <input id="selected-satuan" name="selected_satuan" type="hidden" value="{{ request('selected_satuan') }}">

        @php
            $sparepartGroups = [];
            $totalItems = 0;
            $rkb = $TableData->first(); // Get the RKB model from paginator
            foreach ($rkb->linkAlatDetailRkbs as $detail1) {
                foreach ($detail1->linkRkbDetails as $detail2) {
                    $remainder = $detail2->detailRkbUrgent?->quantity_remainder ?? ($detail2->detailRkbGeneral?->quantity_remainder ?? 0);

                    if ($remainder <= 0) {
                        continue;
                    }
                    $totalItems++;

                    $sparepartName = $detail2->detailRkbUrgent->masterDataSparepart->nama ?? $detail2->detailRkbGeneral->masterDataSparepart->nama;
                    $satuan = $detail2->detailRkbUrgent->satuan ?? $detail2->detailRkbGeneral->satuan;
                    if (isset($detail2->detailRkbGeneral->kategoriSparepart)) {
                        $kategori = $detail2->detailRkbGeneral->kategoriSparepart->kode . ': ' . $detail2->detailRkbGeneral->kategoriSparepart->nama;
                    }
                    if (isset($detail2->detailRkbUrgent->kategoriSparepart)) {
                        $kategori = $detail2->detailRkbUrgent->kategoriSparepart->kode . ': ' . $detail2->detailRkbUrgent->kategoriSparepart->nama;
                    }

                    $groupKey = $sparepartName;

                    if (!isset($sparepartGroups[$groupKey])) {
                        $sparepartGroups[$groupKey] = [];
                    }

                    $sparepartGroups[$groupKey][] = [
                        'alat' => $detail1->masterDataAlat,
                        'detail' => $detail2,
                        'satuan' => $satuan,
                        'kategori' => $kategori,
                        'alat_detail_id' => $detail1->id,
                    ];
                }
            }
        @endphp

        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
            <div class="row p-0 m-0">
                <div class="col-md-4 p-0 m-0 pb-3">
                    <label class="form-label">Pilih Supplier</label>
                    <select class="form-select supplier-select-main" id="supplier_main" name="supplier_main" {{ $totalItems === 0 ? 'disabled' : '' }}>
                        <option value="" disabled selected>Pilih Supplier</option>
                        @foreach ($supplier as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @if ($totalItems === 0)
                        <small class="text-danger">Tidak ada item yang tersedia untuk pembuatan SPB</small>
                    @endif
                </div>
            </div>
        @endif

        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_jenis_alat') || request('selected_kode_alat') || request('selected_kategori') || request('selected_sparepart') || request('selected_quantity') || request('selected_satuan'))
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() . (request('search') ? '?search=' . request('search') : '') }}">
                    <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        @foreach ($headers as $header)
                            @include('components.table-header-filter', $header)
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse ($sparepartGroups as $sparepartName => $group)
                        @foreach ($group as $index => $data)
                            <tr>
                                <td class="text-center">{{ $data['alat']->jenis_alat }}</td>
                                <td class="text-center">{{ $data['alat']->kode_alat }}</td>
                                <td class="text-center">{{ $data['kategori'] }}</td>
                                <td class="text-center">
                                    @php
                                        $sparepart = $data['detail']->detailRkbUrgent?->masterDataSparepart ?? $data['detail']->detailRkbGeneral?->masterDataSparepart;
                                    @endphp
                                    {{ $sparepartName }} - {{ $sparepart->part_number ?? '-' }} -
                                    {{ $sparepart->merk ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $kategoriId = $data['detail']->detailRkbUrgent ? $data['detail']->detailRkbUrgent->id_kategori_sparepart_sparepart : $data['detail']->detailRkbGeneral->id_kategori_sparepart_sparepart;
                                    @endphp
                                    <select class="form-select sparepart-select" id="sparepart-{{ $data['detail']->id }}" name="sparepart[{{ $data['detail']->id }}]" data-kategori="{{ $kategoriId }}" disabled>
                                        <option value="" selected disabled>Pilih Sparepart</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    {{ $data['detail']->detailRkbUrgent->quantity_remainder ?? $data['detail']->detailRkbGeneral->quantity_remainder }}
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center" id="qty-{{ $data['detail']->id }}" name="qty[{{ $data['detail']->id }}]" type="number" value="0" min="0" max="{{ $data['detail']->detailRkbUrgent->quantity_remainder ?? $data['detail']->detailRkbGeneral->quantity_remainder }}" disabled>
                                </td>
                                <td class="text-center">
                                    {{ $data['detail']->detailRkbUrgent->satuan ?? $data['detail']->detailRkbGeneral->satuan }}
                                    <input name="satuan[{{ $data['detail']->id }}]" type="hidden" value="{{ $data['detail']->detailRkbUrgent->satuan ?? $data['detail']->detailRkbGeneral->satuan }}">
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center harga-input" id="harga-{{ $data['detail']->id }}" type="text" value="0" placeholder="" disabled>
                                    <input id="harga-hidden-{{ $data['detail']->id }}" name="harga[{{ $data['detail']->id }}]" type="hidden" value="0">
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center bg-secondary jumlah-harga" type="text" value="0" readonly>
                                </td>
                                <input name="alat_detail_id[{{ $data['detail']->id }}]" type="hidden" value="{{ $data['alat']->id }}">
                                <input name="link_rkb_detail_id[{{ $data['detail']->id }}]" type="hidden" value="{{ $data['detail']->id }}">
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="10">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada sparepart yang tersedia untuk pembuatan PO
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-primary">
                    <tr>
                        <th class="ps-4" style="text-align: left;" colspan="8">Jumlah</th>
                        <th class="currency-value" id="totalHarga">0</th>
                        <th class="currency-value" id="totalJumlahHarga">0</th>
                        {{-- <th></th> --}}
                    </tr>
                    <tr>
                        <th class="ps-4" style="text-align: left;" colspan="9">PPN 11%</th>
                        <th class="currency-value" id="ppn11">0</th>
                        {{-- <th></th> --}}
                    </tr>
                    <tr>
                        <th class="ps-4" style="text-align: left;" colspan="9">Grand Total</th>
                        <th class="currency-value" id="grandTotal">0</th>
                        {{-- <th></th> --}}
                    </tr>
                </tfoot>
            </table>
    </form>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Select2 initialization
            $('#supplier_main, .sparepart-select').select2({
                placeholder: 'Pilih Supplier',
                width: '100%',
                allowClear: true
            });

            // Set column widths for specific columns
            const $table = $('#table-data');
            const $headers = $table.find('thead th');
            const textsToCheck = ['Quantity PO', 'Satuan', 'Harga', 'Jumlah Harga'];

            $headers.each(function(index) {
                const headerText = $(this).text().trim();
                if (textsToCheck.includes(headerText)) {
                    $table.find('tbody tr').each(function() {
                        $(this).find('td').eq(index).css('width', '1%');
                    });
                }
            });

            // Format number function for Indonesian locale - Fixed to use comma consistently
            function formatRupiah(angka, prefix) {
                if (angka === null || angka === undefined || angka === '') return prefix ? prefix + ' 0' : '0';

                // Convert to number first to ensure proper handling of floating point values
                var num = typeof angka === 'string' ?
                    parseFloat(angka.replace(/\./g, '').replace(',', '.')) :
                    parseFloat(angka);

                // Fix decimal precision to 2 places
                num = !isNaN(num) ? Math.round(num * 100) / 100 : 0;

                // Convert to string and split into integer and decimal parts
                var stringNum = num.toString();
                var parts = stringNum.includes('.') ? stringNum.split('.') : [stringNum, '0'];

                // Format integer part with thousand separators
                var integerPart = parts[0];
                var formattedInteger = '';

                // Add thousand separators
                var sisa = integerPart.length % 3;
                formattedInteger = integerPart.substr(0, sisa);
                var ribuan = integerPart.substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    var separator = sisa ? '.' : '';
                    formattedInteger += separator + ribuan.join('.');
                }

                // Ensure 2 decimal places and use comma as decimal separator
                var decimalPart = parts[1].padEnd(2, '0');
                if (decimalPart.length > 2) {
                    decimalPart = decimalPart.substr(0, 2);
                }

                // Combine with comma as decimal separator
                var result = formattedInteger + ',' + decimalPart;

                // Add prefix if provided
                return prefix === undefined ? result : (result ? prefix + ' ' + result : prefix + ' 0');
            }

            // Parse Indonesian formatted number back to standard decimal - improved for precision
            function parseRupiah(rupiahString) {
                if (!rupiahString) return 0;

                // Remove prefix, currency symbols, and clean the string
                var cleanStr = rupiahString.replace(/^Rp\s+/, '').replace(/[^\d,\.]/g, '').trim();

                // Replace thousand separators and convert decimal separator
                var parsedValue = parseFloat(cleanStr.replace(/\./g, '').replace(',', '.')) || 0;

                // Ensure precision up to 2 decimal places
                return Math.round(parsedValue * 100) / 100;
            }

            function updateJumlahHarga(row) {
                const hargaInput = row.find('.harga-input');
                const hargaValue = parseRupiah(hargaInput.val());
                const quantity = parseInt(row.find('input[name^="qty"]').val()) || 0;

                // Calculate with proper decimal precision
                const jumlahHarga = hargaValue * quantity;

                // Update the hidden input with the exact parsed value (for server)
                const detailId = hargaInput.attr('id').replace('harga-', '');
                $('#harga-hidden-' + detailId).val(hargaValue);

                // Format with correct decimal separator (comma) for display
                row.find('.jumlah-harga').val(formatRupiah(jumlahHarga, 'Rp'));

                updateTotalFooter();
            }

            function updateTotalFooter() {
                let totalHarga = 0;
                let totalJumlahHarga = 0;

                // For each row, calculate precise values
                $('.harga-input').each(function() {
                    const row = $(this).closest('tr');
                    const harga = parseRupiah($(this).val());
                    const quantity = parseInt(row.find('input[name^="qty"]').val()) || 0;

                    // Calculate with precise decimal handling
                    const jumlahHarga = harga * quantity;

                    totalHarga += harga;
                    totalJumlahHarga += jumlahHarga;
                });

                // Calculate PPN with proper decimal precision
                const ppn11 = Math.round(totalJumlahHarga * 11) / 100; // 11% with rounding
                const grandTotal = totalJumlahHarga + ppn11;

                // Format all values with Indonesian locale
                $('#totalHarga').text(formatRupiah(totalHarga, 'Rp'));
                $('#totalJumlahHarga').text(formatRupiah(totalJumlahHarga, 'Rp'));
                $('#ppn11').text(formatRupiah(ppn11, 'Rp'));
                $('#grandTotal').text(formatRupiah(grandTotal, 'Rp'));
            }

            // Event handler for harga input - format on blur
            $(document).on('blur', '.harga-input', function() {
                const value = $(this).val();

                // Only format if there's a value
                if (value && value.trim() !== '') {
                    $(this).val(formatRupiah(parseRupiah(value), 'Rp'));
                } else {
                    $(this).val(''); // Keep empty if it was empty
                }

                updateJumlahHarga($(this).closest('tr'));
            });

            // Event handler for harga input - handle input formatting
            $(document).on('input', '.harga-input', function() {
                var value = $(this).val();

                // Remove non-numeric characters except comma
                value = value.replace(/[^\d,]/g, '');

                // Ensure only one comma exists
                var commaCount = (value.match(/,/g) || []).length;
                if (commaCount > 1) {
                    value = value.replace(/,/g, function(match, offset, string) {
                        return offset === string.indexOf(',') ? match : '';
                    });
                }

                // Limit to 2 decimal places after comma
                if (value.indexOf(',') !== -1) {
                    var parts = value.split(',');
                    if (parts[1] && parts[1].length > 2) {
                        parts[1] = parts[1].substring(0, 2);
                        value = parts.join(',');
                    }
                }

                $(this).val(value);
            });

            // Event handler for quantity input
            $(document).on('input', 'input[name^="qty"]', function() {
                const row = $(this).closest('tr');
                const max = parseInt($(this).attr('max'));
                let val = parseInt($(this).val()) || 0;

                if (val > max) {
                    alert('Quantity PO tidak boleh melebihi Quantity Sisa');
                    $(this).val(max);
                    val = max;
                }

                if (val < 0) {
                    $(this).val(0);
                    val = 0;
                }

                updateJumlahHarga(row);
            });

            // Add event handler for sparepart select change
            $(document).on('change', '.sparepart-select', function() {
                const row = $(this).closest('tr');
                const qtyInput = row.find('input[name^="qty"]');
                const hargaInput = row.find('.harga-input');
                const detailId = $(this).attr('id').replace('sparepart-', '');

                if ($(this).val()) {
                    // Enable fields when sparepart is selected
                    qtyInput.prop('disabled', false);
                    hargaInput.prop('disabled', false);
                } else {
                    // Disable and reset fields when no sparepart is selected
                    qtyInput.prop('disabled', true).val(0);
                    hargaInput.prop('disabled', true).val('');

                    // Update associated hidden fields
                    $('#harga-hidden-' + detailId).val(0);

                    updateJumlahHarga(row);
                }
            });

            // Initialize harga inputs to empty values when enabled
            $('.harga-input').each(function() {
                if (!$(this).prop('disabled')) {
                    $(this).val('');
                } else {
                    $(this).val(''); // Initialize as empty
                }
            });

            // Update totals on page load
            updateTotalFooter();

            // Add a focus handler to clear the field completely for easier editing
            $(document).on('focus', '.harga-input', function() {
                // If the value is 0 or Rp 0, clear it
                const value = $(this).val();
                if (value === '0' || value === 'Rp 0' || value === 'Rp 0,00' || value.match(/^Rp\s+0[,.]?0*$/)) {
                    $(this).val('');
                } else {
                    // Remove the Rp prefix and any leading/trailing spaces for non-zero values
                    $(this).val(value.replace(/^Rp\s+/, '').trim());
                }
            });

            // Make sure all hidden input values are updated before any form submission
            $('#detailSpbForm').on('submit', function(e) {
                // Update all hidden input values
                $('.harga-input').each(function() {
                    const row = $(this).closest('tr');
                    updateJumlahHarga(row);
                });
            });
        });

        $('.supplier-select-main').on('change', function() {
            let supplierId = $(this).val();

            if (!supplierId) {
                // Clear semua sparepart dropdown jika tidak ada supplier dipilih
                $('.sparepart-select').each(function() {
                    $(this).empty().append('<option value="" selected disabled>Pilih Sparepart</option>');
                    $(this).prop('disabled', true).trigger('change');
                });
                return;
            }

            // Enable sparepart dropdowns
            $('.sparepart-select').prop('disabled', false);

            // Show loading indicator
            $('body').append(`
<div class="loading-overlay">
<div class="spinner-border text-primary" role="status">
<span class="visually-hidden">Loading...</span>
</div>
</div>
`);

            // Fetch spareparts from API
            $.ajax({
                url: "{{ route('spb.detail.getSparepart', ':supplierId') }}".replace(':supplierId', supplierId),
                type: 'GET',
                success: function(response) {
                    // First, completely destroy any existing Select2 instances
                    $('.sparepart-select').select2('destroy');

                    // Organize spareparts by kategori
                    const sparepartsByKategori = {};

                    // Process the API response
                    if (response.master_data_spareparts && Array.isArray(response.master_data_spareparts)) {
                        response.master_data_spareparts.forEach(function(sparepart) {
                            if (!sparepart || !sparepart.id_kategori_sparepart) return;

                            const kategoriId = sparepart.id_kategori_sparepart;

                            // Create category array if it doesn't exist
                            if (!sparepartsByKategori[kategoriId]) {
                                sparepartsByKategori[kategoriId] = [];
                            }

                            // Add to this kategori if not already added
                            if (!sparepartsByKategori[kategoriId].some(s => s.id === sparepart.id)) {
                                sparepartsByKategori[kategoriId].push(sparepart);
                            }
                        });
                    }

                    // Process each dropdown
                    $('.sparepart-select').each(function() {
                        const $select = $(this);
                        const kategoriId = $select.data('kategori');

                        // Clear dropdown
                        $select.empty();

                        // Add placeholder option
                        $select.append('<option value="" selected>Pilih Sparepart</option>');

                        // Get spareparts for this kategori
                        const spareparts = sparepartsByKategori[kategoriId] || [];

                        // Add each sparepart option
                        spareparts.forEach(function(sparepart) {
                            $select.append(`<option value="${sparepart.id}">${sparepart.nama} - ${sparepart.merk || 'No Merk'} (${sparepart.part_number || 'No Part'})</option>`);
                        });
                    });

                    // Re-initialize Select2 on all dropdowns
                    $('.sparepart-select').select2({
                        width: '100%',
                        placeholder: 'Pilih Sparepart',
                        allowClear: true,
                        dropdownParent: $('body')
                    });

                    // Ensure changes are recognized
                    $('.sparepart-select').trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("Error loading spareparts:", error);
                    alert('Failed to load spareparts: ' + error);
                },
                complete: function() {
                    $('.loading-overlay').remove();
                }
            });
        });

        // Immediately initialize Select2 on page load using a function
        function initializeSelect2() {
            $('.sparepart-select').select2({
                width: '100%',
                placeholder: 'Pilih Sparepart',
                allowClear: true,
                dropdownParent: $('body')
            });

            $('#supplier_main').select2({
                width: '100%',
                placeholder: 'Pilih Supplier',
                allowClear: true
            });
        }

        // Call the function at document ready
        $(document).ready(function() {
            initializeSelect2();

            // Re-initialize Select2 after Ajax completes
            $(document).ajaxComplete(function() {
                setTimeout(function() {
                    initializeSelect2();
                }, 100);
            });
        });
    </script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
