<script>
    // Add debounce function at the top
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

    function decodeParameterValue(value) {
        if (!value) return '';

        try {
            // Try to decode as base64
            return atob(value);
        } catch (e) {
            // If decoding fails, assume it's already decoded
            return value;
        }
    }

    function getSelectedValues(paramValue) {
        if (!paramValue) return [];

        try {
            // Try base64 decode first
            const decoded = atob(paramValue);
            return decoded.split(',');
        } catch (e) {
            // If not base64, split directly
            return paramValue.split(',');
        }
    }

    function sanitizeValue(value) {
        // Remove any non-UTF8 characters
        return value.replace(/[^\x00-\x7F]/g, "");
    }

    function toggleFilter(id) {
        $('.filter-popup').not(`#${id}`).hide();
        const popup = $(`#${id}`);
        const button = $(`button[onclick="toggleFilter('${id}')"]`);

        if (popup.is(':hidden')) {
            if (!popup.data('originalWidth')) {
                popup.css('width', '');
                popup.data('originalWidth', popup.outerWidth());
            }

            const type = id.replace('-filter', '').replace('-', '_');
            const urlParams = new URLSearchParams(window.location.search);
            const encodedSelected = urlParams.get(`selected_${type}`);

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

            positionPopup(popup, button);
            popup.toggle();
        } else {
            popup.toggle();
        }
    }

    function positionPopup(popup, button) {
        const buttonRect = button[0].getBoundingClientRect();
        const windowWidth = $(window).width();
        const windowHeight = $(window).height();
        const popupHeight = popup.outerHeight();
        const safetyMargin = 40;
        const verticalGap = 10;

        const originalWidth = popup.data('originalWidth');

        const maxWidth = windowWidth - (safetyMargin * 2);
        popup.css({
            'width': Math.min(originalWidth, maxWidth) + 'px',
            'max-width': `${maxWidth}px`
        });

        popup.find('.checkbox-list').css({
            'word-break': 'break-word',
            'overflow-x': 'hidden'
        });

        let top = buttonRect.bottom + verticalGap;

        if (top + popupHeight > windowHeight - safetyMargin) {
            top = buttonRect.top - popupHeight - verticalGap;
        }

        top = Math.max(safetyMargin, top);

        let left = buttonRect.left;

        if (left + originalWidth > windowWidth - safetyMargin) {
            left = windowWidth - originalWidth - safetyMargin;
            popup.addClass('right-aligned');
        } else {
            popup.removeClass('right-aligned');
        }

        left = Math.max(safetyMargin, left);

        popup.css({
            top: `${top}px`,
            left: `${left}px`,
            transition: 'left 0.2s, top 0.2s'
        });
    }

    $(window).on('resize', function() {
        $('.filter-popup:visible').each(function() {
            const id = $(this).attr('id');
            const button = $(`button[onclick="toggleFilter('${id}')"]`);
            positionPopup($(this), button);
        });
    });

    function filterCheckboxes(type, searchEvent) {
        const selector = `.${type}-checkbox`;
        const container = $(selector).first().closest('.checkbox-list');
        const searchText = searchEvent ? $(searchEvent.target).val().toLowerCase() : '';

        container.children('.form-check').each(function() {
            const label = $(this).find('label').text().toLowerCase();
            const isChecked = $(this).find('input[type="checkbox"]').prop('checked');

            if (isChecked || !searchText || label.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function applyFilter(type) {
        const selector = `.${type}-checkbox:checked`;
        const selected = $(selector).map(function() {
            return $(this).val();
        }).get();

        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        if (selected.length > 0) {
            // Always use base64 for new filter values
            const encodedValue = btoa(selected.join(','));
            urlParams.set(`selected_${type}`, encodedValue);
        } else {
            urlParams.delete(`selected_${type}`);
        }

        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    function clearFilter(type) {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

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

    function clearAllFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        const paramsToKeep = ['search', 'per_page', 'id_proyek'];
        const currentParams = Array.from(urlParams.keys());

        currentParams.forEach(param => {
            if (!paramsToKeep.includes(param)) {
                urlParams.delete(param);
            }
        });

        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    function toggleCheckbox(element) {
        const checkbox = $(element).prev('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
    }

    $(document).ready(function() {
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.filter-popup, button').length) {
                $('.filter-popup').hide();
            }
        });

        $(document).on('keydown', function(event) {
            if (event.key === 'Escape') {
                $('.filter-popup').hide();
            }
        });

        $(document).on('click', '.form-check-label', function(event) {
            event.stopPropagation();
        });

        // Replace the search event handler with debounced version
        const debouncedFilter = debounce((event) => {
            const popupId = $(event.target).closest('.filter-popup').attr('id');
            const type = popupId.replace('-filter', '').replace('-', '_');
            filterCheckboxes(type, event);
        }, 300);

        $('.filter-popup input[type="text"]').on('input', debouncedFilter);
    });
</script>
