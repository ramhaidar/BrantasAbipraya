<!-- Modal for Large Image Preview (Add) -->
<div class="modal fade" id="imagePreviewModalforAdd" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitleForAdd">Preview</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body d-flex justify-content-center align-items-center p-0">
                <img class="img-fluid" id="largeImagePreviewForAdd" src="" alt="Preview" style="object-fit: contain; width: auto;">
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Large Image Preview (Show) -->
<div class="modal fade" id="imagePreviewModalforShow" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitleForShow">Preview</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body d-flex justify-content-center align-items-center p-0">
                <img class="img-fluid" id="largeImagePreviewForShow" src="" alt="Preview" style="object-fit: contain; width: auto;">
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Large Image Preview (Edit) -->
<div class="modal fade" id="imagePreviewModalforEdit" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitleForEdit">Preview</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body d-flex justify-content-center align-items-center p-0">
                <img class="img-fluid" id="largeImagePreviewForEdit" src="" alt="Preview" style="object-fit: contain; width: auto;">
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Mini Preview -->
<div class="modal fade" id="dokumentasiPreviewModal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dokumentasiPreviewTitle">Dokumentasi Preview</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-3" id="dokumentasiPreviewContainer"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        const $dokumentasiPreviewContainer = $('#dokumentasiPreviewContainer');
        const $largeImagePreviewForShow = $('#largeImagePreviewForShow');
        const dokumentasiPreviewModal = new bootstrap.Modal('#dokumentasiPreviewModal');
        const imagePreviewModalforShow = new bootstrap.Modal('#imagePreviewModalforShow');

        // Laravel route name for dokumentasi
        const dokumentasiRoute = @json(route('rkb_urgent.detail.dokumentasi', ['id' => ':id']));

        // Fetch and display dokumentasi in modal
        window.showDokumentasi = function(id) {
            $dokumentasiPreviewContainer.empty();

            const fetchUrl = dokumentasiRoute.replace(':id', id);

            $.getJSON(fetchUrl)
                .done(function(data) {
                    if (data.dokumentasi && data.dokumentasi.length) {
                        $.each(data.dokumentasi, function(_, file) {
                            $('<img>', {
                                src: file.url,
                                alt: file.name,
                                title: file.name,
                                class: 'img-thumbnail',
                                click: function() {
                                    $('#dokumentasiPreviewModal').modal('hide');
                                    $largeImagePreviewForShow.attr('src', file.url);
                                    $('#imagePreviewTitleForShow').text(file.name);
                                    imagePreviewModalforShow.show();
                                }
                            }).appendTo($dokumentasiPreviewContainer);
                        });
                    } else {
                        $dokumentasiPreviewContainer.html('<p class="text-muted text-center">Tidak ada Dokumentasi</p>');
                    }
                    dokumentasiPreviewModal.show();
                })
                .fail(function() {
                    $dokumentasiPreviewContainer.html('<p class="text-danger text-center">Failed to load dokumentasi</p>');
                    dokumentasiPreviewModal.show();
                });
        };

        // Event listener for when the preview modal is closed
        $('#imagePreviewModalforShow').on('hidden.bs.modal', function() {
            $('#dokumentasiPreviewModal').modal('show');
        });
    </script>
@endpush
