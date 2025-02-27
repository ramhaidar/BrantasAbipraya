@php
    // Helper function to format values based on type
    $formatValue = function ($value, $type) {
        switch ($type) {
            case 'periode':
                return \Carbon\Carbon::parse($value)->isoFormat('MMMM Y');
            case 'tanggal':
                setlocale(LC_TIME, 'id_ID');
                return \Carbon\Carbon::parse($value)->translatedFormat('l, d F Y');
            case 'tipe':
                return ucwords($value);
            case 'durasi_rencana':
            case 'durasi_actual':
                return $value . ' Hari';
            case 'harga':
            case 'jumlah_harga':
            case 'ppn':
            case 'bruto':
                return number_format($value, 0, ',', '.');
            default:
                return $value;
        }
    };

    // Helper function to get filter values
    $getFilterValue = function ($paramName, $prefix) {
        $selectedValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, $prefix) === 0) : null;
        return $selectedValue ? substr($selectedValue, strlen($prefix)) : '';
    };

    // Helper function to format price value from standard to Indonesian format
    $formatPriceValue = function ($value) {
        if (empty($value)) {
            return '';
        }

        // Check if value contains decimal part
        if (strpos($value, '.') !== false) {
            $parts = explode('.', $value);
            $intPart = $parts[0];
            $decimalPart = isset($parts[1]) ? $parts[1] : '';

            // Format integer part with thousand separators
            $formattedInt = number_format((int) $intPart, 0, '', '.');

            // Format with Indonesian decimal separator (comma)
            return $formattedInt . ($decimalPart ? ',' . $decimalPart : '');
        } else {
            // No decimal part, just format with thousand separators
            return number_format((int) $value, 0, '', '.');
        }
    };

    // Set default variables
    $showHeader = true;
    if (isset($roles)) {
        $showHeader = in_array(auth()->user()->role, $roles);
    }
    $paramName = $paramName ?? '';

    // Format values
    $formattedValues = [];
    if (isset($uniqueValues[$paramName])) {
        $formattedValues = collect($uniqueValues[$paramName])->map(fn($value) => $formatValue($value, $paramName))->toArray();
    }

    // Get filter values
    $exactValue = $getFilterValue($paramName, 'exact:');
    $gtValue = $getFilterValue($paramName, 'gt:');
    $ltValue = $getFilterValue($paramName, 'lt:');

    // Format price values if type is price
    if (isset($type) && $type === 'price') {
        $exactValue = $formatPriceValue($exactValue);
        $gtValue = $formatPriceValue($gtValue);
        $ltValue = $formatPriceValue($ltValue);
    }
@endphp

