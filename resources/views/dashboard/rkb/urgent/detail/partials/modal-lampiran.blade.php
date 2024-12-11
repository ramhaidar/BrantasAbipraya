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
                            <div id="pdfPreview" style="border: 1px solid #ccc; width: 100%; height: 500px;"></div>
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

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js" integrity="sha512-Nr6NV16pWOefJbWJiT8SrmZwOomToo/84CNd0MN6DxhP5yk8UAoPUjNuBj9KyRYVpESUb14RTef7FKxLVA4WGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            //console.log("jQuery is ready!");

            const lampiranInput = $('#lampiranInput');
            const pdfPreviewContainer = $('#pdfPreviewContainer');
            const pdfPreview = $('#pdfPreview');
            const resetButton = $('#lampiranForm button[type="reset"]'); // Select reset button

            //console.log(lampiranInput);

            lampiranInput.on('change', function() {
                //console.log("File input changed.");
                const file = this.files[0];

                if (file && file.type === 'application/pdf') {
                    //console.log("PDF file selected:", file.name);

                    // Revoke any previously created object URL to prevent memory leaks
                    const previousUrl = pdfPreview.data('fileUrl');
                    if (previousUrl) {
                        URL.revokeObjectURL(previousUrl);
                    }

                    const fileURL = URL.createObjectURL(file); // Create an object URL for the file
                    pdfPreview.data('fileUrl', fileURL); // Save the object URL for later cleanup

                    // Embed the PDF using PDFObject
                    const options = {
                        width: "100%",
                        height: "500px"
                    };
                    const embedded = PDFObject.embed(fileURL, '#pdfPreview', options);

                    // Show or hide the preview container based on embedding result
                    if (embedded) {
                        //console.log('PDF successfully embedded.');
                        pdfPreviewContainer.show();
                    } else {
                        console.error('PDF embedding failed.');
                        alert('Gagal menampilkan pratinjau PDF. Silakan coba lagi.');
                        pdfPreviewContainer.hide();
                    }
                } else {
                    alert('Silakan unggah file PDF yang valid.');
                    lampiranInput.val(''); // Reset the input
                    pdfPreviewContainer.hide();
                }
            });

            // Reset button functionality
            resetButton.on('click', function() {
                //console.log("Reset button clicked.");
                lampiranInput.val(''); // Clear the file input
                pdfPreviewContainer.hide(); // Hide the preview container
                const previousUrl = pdfPreview.data('fileUrl');
                if (previousUrl) {
                    URL.revokeObjectURL(previousUrl); // Clean up object URL
                    pdfPreview.removeData('fileUrl'); // Remove stored data
                }
            });

            // Hide preview if no file is selected
            lampiranInput.on('input', function() {
                if (!this.value) {
                    //console.log("Input cleared, hiding preview.");
                    pdfPreviewContainer.hide();
                }
            });
        });
    </script>
@endpush
