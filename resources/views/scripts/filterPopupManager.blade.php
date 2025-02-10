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

    function filterCheckboxes(type) {
        const searchText = $(event.target).val().toLowerCase();
        const selector = `.${type}-checkbox`;
        $(selector).each(function() {
            const label = $(this).next('label').text().toLowerCase();
            $(this).parent().toggle(label.includes(searchText));
        });
    }

    function applyFilter(type) {
        const selector = `.${type}-checkbox:checked`;
        const selected = $(selector).map(function() {
            return $(this).val();
        }).get();

        // Dapatkan semua parameter URL saat ini
        const urlParams = new URLSearchParams(window.location.search);

        // Update atau hapus parameter filter yang diubah
        if (selected.length > 0) {
            urlParams.set(`selected_${type}`, selected.join(','));
        } else {
            urlParams.delete(`selected_${type}`);
        }

        // Redirect dengan parameter yang diupdate
        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    function clearFilter(type) {
        // Dapatkan semua parameter URL saat ini
        const urlParams = new URLSearchParams(window.location.search);

        if (type === 'price') {
            urlParams.delete('price_min');
            urlParams.delete('price_max');
            urlParams.delete('price_exact');
        } else {
            urlParams.delete(`selected_${type}`);
        }

        // Redirect dengan parameter yang diupdate
        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    // Handler untuk tombol "Clear All Filters"
    function clearAllFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        // Hapus semua parameter filter tapi pertahankan parameter lain
        const paramsToKeep = ['search', 'per_page', 'id_proyek'];
        const currentParams = Array.from(urlParams.keys());

        currentParams.forEach(param => {
            if (!paramsToKeep.includes(param)) {
                urlParams.delete(param);
            }
        });

        // Redirect dengan parameter yang tersisa
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
    });
</script>
