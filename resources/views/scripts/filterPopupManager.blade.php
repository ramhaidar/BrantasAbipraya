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
            // Try to decode base64 and split into array
            const decoded = atob(paramValue);
            return decoded.split(',');
        } catch (e) {
            // If not base64 encoded, split the original value
            return paramValue.split(',');
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
            const type = id.replace('-filter', '').replace('-', '_');
            const urlParams = new URLSearchParams(window.location.search);
            const encodedSelected = urlParams.get(`selected_${type}`);

            // Restore previously selected checkboxes
            if (encodedSelected) {
                try {
                    const decodedValues = atob(encodedSelected).split(',');
                    decodedValues.forEach(value => {
                        const cleanValue = value.trim();
                        const checkbox = $(`.${type}-checkbox[value="${cleanValue}"]`);
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        }
                    });
                } catch (e) {
                    console.error('Base64 decode error:', e);
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

        container.children('.form-check').each(function() {
            const label = $(this).find('label').text().toLowerCase();
            const isChecked = $(this).find('input[type="checkbox"]').prop('checked');

            // Show item if it's checked or matches search
            if (isChecked || !searchText || label.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    /**
     * Applies the selected filters and updates the URL
     * @param {string} type - The type of filter to apply
     */
    function applyFilter(type) {
        const selector = `.${type}-checkbox:checked`;
        const selected = $(selector).map(function() {
            return $(this).val();
        }).get();

        const urlParams = new URLSearchParams(window.location.search);

        // Reset to first page when filter changes
        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        // Update URL parameters
        if (selected.length > 0) {
            const encodedValue = btoa(selected.join(','));
            urlParams.set(`selected_${type}`, encodedValue);
        } else {
            urlParams.delete(`selected_${type}`);
        }

        // Navigate to updated URL
        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
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

    // Document ready event handler
    $(document).ready(function() {
        // Close popup when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.filter-popup, button').length) {
                $('.filter-popup').hide();
            }
        });

        // Close popup when pressing Escape key
        $(document).on('keydown', function(event) {
            if (event.key === 'Escape') {
                $('.filter-popup').hide();
            }
        });

        // Prevent label clicks from bubbling up
        $(document).on('click', '.form-check-label', function(event) {
            event.stopPropagation();
        });

        // Apply debounced search filter
        const debouncedFilter = debounce((event) => {
            const popupId = $(event.target).closest('.filter-popup').attr('id');
            const type = popupId.replace('-filter', '').replace('-', '_');
            filterCheckboxes(type, event);
        }, 300); // Wait 300ms before applying filter

        // Attach debounced filter to search inputs
        $('.filter-popup input[type="text"]').on('input', debouncedFilter);
    });
</script>
