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
        'showNavigationButtons' => true, // Default: true - shows date increment/decrement buttons
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
    'showNavigationButtons' => true,
])

<style>
    .date-nav-buttons {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        margin-left: 5px;
    }

    .date-nav-btn {
        display: block;
        width: 20px;
        height: 20px;
        /* Increased height from 15px */
        line-height: 20px;
        /* Increased line height to match */
        text-align: center;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        cursor: pointer;
        user-select: none;
        font-weight: bold;
        color: #495057;
        margin: 2px 0;
        /* Added margin between buttons */
    }

    .date-nav-btn:first-child {
        border-radius: 3px 3px 0 0;
        margin-bottom: 4px;
        /* Added extra space at bottom of first button */
    }

    .date-nav-btn:last-child {
        border-radius: 0 0 3px 3px;
        margin-top: 4px;
        /* Added extra space at top of second button */
    }

    .date-nav-btn:hover {
        background-color: #e9ecef;
    }

    .datepicker-container {
        display: flex;
        align-items: center;
    }
</style>

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

                // Add navigation buttons if enabled
                if ({{ $showNavigationButtons ? 'true' : 'false' }}) {
                    $datepickers.each(function() {
                        const $input = $(this);

                        // Only add if not already wrapped
                        if (!$input.parent().hasClass('datepicker-container')) {
                            // Wrap the input in a container
                            $input.wrap('<div class="datepicker-container"></div>');

                            // Add the navigation buttons
                            const $navButtons = $('<div class="date-nav-buttons">' +
                                '<div class="date-nav-btn date-increment" title="Increment date (Up Arrow)">▲</div>' +
                                '<div class="date-nav-btn date-decrement" title="Decrement date (Down Arrow)">▼</div>' +
                                '</div>');

                            $input.after($navButtons);

                            // Attach increment event
                            $navButtons.find('.date-increment').on('click', function() {
                                changeDate($input, 1);
                            });

                            // Attach decrement event
                            $navButtons.find('.date-decrement').on('click', function() {
                                changeDate($input, -1);
                            });
                        }
                    });
                }

                // Function to change date by days
                function changeDate($input, days) {
                    // Get current date from datepicker
                    let currentDate = $input.datepicker('getDate');

                    // If no date is set, use today
                    if (!currentDate) {
                        currentDate = new Date();
                    }

                    // Clone the date object to avoid modifying the original
                    let newDate = new Date(currentDate.getTime());

                    // Change date by specified days
                    newDate.setDate(newDate.getDate() + days);

                    // Update datepicker with new date
                    $input.datepicker('setDate', newDate);

                    // Trigger change event for event handlers
                    $input.change();

                    // If onSelect is defined, trigger it manually
                    const inst = $input.data('datepicker');
                    if (inst && inst.settings.onSelect) {
                        const dateText = $.datepicker.formatDate(inst.settings.dateFormat, newDate, inst.settings);
                        inst.settings.onSelect.call($input[0], dateText, inst);
                    }
                }

                // Add keyboard navigation with fallback for security popup scenarios
                $datepickers.on('keydown', function(e) {
                    const key = e.which || e.keyCode;

                    try {
                        // Only handle up and down arrow keys
                        if (key === 38 || key === 40) {
                            e.preventDefault(); // Prevent default arrow key behavior

                            // Use the change date function to avoid duplicate code
                            changeDate($(this), key === 38 ? 1 : -1);

                            return false;
                        }

                        // Handle escape key - may clear security popups
                        if (key === 27) {
                            // Let the browser handle Escape naturally
                            return true;
                        }
                    } catch (error) {
                        console.warn("Error handling datepicker keyboard navigation:", error);
                    }
                });
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
