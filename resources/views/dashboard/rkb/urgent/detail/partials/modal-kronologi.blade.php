<!-- Modal for Kronologi -->
<div class="modal fade" id="kronologiModal" aria-labelledby="kronologiModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kronologiModalLabel">Kronologi RKB Urgent</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <textarea class="form-control" id="kronologiText" rows="10" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize kronologi modal
            const kronologiModal = new bootstrap.Modal('#kronologiModal');
            const $kronologiText = $('#kronologiText');

            // Laravel route for kronologi
            const kronologiRoute = @json(route('rkb_urgent.detail.kronologi', ['id' => ':id']));

            // Function to show kronologi in modal
            window.showKronologi = function(id) {
                const fetchUrl = kronologiRoute.replace(':id', id);

                $.getJSON(fetchUrl)
                    .done(function(data) {
                        if (data.kronologi) {
                            $kronologiText.val(data.kronologi);
                        } else {
                            $kronologiText.val('Tidak ada kronologi');
                        }
                        kronologiModal.show();
                    })
                    .fail(function() {
                        $kronologiText.val('Gagal memuat kronologi');
                        kronologiModal.show();
                    });
            };
        });
    </script>
@endpush
