<!-- Modal for Lampiran -->
<div class="modal fade" id="modalForLampiran" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForLampiranLabel">Lampiran Dokumen</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="lampiranForm" novalidate method="POST" action="{{ route('rkb_urgent.detail.lampiran.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="lampiranInput">Unggah PDF</label>
                            <input class="form-control" id="lampiranInput" name="lampiran" type="file" accept="application/pdf" required>
                            <div class="invalid-feedback">File PDF diperlukan.</div>
                        </div>

                        <div class="col-12 mt-3" id="pdfPreviewContainer" style="display: none;">
                            <label class="form-label">Pratinjau PDF:</label>
                            <canvas id="pdfPreviewCanvas" style="border: 1px solid #ccc; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <!-- Include PDF.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            const lampiranInput = document.getElementById('lampiranInput');
            const pdfPreviewContainer = document.getElementById('pdfPreviewContainer');
            const pdfPreviewCanvas = document.getElementById('pdfPreviewCanvas');

            // Function to preview PDF using PDF.js
            const previewPDF = (file) => {
                const fileReader = new FileReader();

                fileReader.onload = function(e) {
                    const pdfData = e.target.result;

                    // Using PDF.js to load and render PDF
                    const loadingTask = pdfjsLib.getDocument({
                        data: pdfData
                    });

                    loadingTask.promise.then(function(pdf) {
                        pdf.getPage(1).then(function(page) {
                            const scale = 1.5;
                            const viewport = page.getViewport({
                                scale: scale
                            });

                            const context = pdfPreviewCanvas.getContext('2d');
                            pdfPreviewCanvas.width = viewport.width;
                            pdfPreviewCanvas.height = viewport.height;

                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };
                            page.render(renderContext);

                            pdfPreviewContainer.style.display = 'block';
                        });
                    }, function(reason) {
                        console.error('Error loading PDF:', reason);
                    });
                };

                fileReader.readAsArrayBuffer(file);
            };

            lampiranInput.addEventListener('change', function() {
                const file = lampiranInput.files[0];
                if (file && file.type === 'application/pdf') {
                    previewPDF(file);
                } else {
                    alert('Silakan unggah file PDF yang valid.');
                    lampiranInput.value = ''; // Reset the input
                    pdfPreviewContainer.style.display = 'none';
                }
            });
        });
    </script>
@endpush
