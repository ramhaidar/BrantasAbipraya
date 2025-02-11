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

    function toggleFilter(id) {
        $('.filter-popup').not(`#${id}`).hide();
        const popup = $(`#${id}`);
        const button = $(`button[onclick="toggleFilter('${id}')"]`);

        if (popup.is(':hidden')) {
            if (!popup.data('originalWidth')) {
                popup.css('width', '');
                popup.data('originalWidth', popup.outerWidth());
            }

            // Set checked state berdasarkan URL parameter
            const type = id.replace('-filter', '').replace('-', '_');
            const urlParams = new URLSearchParams(window.location.search);
            const encodedSelected = urlParams.get(`selected_${type}`);

            // Reset semua checkbox
            $(`.${type}-checkbox`).prop('checked', false);

            // Jika ada parameter yang ter-encode
            if (encodedSelected) {
                try {
                    const selectedValues = atob(encodedSelected).split(',');
                    selectedValues.forEach(value => {
                        $(`.${type}-checkbox[value="${value}"]`).prop('checked', true);
                    });
                } catch (e) {
                    console.error('Error decoding base64:', e);
                }
            }

            filterCheckboxes(type);

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

        // Store search text in data attribute to prevent loss
        if (searchEvent) {
            container.data('searchText', $(searchEvent.target).val().toLowerCase());
        }

        const searchText = container.data('searchText') || '';

        // Store original items if not already stored
        if (!container.data('originalItems')) {
            container.data('originalItems', container.html());
        }

        // Restore original items before filtering
        if (!searchText) {
            container.html(container.data('originalItems'));
        }

        const items = container.children('.form-check').get();

        // First hide/show based on search
        items.forEach(item => {
            const $item = $(item);
            const label = $item.find('label').text().toLowerCase();
            const isChecked = $item.find('input[type="checkbox"]').prop('checked');

            if (isChecked || !searchText || label.includes(searchText)) {
                $item.show();
            } else {
                $item.hide();
            }
        });

        // Then sort visible items
        const visibleItems = items.filter(item => $(item).is(':visible'));
        visibleItems.sort((a, b) => {
            const isCheckedA = $(a).find('input[type="checkbox"]').prop('checked');
            const isCheckedB = $(b).find('input[type="checkbox"]').prop('checked');

            if (isCheckedA !== isCheckedB) {
                return isCheckedB ? 1 : -1;
            }

            const labelA = $(a).find('label').text().toLowerCase();
            const labelB = $(b).find('label').text().toLowerCase();

            if (labelA === "empty/null") return -1;
            if (labelB === "empty/null") return 1;

            return labelA.localeCompare(labelB);
        });

        // Detach items from DOM
        const $items = $(items);
        $items.detach();

        // Append sorted visible items
        container.append(visibleItems);

        // Append hidden items to preserve them
        const hiddenItems = items.filter(item => !$(item).is(':visible'));
        container.append(hiddenItems);
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

        $('.filter-popup').on('hide', function() {
            const container = $(this).find('.checkbox-list');
            if (container.data('originalItems')) {
                container.html(container.data('originalItems'));
            }
            container.removeData('originalItems');
            container.removeData('searchText');
            $(this).removeData('originalWidth');
            $(this).find('input[type="text"]').val('');
        });

        $('.filter-popup').each(function() {
            const type = $(this).attr('id').replace('-filter', '').replace('-', '_');
            filterCheckboxes(type);
        });

        // Replace the search event handler with debounced version
        const debouncedFilter = debounce((event) => {
            const popupId = $(event.target).closest('.filter-popup').attr('id');
            const type = popupId.replace('-filter', '').replace('-', '_');
            filterCheckboxes(type, event);
        }, 300);

        $('.filter-popup input[type="text"]').on('input', debouncedFilter);

        // Clear search text when popup is closed
        $('.filter-popup').on('hide', function() {
            $(this).find('.checkbox-list').removeData('searchText');
            $(this).find('input[type="text"]').val('');
        });
    });
</script>
