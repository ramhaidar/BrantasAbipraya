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
        $(document).ready(function() {
            'use strict';

            const $dokumentasiPreviewContainer = $('#dokumentasiPreviewContainer');
            const $largeImagePreview = $('#largeImagePreviewForShow');
            const $imagePreviewTitle = $('#imagePreviewTitleForShow');
            const dokumentasiRoute = @json(route('evaluasi_rkb_urgent.detail.dokumentasi', ['id' => ':id']));

            window.showDokumentasi = function(id) {
                $dokumentasiPreviewContainer.empty();
                const fetchUrl = dokumentasiRoute.replace(':id', id);

                $.getJSON(fetchUrl)
                    .done(function(data) {
                        if (data.dokumentasi?.length) {
                            data.dokumentasi.forEach(file => {
                                $('<img>', {
                                        src: file.url,
                                        alt: file.name,
                                        title: file.name
                                    }).addClass('img-thumbnail')
                                    .on('click', () => {
                                        $('#dokumentasiPreviewModal').modal('hide');
                                        $largeImagePreview.attr('src', file.url);
                                        $imagePreviewTitle.text(file.name);
                                        $('#imagePreviewModalforShow').modal('show');
                                    })
                                    .appendTo($dokumentasiPreviewContainer);
                            });
                        } else {
                            $dokumentasiPreviewContainer.html(
                                '<p class="text-muted text-center">Tidak ada Dokumentasi</p>'
                            );
                        }
                        $('#dokumentasiPreviewModal').modal('show');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching dokumentasi:', textStatus, errorThrown);
                        $dokumentasiPreviewContainer.html(
                            '<p class="text-danger text-center">Failed to load dokumentasi</p>'
                        );
                        $('#dokumentasiPreviewModal').modal('show');
                    });
            };

            $('#imagePreviewModalforShow').on('hidden.bs.modal', function() {
                $('#dokumentasiPreviewModal').modal('show');
            });

            // Prevent form submission when clicking dokumentasi button
            $(document).on('click', '[data-id]', function(e) {
                if ($(this).closest('td').hasClass('text-center')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    </script>
@endpush
