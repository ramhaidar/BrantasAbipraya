<!-- Modal for Documentation -->
<div class="modal fade" id="dokumentasiModal" aria-labelledby="dokumentasiModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dokumentasiModalLabel">Dokumentasi</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="carousel slide" id="dokumentasiCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <!-- Images will be loaded here dynamically -->
                    </div>
                    <button class="carousel-control-prev" data-bs-target="#dokumentasiCarousel" data-bs-slide="prev" type="button" style="width: 10%; background: rgba(0,0,0,0.3);">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="width: 45px; height: 45px;"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" data-bs-target="#dokumentasiCarousel" data-bs-slide="next" type="button" style="width: 10%; background: rgba(0,0,0,0.3);">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="width: 45px; height: 45px;"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        function showDokumentasiModal(id) {
            $.ajax({
                url: `/atb/dokumentasi/${id}`,
                type: 'GET',
                success: function(response) {
                    const carouselInner = $('#dokumentasiCarousel .carousel-inner');
                    carouselInner.empty();

                    if (response.dokumentasi && response.dokumentasi.length > 0) {
                        response.dokumentasi.forEach((foto, index) => {
                            // Create carousel item
                            const item = $('<div>').addClass('carousel-item')
                                .css({
                                    'height': '500px',
                                    'background-color': '#000'
                                });

                            if (index === 0) item.addClass('active');

                            // Add image with full path 
                            const img = $('<img>')
                                .attr('src', '/' + foto) // Add leading slash for absolute path
                                .addClass('d-block w-100')
                                .css({
                                    'height': '100%',
                                    'object-fit': 'contain'
                                });

                            item.append(img);
                            carouselInner.append(item);
                        });

                        $('#dokumentasiModal').modal('show');
                    } else {
                        alert('Tidak ada foto dokumentasi yang ditemukan di direktori.');
                    }
                },
                error: function() {
                    alert('Gagal memuat foto dokumentasi.');
                }
            });
        }

        // Handle modal hidden event to reset carousel
        $('#dokumentasiModal').on('hidden.bs.modal', function() {
            $('#dokumentasiCarousel .carousel-inner').empty();
        });
    </script>
@endpush
