{{-- 
    Reusable Datepicker Component
    
    Usage:
    @include('components.datepicker', [
        'selector' => '#tanggal', // Can be a single selector or comma-separated list: '#date1, #date2'
        'persistKey' => 'my-specific-key', // Optional: specify a custom persistence key
        'format' => 'yy-mm-dd', // Default: 'yy-mm-dd'
        'allowPersistence' => true, // Default: true
        'changeMonth' => true, // Default: true
        'changeYear' => true, // Default: true
        'showButtonPanel' => true, // Default: true
        'autoResetSelector' => '#resetButton', // Optional: automatically bind to reset button
        'formSelector' => '#myForm', // Optional: bind to specific form reset event
    ])
--}}

@props([
    'selector' => '.datepicker',
    'persistKey' => null,
    'format' => 'yy-mm-dd',
    'allowPersistence' => true,
    'changeMonth' => true,
    'changeYear' => true,
    'showButtonPanel' => true,
    'autoResetSelector' => null,
    'formSelector' => null,
])

<script>
    $(document).ready(function() {
        // Setup Indonesian localization for datepicker if not already done
        if (!$.datepicker.regional['id']) {
            $.datepicker.regional['id'] = {
                closeText: 'Tutup',
                prevText: 'Sebelumnya',
                nextText: 'Selanjutnya',
                currentText: 'Hari Ini',
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                dayNames: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                dayNamesShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                dayNamesMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                weekHeader: 'Mg',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['id']);
        }

        // Parse selectors into array to support multiple datepickers
        const selectors = '{{ $selector }}'.split(',').map(s => s.trim());

        // Function to initialize datepickers
        function initDatepickers() {
            // Configure datepicker options
            var options = {
                dateFormat: '{{ $format }}',
                changeMonth: {{ $changeMonth ? 'true' : 'false' }},
                changeYear: {{ $changeYear ? 'true' : 'false' }},
                showButtonPanel: {{ $showButtonPanel ? 'true' : 'false' }},
                onSelect: function(dateText) {
                    $(this).change();
                }
            };

            // Process each selector (might be multiple datepickers)
            selectors.forEach(function(selector) {
                // Find matching elements
                const $datepickers = $(selector);

                if ($datepickers.length === 0) return;

                // Add persistence attributes if needed
                if ({{ $allowPersistence ? 'true' : 'false' }}) {
                    $datepickers.each(function(index) {
                        const $this = $(this);

                        // Generate persist key with index if multiple elements match the selector
                        let keyBase = '{{ $persistKey }}' || $this.attr('id') || $this.attr('name');
                        if (!keyBase) {
                            keyBase = 'datepicker-' + Math.random().toString(36).substring(2, 9);
                        }

                        // Add index suffix for multiple matches
                        const finalKey = $datepickers.length > 1 ? `${keyBase}-${index}` : keyBase;

                        // Only set persist key if not already set
                        if (!$this.attr('data-persist-key')) {
                            $this.attr('data-persist-key', finalKey);
                        }

                        // Allow persistence to override default values
                        $this.attr('data-allow-override', 'true');

                        // Store form parent for reset handling
                        $this.data('form-parent', $this.closest('form').attr('id'));
                    });
                }

                // Initialize datepickers with options
                $datepickers.datepicker(options);
            });

            // Fix Today button if not already fixed
            if (!$.datepicker._gotoToday_fixed) {
                // Save original function if not already patched
                $.datepicker._gotoToday_original = $.datepicker._gotoToday;
                $.datepicker._gotoToday_fixed = true;

                // Override with improved implementation
                $.datepicker._gotoToday = function(id) {
                    var target = $(id);
                    var inst = this._getInst(target[0]);

                    // Get today's date
                    var date = new Date();

                    // Format today's date according to the dateFormat option
                    var formattedDate = $.datepicker.formatDate(
                        this._get(inst, 'dateFormat'),
                        date,
                        this._getFormatConfig(inst)
                    );

                    // Set the formatted date in the input field
                    $(target).val(formattedDate);

                    // Update the datepicker's internal state
                    inst.selectedDay = date.getDate();
                    inst.drawMonth = inst.selectedMonth = date.getMonth();
                    inst.drawYear = inst.selectedYear = date.getFullYear();

                    // Update the datepicker display but don't close it
                    this._notifyChange(inst);
                    this._adjustDate(target);

                    // Manually trigger the onSelect and change events
                    if (this._get(inst, 'onSelect')) {
                        this._get(inst, 'onSelect').apply(target[0], [formattedDate, inst]);
                    }
                    $(target).change(); // Trigger change to ensure persistence works

                    // Make sure the input has the focus to keep the datepicker open
                    target.focus();

                    return false; // Prevent default behavior
                };
            }
        }

        // If using persistence, wait for it to run first
        if ({{ $allowPersistence ? 'true' : 'false' }}) {
            setTimeout(initDatepickers, 100);
        } else {
            initDatepickers();
        }

        // Function to reset datepickers to today's date
        function resetDatepickers(selectorList) {
            selectorList = selectorList || selectors;

            // Handle both array and string inputs
            if (typeof selectorList === 'string') {
                selectorList = [selectorList];
            }

            // Process each selector
            selectorList.forEach(function(selector) {
                const $inputs = $(selector);
                if ($inputs.length) {
                    const today = new Date();
                    $inputs.each(function() {
                        $(this).datepicker('setDate', today).change();
                    });
                }
            });
        }

        // Global helper function to set datepicker(s) to today's date
        window.setDatepickerToday = function(selector) {
            if (selector) {
                resetDatepickers(selector);
            } else {
                resetDatepickers();
            }
        };

        // Handle form reset events - reset all datepickers in the form to today's date
        $(document).on('reset', 'form', function(e) {
            // Find all datepickers in this form
            const $formDatepickers = $(this).find('.datepicker');
            if ($formDatepickers.length) {
                // Use a small delay to let the form reset first
                setTimeout(function() {
                    $formDatepickers.each(function() {
                        $(this).datepicker('setDate', new Date()).change();
                    });
                }, 10);
            }
        });

        // Bind to specified reset button if provided
        @if ($autoResetSelector)
            $('{{ $autoResetSelector }}').on('click', function() {
                // Find datepickers in related form or within selectors
                let $targetDatepickers;

                // If attached to a form, use that form's datepickers
                const $form = $(this).closest('form');
                if ($form.length) {
                    $targetDatepickers = $form.find('.datepicker');
                } else {
                    // Otherwise use the specified selectors
                    $targetDatepickers = $(selectors.join(','));
                }

                if ($targetDatepickers.length) {
                    $targetDatepickers.each(function() {
                        $(this).datepicker('setDate', new Date()).change();
                    });
                }
            });
        @endif

        // Bind to specific form if provided
        @if ($formSelector)
            $('{{ $formSelector }}').on('reset', function(e) {
                // Get all datepickers in this specific form
                const $formDatepickers = $(this).find('.datepicker');
                if ($formDatepickers.length) {
                    setTimeout(function() {
                        $formDatepickers.each(function() {
                            $(this).datepicker('setDate', new Date()).change();
                        });
                    }, 10);
                }
            });
        @endif
    });
</script>