@if ($showHeader)
    <th>
        {{-- Header Section --}}
        <div class="d-flex align-items-center gap-2 justify-content-center">
            {{ $title }}
            @if ($filter ?? true)
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('{{ $filterId }}-filter')">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    @if (request("selected_$paramName"))
                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('{{ $paramName }}')">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        @if ($filter ?? true)
            <div class="filter-popup" id="{{ $filterId }}-filter" style="display: none;">
                <div class="p-2">
                    {{-- Date Filter Section --}}
                    @if (isset($type) && $type === 'date')
                        <div id="date-filter-group">
                            @foreach (['exact' => 'Sama Dengan "="', 'gt' => 'Setelah atau Sama Dengan ">="', 'lt' => 'Sebelum atau Sama Dengan "<="'] as $key => $label)
                                <div class="mb-2">
                                    <label class="form-label small">{{ $label }}</label>
                                    <div class="input-group input-group-sm date-input-group">
                                        <input class="form-control datepicker" id="{{ $paramName }}-{{ $key }}" type="text" value="{{ ${$key . 'Value'} }}" placeholder="{{ $key === 'exact' ? 'Pilih tanggal' : ($key === 'gt' ? 'Setelah tanggal...' : 'Sebelum tanggal...') }}" autocomplete="off" onchange="clearRelatedFields('{{ $paramName }}', '{{ $key }}')">
                                        <span class="input-group-text" style="cursor: pointer;" onclick="showDatepicker('{{ $paramName }}-{{ $key }}')">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        @if (${$key . 'Value'})
                                            <span class="input-group-text clear-input" data-input-id="{{ $paramName }}-{{ $key }}" role="button">
                                                <i class="bi bi-x"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($key === 'exact' || $key === 'lt')
                                    <hr>
                                @endif
                            @endforeach
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const filterDatePickerOptions = {
                                    dateFormat: 'yy-mm-dd',
                                    changeMonth: true,
                                    changeYear: true,
                                    regional: 'id',
                                    autoOpen: false,
                                    showOn: false,
                                    beforeShow: function(input, inst) {
                                        inst.dpDiv.on('click', function(e) {
                                            e.stopPropagation();
                                        });
                                        return true;
                                    }
                                };

                                $('.filter-popup .datepicker').each(function() {
                                    $(this).datepicker(filterDatePickerOptions);
                                });

                                $(document).on('click', '.ui-datepicker, .ui-datepicker *', function(e) {
                                    e.stopPropagation();
                                });

                                $.datepicker.setDefaults($.datepicker.regional['id']);
                            });

                            function showDatepicker(inputId) {
                                $('#' + inputId).datepicker('show');
                            }
                        </script>

                        {{-- Price Filter Section --}}
                    @elseif (isset($type) && $type === 'price')
                        <div id="price-filter-group">
                            @foreach (['exact' => 'Sama Dengan "="', 'gt' => 'Lebih Besar Dari Sama Dengan ">="', 'lt' => 'Lebih Kecil Dari Sama Dengan "<="'] as $key => $label)
                                <div class="mb-2">
                                    <label class="form-label small">{{ $label }}</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input class="form-control form-control-sm price-input" id="{{ $paramName }}-{{ $key }}" type="text" value="{{ ${$key . 'Value'} }}" placeholder="{{ $key === 'exact' ? 'Masukkan nilai tepat' : ($key === 'gt' ? 'Lebih besar dari...' : 'Lebih kecil dari...') }}" onkeyup="formatPriceInput(this)" onchange="clearRelatedFields('{{ $paramName }}', '{{ $key }}')" autocomplete="off">
                                        @if (${$key . 'Value'})
                                            <span class="input-group-text clear-input" data-input-id="{{ $paramName }}-{{ $key }}" role="button">
                                                <i class="bi bi-x"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($key === 'exact' || $key === 'lt')
                                    <hr>
                                @endif
                            @endforeach
                        </div>

                        {{-- Number Filter Section --}}
                    @elseif (isset($type) && in_array($type, ['number', 'number_of_days']))
                        <div id="number-filter-group">
                            @foreach (['exact' => 'Sama Dengan "="', 'gt' => 'Lebih Besar Dari Sama Dengan ">="', 'lt' => 'Lebih Kecil Dari Sama Dengan "<="'] as $key => $label)
                                <div class="mb-2">
                                    <label class="form-label small">{{ $label }}</label>
                                    <div class="input-group input-group-sm">
                                        <input class="form-control form-control-sm" id="{{ $paramName }}-{{ $key }}" type="number" value="{{ ${$key . 'Value'} }}" placeholder="{{ $key === 'exact' ? 'Masukkan nilai tepat' : ($key === 'gt' ? 'Lebih besar dari...' : 'Lebih kecil dari...') }}" autocomplete="off">
                                        @if (${$key . 'Value'})
                                            <span class="input-group-text clear-input" data-input-id="{{ $paramName }}-{{ $key }}" role="button">
                                                <i class="bi bi-x"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if ($key === 'exact' || $key === 'lt')
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Search and Checkbox Section --}}
                    <div id="checkbox-filter-group">
                        <div class="input-group input-group-sm mb-2">
                            <input class="form-control form-control-sm" id="search-{{ $paramName }}" type="text" placeholder="Cari {{ strtolower($title) }}..." onkeyup="filterCheckboxes('{{ $paramName }}', event)" autocomplete="off">
                            <span class="input-group-text clear-input" data-input-id="search-{{ $paramName }}" role="button" style="display: none;">
                                <i class="bi bi-x"></i>
                            </span>
                        </div>

                        @if (isset($uniqueValues[(string) $paramName]) || isset($customUniqueValues))
                            <div class="checkbox-list text-start">
                                {{-- Empty/Null Checkbox --}}
                                <div class="form-check">
                                    <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer" {{ in_array('Empty/Null', explode(',', request("selected_$paramName", ''))) ? 'checked' : '' }}>
                                    <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">
                                        Empty/Null
                                    </label>
                                </div>

                                {{-- Values Checkboxes --}}
                                @foreach (isset($customUniqueValues) ? $customUniqueValues : $uniqueValues[(string) $paramName] as $key => $value)
                                    <div class="form-check">
                                        <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="{{ $value }}" style="cursor: pointer" {{ in_array((string) $value, explode(',', request("selected_$paramName", ''))) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">
                                            {{ isset($customUniqueValues) ? $value : $formattedValues[$key] ?? $value }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Apply Button --}}
                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('{{ $paramName }}', this)">
                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                    </button>
                </div>
            </div>
        @endif
    </th>
@endif

<script>
    // Add this new function
    function clearInput(inputId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        $('#' + inputId).val('');
        $('#' + inputId).trigger('change');
        updateClearButtonVisibility(inputId);
    }

    // Add this new function
    function updateClearButtonVisibility(inputId) {
        const input = $('#' + inputId);
        const clearButton = input.siblings('.clear-input');
        clearButton.toggle(input.val() !== '');
    }

    // Price input formatter
    function formatPriceInput(element) {
        let value = element.value;

        // Remove all non-numeric characters except for comma
        value = value.replace(/[^\d,]/g, '');

        // Ensure only one comma exists
        let commaIndex = value.indexOf(',');
        if (commaIndex !== -1) {
            let beforeComma = value.substring(0, commaIndex);
            let afterComma = value.substring(commaIndex + 1);

            // Remove any additional commas from afterComma
            afterComma = afterComma.replace(/,/g, '');

            // Limit to 2 decimal places
            if (afterComma.length > 2) {
                afterComma = afterComma.substring(0, 2);
            }

            value = beforeComma + ',' + afterComma;
        }

        // Format numbers with thousand separators
        if (commaIndex === -1) {
            // If no comma yet, format the whole number
            value = addThousandSeparator(value);
        } else {
            // If there's a comma, format only the part before comma
            let beforeComma = value.substring(0, commaIndex);
            let afterComma = value.substring(commaIndex);
            value = addThousandSeparator(beforeComma) + afterComma;
        }

        element.value = value;
    }

    // Add thousand separators
    function addThousandSeparator(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Parse price value to a number
    function parsePriceValue(value) {
        if (!value) return '';

        // First remove all thousand separators (dots in Indonesian format)
        let cleanValue = value.replace(/\./g, '');

        // Then replace comma with dot for standard decimal format
        // But only replace the first comma (the decimal separator in Indonesian)
        const commaIndex = cleanValue.indexOf(',');
        if (commaIndex !== -1) {
            cleanValue = cleanValue.substring(0, commaIndex) + '.' + cleanValue.substring(commaIndex + 1);
        }

        return cleanValue;
    }

    // Update clearRelatedFields function
    function clearRelatedFields(paramName, type) {
        if (type === 'exact') {
            // If exact input has value, clear gt and lt inputs
            if ($('#' + paramName + '-exact').val()) {
                $('#' + paramName + '-gt').val('');
                $('#' + paramName + '-lt').val('');
                updateClearButtonVisibility(paramName + '-gt');
                updateClearButtonVisibility(paramName + '-lt');
            }
        } else {
            // If either gt or lt inputs have value, clear exact input
            if ($('#' + paramName + '-gt').val() || $('#' + paramName + '-lt').val()) {
                $('#' + paramName + '-exact').val('');
                updateClearButtonVisibility(paramName + '-exact');
            }
        }
    }

    // Add input event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Add input event listeners for number, date and price inputs
        $('input[type="number"], .datepicker, .price-input').on('input change', function() {
            updateClearButtonVisibility(this.id);
        });

        // Clear input handler
        $(document).on('click', '.clear-input', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const inputId = $(this).data('input-id');
            const input = $('#' + inputId);
            input.val('').trigger('change');
            $(this).hide();

            // Handle related fields
            const paramName = inputId.split('-')[0];
            const type = inputId.split('-')[1];
            clearRelatedFields(paramName, type);
        });

        // Show/hide clear button on input
        $('input[type="number"], .datepicker, .price-input').on('input change', function() {
            const clearBtn = $(this).siblings('.clear-input');
            if (this.value) {
                if (clearBtn.length === 0) {
                    const span = $('<span class="input-group-text clear-input" role="button" data-input-id="' + this.id + '"><i class="bi bi-x"></i></span>');
                    $(this).closest('.input-group').append(span);
                } else {
                    clearBtn.show();
                }
            } else {
                clearBtn.hide();
            }
        });

        // Add search input clear button functionality - be more specific with selector
        $('.filter-popup input[id^="search-"]').on('input keyup', function() {
            const clearBtn = $(this).siblings('.clear-input');
            clearBtn.toggle(this.value !== '');
        });

        // Clear search input handler
        $(document).on('click', '.clear-input[data-input-id^="search-"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const inputId = $(this).data('input-id');
            const input = $('#' + inputId);
            input.val('').trigger('keyup'); // Trigger keyup to update the filter
            $(this).hide();
        });

        // Add keyboard event listener for Enter key
        $(document).on('keydown', function(e) {
            if (e.key === 'Enter') {
                const visiblePopup = $('.filter-popup:visible');
                if (visiblePopup.length) {
                    e.preventDefault();
                    const paramName = visiblePopup.attr('id').replace('-filter', '').replace(/-/g, '_');
                    const button = visiblePopup.find('button[type="button"]');
                    applyFilter(paramName, button[0]);
                }
            }
        });

        // Initialize price inputs with proper formatting
        $('.price-input').each(function() {
            if (this.value) {
                formatPriceInput(this);
            }
        });

        // Prevent filtering on non-search inputs
        $('.filter-popup .price-input, .filter-popup .datepicker').on('keyup', function(e) {
            e.stopPropagation(); // Prevent event bubbling
        });
    });
</script>

<style>
    .clear-input {
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        line-height: 1;
    }

    .clear-input:hover {
        background-color: #e9ecef;
    }

    .clear-input i {
        font-size: 0.875rem;
    }
</style>
