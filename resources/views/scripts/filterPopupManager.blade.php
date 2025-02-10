<script>
    function toggleFilter(id) {
        $('.filter-popup').not(`#${id}`).hide();
        const popup = $(`#${id}`);
        const button = $(`button[onclick="toggleFilter('${id}')"]`);

        if (popup.is(':hidden')) {
            if (!popup.data('originalWidth')) {
                popup.css('width', ''); // Reset width only on first open
                popup.data('originalWidth', popup.outerWidth()); // Store original width
            }

            // Sort checkboxes when opening popup
            const type = id.replace('-filter', '').replace('-', '_');
            filterCheckboxes(type);

            positionPopup(popup, button);
            popup.toggle();
            popup.find('input[type="text"]').focus(); // Add focus to search input
        } else {
            popup.toggle();
        }
    }

    function positionPopup(popup, button) {
        const buttonRect = button[0].getBoundingClientRect();
        const windowWidth = $(window).width();
        const windowHeight = $(window).height();
        const popupHeight = popup.outerHeight();
        const safetyMargin = 40; // Increased margin from 25 to 40px
        const verticalGap = 10;

        // Gunakan lebar yang tersimpan atau hitung ulang jika belum ada
        const originalWidth = popup.data('originalWidth');

        // Ensure popup width doesn't exceed viewport
        const maxWidth = windowWidth - (safetyMargin * 2);
        popup.css({
            'width': Math.min(originalWidth, maxWidth) + 'px',
            'max-width': `${maxWidth}px`
        });

        // Force content to wrap if needed
        popup.find('.checkbox-list').css({
            'word-break': 'break-word',
            'overflow-x': 'hidden'
        });

        // Calculate vertical position
        let top = buttonRect.bottom + verticalGap;

        // Check if popup would go below viewport
        if (top + popupHeight > windowHeight - safetyMargin) {
            top = buttonRect.top - popupHeight - verticalGap;
        }

        // Ensure top is not negative and has minimum margin from top
        top = Math.max(safetyMargin, top);

        // Calculate horizontal position
        let left = buttonRect.left;

        // Check if popup would go off right edge
        if (left + originalWidth > windowWidth - safetyMargin) {
            left = windowWidth - originalWidth - safetyMargin;
            popup.addClass('right-aligned');
        } else {
            popup.removeClass('right-aligned');
        }

        // Ensure left is not negative and has minimum margin from left
        left = Math.max(safetyMargin, left);

        // Set the position with smooth transition
        popup.css({
            top: `${top}px`,
            left: `${left}px`,
            transition: 'left 0.2s, top 0.2s' // Optional: adds smooth movement
        });
    }

    // Update popup positions on window resize
    $(window).on('resize', function() {
        $('.filter-popup:visible').each(function() {
            const id = $(this).attr('id');
            const button = $(`button[onclick="toggleFilter('${id}')"]`);
            positionPopup($(this), button);
        });
    });

    function filterCheckboxes(type, event) {
        const searchText = event ? $(event.target).val().toLowerCase() : '';
        const selector = `.${type}-checkbox`;
        const container = $(selector).first().closest('.checkbox-list');

        // Filter items based on search text
        container.find('.form-check').each(function() {
            const label = $(this).find('label').text().toLowerCase();
            $(this).toggle(label.includes(searchText));
        });

        // Only sort if there's no search text
        if (!searchText) {
            const items = container.children('.form-check').get();

            items.sort((a, b) => {
                const isCheckedA = $(a).find('input[type="checkbox"]').prop('checked');
                const isCheckedB = $(b).find('input[type="checkbox"]').prop('checked');
                const labelA = $(a).find('label').text().toLowerCase();
                const labelB = $(b).find('label').text().toLowerCase();

                if (isCheckedA !== isCheckedB) {
                    return isCheckedA ? -1 : 1;
                }

                if (labelA === "empty/null") return -1;
                if (labelB === "empty/null") return 1;
                return labelA.localeCompare(labelB);
            });

            container.append(items);
        }
    }

    function applyFilter(type) {
        const selector = `.${type}-checkbox:checked`;
        const selected = $(selector).map(function() {
            return $(this).val();
        }).get();

        const urlParams = new URLSearchParams(window.location.search);

        // Reset page parameter ke 1
        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        if (selected.length > 0) {
            // Encode the selected values using base64
            const encodedValue = btoa(selected.join(','));
            urlParams.set(`selected_${type}`, encodedValue);
        } else {
            urlParams.delete(`selected_${type}`);
        }

        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    function clearFilter(type) {
        const urlParams = new URLSearchParams(window.location.search);

        // Reset page parameter ke 1
        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        if (type === 'price') {
            // For price filters, encode the values if they exist
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

    // Handler untuk tombol "Clear All Filters"
    function clearAllFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        // Reset page parameter ke 1
        if (urlParams.has('page')) {
            urlParams.set('page', '1');
        }

        // Hapus semua parameter filter tapi pertahankan parameter lain
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

        // Clear stored widths when filters are cleared
        $('.filter-popup').on('hide', function() {
            $(this).removeData('originalWidth');
        });

        // Sort checkboxes initially
        $('.filter-popup').each(function() {
            const type = $(this).attr('id').replace('-filter', '').replace('-', '_');
            filterCheckboxes(type);
        });

        // Add event listener for search inputs
        $('.filter-popup input[type="text"]').on('input', function() {
            const popupId = $(this).closest('.filter-popup').attr('id');
            const type = popupId.replace('-filter', '').replace('-', '_');
            filterCheckboxes(type, {
                target: this
            });
        });

        // Initial sort for checkboxes
        $('.filter-popup').each(function() {
            const type = $(this).attr('id').replace('-filter', '').replace('-', '_');
            filterCheckboxes(type);
        });
    });
</script>
