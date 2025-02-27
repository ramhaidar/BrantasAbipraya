<script>
    /**
     * Debounce function to limit how often a function can be called
     * This is useful for performance optimization, especially with search inputs
     * @param {Function} func - The function to be debounced
     * @param {number} wait - Time to wait in milliseconds before executing the function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Add flag to track if a filter operation is in progress
    let isFilterProcessing = false;

    /**
     * Decodes a parameter value from base64 if possible
     * Falls back to original value if decoding fails
     * @param {string} value - The value to decode
     * @returns {string} - Decoded or original value
     */
    function decodeParameterValue(value) {
        if (!value) return '';

        try {
            // First attempt to decode from base64
            return atob(value);
        } catch (e) {
            // If decoding fails, return the original value
            return value;
        }
    }

    /**
     * Gets an array of selected values from a parameter
     * Handles both base64 encoded and plain comma-separated values
     * @param {string} paramValue - The parameter value to process
     * @returns {Array} - Array of selected values
     */
    function getSelectedValues(paramValue) {
        if (!paramValue) return [];

        try {
            // Use a special delimiter that's unlikely to appear in values
            return atob(paramValue).split('||').map(value => value.trim());
        } catch (e) {
            console.error('Error decoding parameter value:', e);
            return [];
        }
    }

    /**
     * Removes non-UTF8 characters from a string
     * @param {string} value - The string to sanitize
     * @returns {string} - Sanitized string
     */
    function sanitizeValue(value) {
        return value.replace(/[^\x00-\x7F]/g, "");
    }

    /**
     * Handles the showing/hiding of filter popups
     * Also manages the position and state of filter checkboxes
     * @param {string} id - The ID of the filter popup to toggle
     */
    function toggleFilter(id) {
        // Hide all other filter popups
        $('.filter-popup').not(`#${id}`).hide();
        const popup = $(`#${id}`);
        const button = $(`button[onclick="toggleFilter('${id}')"]`);

        if (popup.is(':hidden')) {
            // Store original width for positioning calculations
            if (!popup.data('originalWidth')) {
                popup.css('width', '');
                popup.data('originalWidth', popup.outerWidth());
            }

            // Position popup
            positionPopup(popup, button);

            // Extract filter type from ID and handle URL parameters
            const type = id.replace('-filter', '').replaceAll('-', '_');
            const urlParams = new URLSearchParams(window.location.search);
            const encodedSelected = urlParams.get(`selected_${type}`);

            // Sort items initially when popup opens
            const container = popup.find('.checkbox-list');
            const items = [];

            // Collect all items with their data
            container.children('.form-check').each(function() {
                const item = $(this);
                const label = item.find('label').text();
                const labelLower = label.toLowerCase();
                const priority = getItemPriority(label);
                items.push({
                    item,
                    label,
                    labelLower,
                    priority
                });
            });

            // Sort items with priority
            items.sort((a, b) => {
                if (a.priority !== b.priority) {
                    return a.priority - b.priority;
                }
                return a.labelLower.localeCompare(b.labelLower);
            });

            // Clear and re-append sorted items
            container.empty();
            items.forEach(({
                item
            }) => container.append(item));

            // Now restore checked states from URL
            if (encodedSelected) {
                try {
                    const selectedValues = atob(encodedSelected).split('||').map(value => value.trim());
                    selectedValues.forEach(value => {
                        const checkbox = Array.from(document.querySelectorAll(`.${type}-checkbox`))
                            .find(cb => cb.value === value);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                    // After restoring checked states, run filterCheckboxes to ensure proper ordering
                    filterCheckboxes(type);
                } catch (e) {
                    console.error('Error restoring checkbox states:', e);
                }
            }

            // Show popup and focus search input
            popup.show();
            popup.find('input[type="text"]').focus();
        } else {
            popup.hide();
        }
    }

    /**
     * Calculates and sets the position of a filter popup
     * Ensures the popup is visible and properly aligned on screen
     * @param {jQuery} popup - The popup element
     * @param {jQuery} button - The button that triggered the popup
     */
    function positionPopup(popup, button) {
        // Get dimensions and positions
        const buttonRect = button[0].getBoundingClientRect();
        const windowWidth = $(window).width();
        const windowHeight = $(window).height();
        const popupHeight = popup.outerHeight();
        const safetyMargin = 40; // Margin from window edges
        const verticalGap = 10; // Gap between button and popup

        const originalWidth = popup.data('originalWidth');

        // Calculate maximum width based on window size
        const maxWidth = windowWidth - (safetyMargin * 2);
        popup.css({
            'width': Math.min(originalWidth, maxWidth) + 'px',
            'max-width': `${maxWidth}px`
        });

        // Ensure content breaks properly
        popup.find('.checkbox-list').css({
            'word-break': 'break-word',
            'overflow-x': 'hidden'
        });

        // Calculate vertical position
        let top = buttonRect.bottom + verticalGap;

        // If popup would go below window bottom, show it above the button
        if (top + popupHeight > windowHeight - safetyMargin) {
            top = buttonRect.top - popupHeight - verticalGap;
        }

        top = Math.max(safetyMargin, top);

        // Calculate horizontal position
        let left = buttonRect.left;

        // Adjust position if popup would go off screen
        if (left + originalWidth > windowWidth - safetyMargin) {
            left = windowWidth - originalWidth - safetyMargin;
            popup.addClass('right-aligned');
        } else {
            popup.removeClass('right-aligned');
        }

        left = Math.max(safetyMargin, left);

        // Apply position with smooth transition
        popup.css({
            top: `${top}px`,
            left: `${left}px`,
            transition: 'left 0.2s, top 0.2s'
        });
    }

    // Reposition popups when window is resized
    $(window).on('resize', function() {
        $('.filter-popup:visible').each(function() {
            const id = $(this).attr('id');
            const button = $(`button[onclick="toggleFilter('${id}')"]`);
            positionPopup($(this), button);
        });
    });

    /**
     * Filters checkboxes based on search input
     * Shows/hides checkboxes based on whether they match the search text
     * @param {string} type - The type of filter
     * @param {Event} searchEvent - The search input event
     */
    function filterCheckboxes(type, searchEvent) {
        const selector = `.${type}-checkbox`;
        const container = $(selector).first().closest('.checkbox-list');
        const searchText = searchEvent ? $(searchEvent.target).val().toLowerCase() : '';

        const checkedItems = [];
        const uncheckedItems = [];

        // Custom function to determine item priority
        const getItemPriority = (label) => {
            const trimmedLabel = label.trim();
            if (trimmedLabel === 'Empty/Null') return 1;
            if (trimmedLabel === '-') return 2;
            return 3;
        };

        container.children('.form-check').each(function() {
            const item = $(this);
            const label = item.find('label').text();
            const labelLower = label.toLowerCase();
            const isChecked = item.find('input[type="checkbox"]').prop('checked');
            const matchesSearch = !searchText || labelLower.includes(searchText);
            const priority = getItemPriority(label);

            // Store item with all necessary information
            const itemData = {
                item,
                label,
                labelLower,
                priority,
                visible: matchesSearch
            };

            if (isChecked) {
                checkedItems.push(itemData);
            } else {
                uncheckedItems.push(itemData);
            }
        });

        // Custom sort function that handles priorities
        const sortItems = (a, b) => {
            // First sort by priority
            if (a.priority !== b.priority) {
                return a.priority - b.priority;
            }
            // Then by alphabetical order for same priority items
            return a.labelLower.localeCompare(b.labelLower);
        };

        // Sort both arrays
        checkedItems.sort(sortItems);
        uncheckedItems.sort(sortItems);

        // Clear container before re-adding items
        container.empty();

        // Add checked items first (they stay at top)
        checkedItems.forEach(({
            item,
            visible
        }) => {
            item.toggle(visible);
            container.append(item);
        });

        // Add unchecked items
        uncheckedItems.forEach(({
            item,
            visible
        }) => {
            item.toggle(visible);
            container.append(item);
        });
    }

    /**
     * Applies the selected filters and updates the URL
     * @param {string} type - The type of filter to apply
     * @param {HTMLElement} buttonElement - The button that was clicked (optional)
     */
    function applyFilter(type, buttonElement) {
        // Prevent multiple submissions
        if (isFilterProcessing) {
            return;
        }

        // Set flag to indicate processing is in progress
        isFilterProcessing = true;

        // Get the filter popup element
        const popupId = type.replace('_', '-') + '-filter';
        const popup = $(`#${popupId}`);

        // If button element is provided, show spinner and disable it
        const button = buttonElement ? $(buttonElement) : $(`.filter-popup#${popupId} button[type="button"]`);
        const originalButtonHtml = button.html();

        // Disable button and show spinner - this should remain visible
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        // Disable all inputs in the popup during processing
        popup.find('input').prop('disabled', true);

        // Process filter values
        const selector = `.${type}-checkbox:checked`;
        const selected = $(selector).map(function() {
            return $(this).val();
        }).get();

        const urlParams = new URLSearchParams(window.location.search);

        // Reset to first page when filter changes
        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        // Check for price or numeric inputs
        const exactInput = document.getElementById(`${type}-exact`);
        const gtInput = document.getElementById(`${type}-gt`);
        const ltInput = document.getElementById(`${type}-lt`);

        if (exactInput || gtInput || ltInput) {
            let values = [...selected];
            const isPriceFilter = exactInput && exactInput.classList.contains('price-input');

            // Handle exact value
            if (exactInput && exactInput.value.trim()) {
                const value = isPriceFilter ? parsePriceValue(exactInput.value) : exactInput.value.trim();
                values.push(`exact:${value}`);
            } else {
                // Handle greater than value
                if (gtInput && gtInput.value.trim()) {
                    const value = isPriceFilter ? parsePriceValue(gtInput.value) : gtInput.value.trim();
                    values.push(`gt:${value}`);
                }

                // Handle less than value
                if (ltInput && ltInput.value.trim()) {
                    const value = isPriceFilter ? parsePriceValue(ltInput.value) : ltInput.value.trim();
                    values.push(`lt:${value}`);
                }
            }

            // Update URL parameters
            if (values.length > 0) {
                const encodedValue = btoa(values.join('||'));
                urlParams.set(`selected_${type}`, encodedValue);
            } else {
                urlParams.delete(`selected_${type}`);
            }
        } else {
            // Handle checkbox values
            if (selected.length > 0) {
                // Use special delimiter instead of comma
                const encodedValue = btoa(selected.join('||'));
                urlParams.set(`selected_${type}`, encodedValue);
            } else {
                urlParams.delete(`selected_${type}`);
            }
        }

        // Create the new URL
        const newUrl = `${window.location.pathname}?${urlParams.toString()}`;

        // IMPORTANT: Do NOT hide the popup here - the browser will handle this during navigation
        // We want users to see the spinner animation

        // Use a small delay to allow the spinner to appear before redirect
        setTimeout(() => {
            window.location.href = newUrl;
        }, 0);

        // If for some reason the redirect doesn't happen, reset the processing flag after timeout
        setTimeout(() => {
            isFilterProcessing = false;
            button.prop('disabled', false).html(originalButtonHtml);
            popup.find('input').prop('disabled', false);
        }, 5000);
    }

    /**
     * Clears a specific filter
     * @param {string} type - The type of filter to clear
     */
    function clearFilter(type) {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        // Special handling for price filter
        if (type === 'price') {
            const priceMin = urlParams.get('price_min');
            const priceMax = urlParams.get('price_max');
            const priceExact = urlParams.get('price_exact');

            if (priceMin) urlParams.delete('price_min');
            if (priceMax) urlParams.delete('price_max');
            if (priceExact) urlParams.delete('price_exact');
        } else {
            urlParams.delete(`selected_${type}`);
        }

        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    /**
     * Clears all filters except for specific parameters
     */
    function clearAllFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        // List of parameters to keep when clearing filters
        const paramsToKeep = ['search', 'per_page', 'id_proyek'];
        const currentParams = Array.from(urlParams.keys());

        // Remove all parameters except those in paramsToKeep
        currentParams.forEach(param => {
            if (!paramsToKeep.includes(param)) {
                urlParams.delete(param);
            }
        });

        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    /**
     * Toggles a checkbox when its label is clicked
     * @param {HTMLElement} element - The label element
     */
    function toggleCheckbox(element) {
        const checkbox = $(element).prev('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
    }

    /**
     * Toggles all visible checkboxes in a filter popup
     * If all visible are checked, unchecks all visible. Otherwise, checks all visible.
     * @param {string} type - The type of filter
     */
    function toggleAllFilters(type) {
        const container = $(`.${type}-checkbox`).first().closest('.checkbox-list');
        const searchInput = container.closest('.filter-popup').find('input[type="text"]');
        const searchText = searchInput.val().toLowerCase();

        // Only work with items that match the current search and are visible in UI
        const visibleCheckboxes = container.find(`.${type}-checkbox`).filter(function() {
            // Check if the parent form-check is actually visible in the UI
            const formCheck = $(this).closest('.form-check');
            if (formCheck.css('display') === 'none') {
                return false;
            }

            // Double check with text search too
            const label = formCheck.find('label').text().toLowerCase();
            return !searchText || label.includes(searchText);
        });

        // Check if all currently visible checkboxes are checked
        const allVisibleChecked = visibleCheckboxes.length > 0 &&
            visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length;

        // Toggle only visible checkboxes
        visibleCheckboxes.prop('checked', !allVisibleChecked);

        // Re-run the filter to maintain current search and ordering
        filterCheckboxes(type, {
            target: searchInput[0]
        });
    }

    // Document ready event handler
    $(document).ready(function() {
        // Close popup when clicking outside
        $(document).on('click', function(event) {
            // Tambahkan pengecekan untuk datepicker
            if (!$(event.target).closest('.filter-popup, button, .ui-datepicker, .ui-datepicker *').length) {
                $('.filter-popup').hide();
            }
        });

        // Handle keyboard shortcuts
        $(document).on('keydown', function(event) {
            // Ignore keydown events if a filter operation is in progress
            if (isFilterProcessing) {
                event.preventDefault();
                return false;
            }

            const visiblePopup = $('.filter-popup:visible');
            if (!visiblePopup.length) return; // Exit if no popup is visible

            const activeElement = document.activeElement;
            const popupId = visiblePopup.attr('id');
            const type = popupId.replace('-filter', '').replaceAll('-', '_');

            // Separate checks for different input types
            const isSearchInput = $(activeElement).is('.filter-popup input[id^="search-"]');
            const isOtherInput = $(activeElement).is('input[type="number"], textarea, .datepicker, .price-input');

            if (event.key === 'Escape') {
                $('.filter-popup').hide();
            } else if (event.key === 'Enter' && $(activeElement).is('.filter-popup input')) {
                applyFilter(type);
                event.preventDefault();
            } else if (event.key === 'a' && event.ctrlKey) {
                // Special case for search inputs - implement the checkbox toggle behavior
                if (isSearchInput) {
                    event.preventDefault(); // Prevent default select all text
                    toggleAllFilters(type);
                    return false;
                }
                // For other inputs, allow default browser behavior (select all text)
                else if (!isOtherInput) {
                    // If not in any input, toggle checkboxes
                    event.preventDefault();
                    toggleAllFilters(type);
                    return false;
                }
                // For other inputs (price, date, etc.), let the browser handle it
            }
        });

        // Prevent label clicks from bubbling up
        $(document).on('click', '.form-check-label', function(event) {
            event.stopPropagation();
        });

        // Apply debounced search filter - IMPORTANT: Only target search inputs specifically
        const debouncedFilter = debounce((event) => {
            const popupId = $(event.target).closest('.filter-popup').attr('id');
            const type = popupId.replace('-filter', '').replace('-', '_');
            filterCheckboxes(type, event);
        }, 300); // Wait 300ms before applying filter

        // Attach debounced filter ONLY to search inputs (not to all text inputs)
        $('.filter-popup input[id^="search-"]').on('input', debouncedFilter);

        // Apply filter if search input already has value on document ready
        $('.filter-popup input[id^="search-"]').each(function() {
            if ($(this).val()) {
                const popupId = $(this).closest('.filter-popup').attr('id');
                const type = popupId.replace('-filter', '').replace('-', '_');
                filterCheckboxes(type, {
                    target: this
                });
            }
        });

        // Add event listeners to clear other inputs when one changes
        const inputs = document.querySelectorAll('input[type="number"], .datepicker, .price-input');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const paramName = this.id.split('-')[0];
                if (this.id.endsWith('-exact')) {
                    document.getElementById(`${paramName}-gt`).value = '';
                    document.getElementById(`${paramName}-lt`).value = '';
                } else {
                    document.getElementById(`${paramName}-exact`).value = '';
                }
            });
        });

        // Add keypress event listener for inputs
        inputs.forEach(input => {
            input.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const paramName = this.id.split('-')[0];
                    applyFilter(paramName);
                }
            });
        });

        // Ensure Apply button works for numeric filters
        document.querySelectorAll('.filter-popup').forEach(popup => {
            const applyButton = popup.querySelector('button[type="button"]');
            if (applyButton) {
                applyButton.addEventListener('click', function(event) {
                    const paramName = popup.id.replace('-filter', '').replace(/-/g, '_');
                    applyFilter(paramName, this); // Pass the button element
                });
            }
        });
    });

    // Custom function to determine item priority
    function getItemPriority(label) {
        const trimmedLabel = label.trim();
        if (trimmedLabel === "Empty/Null") return 1;
        if (trimmedLabel === "-") return 2;
        return 3;
    }

    /**
     * Parse price value from Indonesian format to standard decimal
     * @param {string} value - Price value in Indonesian format (e.g. "1.234,56")
     * @returns {string} - Standard decimal value (e.g. "1234.56")
     */
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

    /**
     * Format price input field with Indonesian locale
     * @param {HTMLElement} element - The input element to format
     */
    function formatPriceInput(element) {
        let value = element.value;

        // Remove all non-numeric characters except for comma
        value = value.replace(/[^\d,]/g, '');

        // Ensure only one comma exists
        const commaIndex = value.indexOf(',');
        if (commaIndex !== -1) {
            const beforeComma = value.substring(0, commaIndex);
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
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            // If there's a comma, format only the part before comma
            const beforeComma = value.substring(0, commaIndex).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            const afterComma = value.substring(commaIndex);
            value = beforeComma + afterComma;
        }

        element.value = value;
    }
</script>
