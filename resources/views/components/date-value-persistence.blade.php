{{-- 
    Date Value Persistence Component
    Saves datepicker values to localStorage and restores them on future visits
    
    Behavior:
    1. If a saved date exists in localStorage, that will be used (subject to allow-override)
    2. If no saved date exists and input has no value, today's date will be set as default
    
    Usage:
    1. Add the 'datepicker' class to any date input field you want to persist
    2. Add data attributes for more control:
       - data-persist-key="custom-key" - Specify a unique key for this input
       - data-allow-override="true" - Allow localStorage values to override default values
       - data-context="form-name" - Specify a context for multiple datepickers
       - data-page-specific="true" - Make persistence specific to the current page (default: true)
    
    @include('components.date-value-persistence', [
        'pageContext' => 'custom-page-name'  // Optional: Override automatic page detection
    ])
--}}

@props(['pageContext' => null])

<script>
    $(document).ready(function() {
        // Helper function to get today's date in YYYY-MM-DD format
        function getTodayFormatted() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Get current page context for namespacing localStorage keys
        function getPageContext() {
            @if ($pageContext)
                return '{{ $pageContext }}';
            @else
                // Auto-detect from current URL/route
                let path = window.location.pathname.replace(/^\/|\/$/g, '');
                // Remove numeric segments (likely IDs) from path
                path = path.split('/').filter(segment => !/^\d+$/.test(segment)).join('-');
                // Clean up the path for storage
                return path || 'home';
            @endif
        }

        // Handle saving date values to localStorage and restoring them
        function setupDatePersistence() {
            // Get current page context
            const pageContext = getPageContext();

            // Find all datepicker inputs
            const $datepickers = $('.datepicker');
            if ($datepickers.length === 0) return;

            // Track datepicker IDs/names to handle duplicates
            const seenIds = {};

            // Process each datepicker
            $datepickers.each(function(index) {
                const $input = $(this);
                const isPageSpecific = $input.data('page-specific') !== false;

                // Determine element identifier
                let identifier = $input.data('persist-key') ||
                    $input.attr('id') ||
                    $input.attr('name');

                // Handle duplicate IDs on the same page by adding index
                if (identifier) {
                    if (seenIds[identifier]) {
                        identifier = `${identifier}-${index}`;
                    } else {
                        seenIds[identifier] = true;
                    }
                } else {
                    identifier = `datepicker-${index}`;
                }

                // Get user-defined context if any
                const userContext = $input.data('context') || '';

                // Build the storage key:
                // Format: datepicker-[page]-[user-context]-[identifier]
                let storageKey = 'datepicker';
                if (isPageSpecific) {
                    storageKey += `-${pageContext}`;
                }
                if (userContext) {
                    storageKey += `-${userContext}`;
                }
                storageKey += `-${identifier}`;

                // Mark this key on the element for reference
                $input.attr('data-storage-key', storageKey);

                // Check if this input allows overriding default values
                const allowOverride = $input.data('allow-override') === true ||
                    $input.attr('data-allow-override') === 'true';

                // See if we have a saved value
                let savedValue;
                try {
                    savedValue = localStorage.getItem(storageKey);
                } catch (e) {
                    console.log('LocalStorage access failed');
                }

                // Apply date value with the following priority:
                // 1. Saved value from localStorage (if allowed)
                // 2. Existing value on the input (if any)
                // 3. Today's date as default
                if (savedValue && isValidDate(savedValue) && (allowOverride || !$input.val())) {
                    // Use saved date from localStorage
                    $input.val(savedValue);

                    // If datepicker is initialized, update it
                    if ($.datepicker && $input.hasClass('hasDatepicker')) {
                        try {
                            $input.datepicker('setDate', savedValue);
                        } catch (e) {
                            console.log('Datepicker update failed, will use value attribute');
                        }
                    }
                } else if (!$input.val()) {
                    // No saved date and no existing value, use today's date
                    const today = getTodayFormatted();
                    $input.val(today);

                    // If datepicker is initialized, update it
                    if ($.datepicker && $input.hasClass('hasDatepicker')) {
                        try {
                            $input.datepicker('setDate', today);
                        } catch (e) {
                            console.log('Datepicker update failed, will use value attribute');
                        }
                    }
                }

                // Handle change and blur events to save value to localStorage
                $input.on('change blur', function() {
                    const value = $input.val();
                    if (value && isValidDate(value)) {
                        try {
                            localStorage.setItem(storageKey, value);
                        } catch (e) {
                            console.log('LocalStorage save failed');
                        }
                    }
                });
            });

            // For new datepickers that might be added dynamically (e.g., in modals)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        const $newDatepickers = $(mutation.addedNodes).find('.datepicker').add(
                            $(mutation.addedNodes).filter('.datepicker')
                        );

                        if ($newDatepickers.length) {
                            // Process each newly added datepicker
                            $newDatepickers.each(function() {
                                const $input = $(this);
                                // Skip if already processed
                                if ($input.data('persistence-initialized')) return;

                                $input.data('persistence-initialized', true);

                                // Same logic as above for new elements
                                const isPageSpecific = $input.data('page-specific') !== false;
                                const identifier = $input.data('persist-key') ||
                                    $input.attr('id') ||
                                    $input.attr('name') ||
                                    `datepicker-dynamic-${Date.now()}`;

                                const userContext = $input.data('context') || '';

                                // Build storage key with the same pattern
                                let storageKey = 'datepicker';
                                if (isPageSpecific) {
                                    storageKey += `-${pageContext}`;
                                }
                                if (userContext) {
                                    storageKey += `-${userContext}`;
                                }
                                storageKey += `-${identifier}`;

                                $input.attr('data-storage-key', storageKey);

                                const allowOverride = $input.data('allow-override') === true ||
                                    $input.attr('data-allow-override') === 'true';

                                let savedValue;
                                try {
                                    savedValue = localStorage.getItem(storageKey);
                                } catch (e) {}

                                // Apply date values with same priority
                                if (savedValue && isValidDate(savedValue) && (allowOverride || !$input.val())) {
                                    $input.val(savedValue);
                                    if ($.datepicker && $input.hasClass('hasDatepicker')) {
                                        try {
                                            $input.datepicker('setDate', savedValue);
                                        } catch (e) {}
                                    }
                                } else if (!$input.val()) {
                                    const today = getTodayFormatted();
                                    $input.val(today);
                                    if ($.datepicker && $input.hasClass('hasDatepicker')) {
                                        try {
                                            $input.datepicker('setDate', today);
                                        } catch (e) {}
                                    }
                                }

                                // Set up persistence events
                                $input.on('change blur', function() {
                                    const value = $input.val();
                                    if (value && isValidDate(value)) {
                                        try {
                                            localStorage.setItem(storageKey, value);
                                        } catch (e) {}
                                    }
                                });
                            });
                        }
                    }
                });
            });

            // Start observing document for dynamically added datepickers
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        // Helper function to validate date strings
        function isValidDate(dateString) {
            // Basic validation for common date formats
            const regex = /^\d{4}-\d{2}-\d{2}$|^\d{2}\/\d{2}\/\d{4}$|^\d{2}-\d{2}-\d{4}$/;

            if (!regex.test(dateString)) return false;

            // Convert to Date object to verify it's a valid date
            let date;
            if (dateString.includes('-')) {
                const parts = dateString.split('-');
                // Handle both YYYY-MM-DD and DD-MM-YYYY
                if (parts[0].length === 4) {
                    date = new Date(parts[0], parts[1] - 1, parts[2]); // YYYY-MM-DD
                } else {
                    date = new Date(parts[2], parts[1] - 1, parts[0]); // DD-MM-YYYY
                }
            } else { // DD/MM/YYYY
                const parts = dateString.split('/');
                date = new Date(parts[2], parts[1] - 1, parts[0]);
            }

            return date instanceof Date && !isNaN(date);
        }

        // Initialize date persistence
        setupDatePersistence();

        // Make utility functions globally available
        window.datePersistence = {
            clearSavedDate: function(selector) {
                const $input = $(selector);
                if ($input.length) {
                    const storageKey = $input.attr('data-storage-key');
                    if (storageKey) {
                        try {
                            localStorage.removeItem(storageKey);
                        } catch (e) {}
                    }
                }
            },
            clearAllDates: function(context = null) {
                const pageContext = getPageContext();
                const prefix = context ?
                    `datepicker-${pageContext}-${context}` :
                    `datepicker-${pageContext}`;

                // Find all localStorage items with the prefix
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith(prefix)) {
                        try {
                            localStorage.removeItem(key);
                        } catch (e) {}
                    }
                });
            }
        };
    });
</script>
