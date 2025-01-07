<!-- Modal for STT -->
<div class="fade modal" id="modalForSTT" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForSTTLabel">Surat Tanda Terima</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <div id="sttPreview" style="height: 600px;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js"></script>
    <script>
        function showSTTModal(id) {
            // Assuming you have an endpoint that returns the PDF URL
            $.ajax({
                url: `/atb/stt/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.pdf_url) {
                        PDFObject.embed(response.pdf_url, "#sttPreview", {
                            height: "600px",
                            fallbackLink: "<p>Browser Anda tidak mendukung preview PDF. <a href='[url]'>Unduh PDF</a></p>"
                        });
                        $('#modalForSTT').modal('show');
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'STT tidak ditemukan',
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat STT',
                        icon: 'error'
                    });
                }
            });
        }
    </script>
@endpush
