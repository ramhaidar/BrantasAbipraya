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

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Modify datepicker initialization to use a more specific selector
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

                                // Use a more specific selector for filter datepickers only
                                $('.filter-popup .datepicker').each(function() {
                                    $(this).datepicker(filterDatePickerOptions);
                                });

                                // Prevent popup closing on datepicker click
                                $(document).on('click', '.ui-datepicker, .ui-datepicker *', function(e) {
                                    e.stopPropagation();
                                });

                                // Set regional settings
                                $.datepicker.setDefaults($.datepicker.regional['id']);
                            });

                            function showDatepicker(inputId) {
                                $('#' + inputId).datepicker('show');
                            }
                        </script>

                        {{-- Number Filter Section --}}
                    @elseif (isset($type) && in_array($type, ['number', 'number_of_days']))
                        @foreach (['exact' => 'Sama Dengan "="', 'gt' => 'Lebih Besar Dari Sama Dengan ">="', 'lt' => 'Lebih Kecil Dari Sama Dengan "<="'] as $key => $label)
                            <div class="mb-2">
                                <label class="form-label small">{{ $label }}</label>
                                <div class="input-group input-group-sm">
                                    <input class="form-control form-control-sm" id="{{ $paramName }}-{{ $key }}" type="number" value="{{ ${$key . 'Value'} }}" placeholder="{{ $key === 'exact' ? 'Masukkan nilai tepat' : ($key === 'gt' ? 'Lebih besar dari...' : 'Lebih kecil dari...') }}">
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
                    @endif

                    {{-- Search and Checkbox Section --}}
                    <div class="input-group input-group-sm mb-2">
                        <input class="form-control form-control-sm" id="search-{{ $paramName }}" type="text" placeholder="Cari {{ strtolower($title) }}..." onkeyup="filterCheckboxes('{{ $paramName }}', event)">
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

                    {{-- Apply Button --}}
                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('{{ $paramName }}')">
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
        // Add input event listeners for number and date inputs
        $('input[type="number"], .datepicker').on('input change', function() {
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
        $('input[type="number"], .datepicker').on('input change', function() {
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

        // Add immediate input event listeners for number and date inputs
        $('input[type="number"], .datepicker').on('input keyup', function(e) {
            const paramName = this.id.split('-')[0];
            const type = this.id.split('-')[1];
            clearRelatedFields(paramName, type);
            updateClearButtonVisibility(this.id);
        });

        // Add search input clear button functionality
        $('.filter-popup input[type="text"]').on('input keyup', function() {
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
                    applyFilter(paramName);
                }
            }
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
