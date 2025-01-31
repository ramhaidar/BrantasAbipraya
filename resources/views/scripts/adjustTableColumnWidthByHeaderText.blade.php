<script>
    $(document).ready(function() {
        const $table = $('#table-data');
        const $headers = $table.find('thead th');
        const textsToCheck = ['Detail', 'Aksi', 'Supplier', 'Riwayat', 'Evaluasi'];
        let indices = {};

        // Find the indices of the headers that match the texts in textsToCheck array
        $headers.each(function(index) {
            const headerText = $(this).text().trim();
            if (textsToCheck.includes(headerText)) {
                indices[headerText] = index;
            }
        });

        // Set the width of the corresponding columns in tbody
        $.each(indices, function(text, index) {
            $table.find('tbody tr').each(function() {
                $(this).find('td').eq(index).css('width', '1%');
            });
        });
    });
</script>
