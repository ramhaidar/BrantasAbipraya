<!-- Modal for Lampiran (New Data) -->
<div class="modal fade" id="modalForLampiranNew" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForLampiranNewLabel">Lampiran Dokumen</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="lampiranFormNew" method="POST" action="{{ route('rkb_urgent.detail.lampiran.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="linkAlatDetailRKBIdNew" name="id_link_alat_detail_rkb" type="hiddenx" value="#">

                        <div class="col-12">
                            <label class="form-label required" for="lampiranInputNew">Unggah PDF</label>
                            <input class="form-control" id="lampiranInputNew" name="lampiran" type="file" accept="application/pdf" required>
                            <div class="invalid-feedback">File PDF diperlukan.</div>
                        </div>

                        <div class="col-12 mt-3" id="pdfPreviewContainerNew" style="display: none;">
                            <label class="form-label">Pratinjau PDF:</label>
                            <div id="pdfPreviewNew" style="border: 1px solid #ccc; width: 100%; height: 500px;"></div>
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

<!-- Modal for Lampiran (Update or Delete) -->
<div class="modal fade" id="modalForLampiranExist" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForLampiranExistLabel">Lampiran Dokumen</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="lampiranFormExist" method="PUT" action="{{ route('rkb_urgent.detail.lampiran.update', ['id' => '__dataId__']) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="linkAlatDetailRKBIdExist" name="id_link_alat_detail_rkb" type="hiddenx" value="#">
                        <input class="form-control" id="lampiranRKBUrgent" name="id_lampiran" type="hiddenx" value="#">

                        <div class="col-12">
                            <label class="form-label required" for="lampiranInputExist">Unggah PDF</label>
                            <input class="form-control" id="lampiranInputExist" name="lampiran" type="file" accept="application/pdf" required>
                            <div class="invalid-feedback">File PDF diperlukan.</div>
                        </div>

                        <div class="col-12 mt-3" id="pdfPreviewContainerExist" style="display: none;">
                            <label class="form-label">Pratinjau PDF:</label>
                            <div id="pdfPreviewExist" style="border: 1px solid #ccc; width: 100%; height: 500px;"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-danger me-2 w-25" type="button">Hapus</button>
                    <button class="btn btn-success w-25" type="submit">Ubah</button>
                </div>
            </form>

            <!-- Hidden form for delete -->
            <form id="deleteLampiranForm" style="display: none;" method="POST" action="{{ route('rkb_urgent.detail.lampiran.destroy', ['id' => '__dataId__']) }}">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js" integrity="sha512-Nr6NV16pWOefJbWJiT8SrmZwOomToo/84CNd0MN6DxhP5yk8UAoPUjNuBj9KyRYVpESUb14RTef7FKxLVA4WGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            // Initialize modal behavior for 'New' modal
            function initializeNewModal() {
                const modal = $('#modalForLampiranNew');
                const lampiranInput = modal.find('#lampiranInputNew');
                const pdfPreviewContainer = modal.find('#pdfPreviewContainerNew');
                const pdfPreview = modal.find('#pdfPreviewNew');
                const resetButton = modal.find('button[type="reset"]');

                lampiranInput.on('change', function() {
                    const file = this.files[0];

                    if (file && file.type === 'application/pdf') {
                        const previousUrl = pdfPreview.data('fileUrl');
                        if (previousUrl) {
                            URL.revokeObjectURL(previousUrl);
                        }

                        const fileURL = URL.createObjectURL(file);
                        pdfPreview.data('fileUrl', fileURL);

                        const options = {
                            width: "100%",
                            height: "500px"
                        };
                        const embedded = PDFObject.embed(fileURL, '#pdfPreviewNew', options);

                        if (embedded) {
                            pdfPreviewContainer.show();
                        } else {
                            console.error('PDF embedding failed.');
                            alert('Gagal menampilkan pratinjau PDF. Silakan coba lagi.');
                            pdfPreviewContainer.hide();
                        }
                    } else {
                        alert('Silakan unggah file PDF yang valid.');
                        lampiranInput.val('');
                        pdfPreviewContainer.hide();
                    }
                });

                resetButton.on('click', function() {
                    lampiranInput.val('');
                    pdfPreviewContainer.hide();
                    const previousUrl = pdfPreview.data('fileUrl');
                    if (previousUrl) {
                        URL.revokeObjectURL(previousUrl);
                        pdfPreview.removeData('fileUrl');
                    }
                });

                lampiranInput.on('input', function() {
                    if (!this.value) {
                        pdfPreviewContainer.hide();
                    }
                });
            }

            initializeNewModal();
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize modal behavior for 'Exist' modal
            function initializeExistModal() {
                const modal = $('#modalForLampiranExist');
                const lampiranInput = modal.find('#lampiranInputExist');
                const pdfPreviewContainer = modal.find('#pdfPreviewContainerExist');
                const pdfPreview = modal.find('#pdfPreviewExist');
                const resetButton = modal.find('button[type="reset"]');

                lampiranInput.on('change', function() {
                    const file = this.files[0];

                    if (file && file.type === 'application/pdf') {
                        const previousUrl = pdfPreview.data('fileUrl');
                        if (previousUrl) {
                            URL.revokeObjectURL(previousUrl);
                        }

                        const fileURL = URL.createObjectURL(file);
                        pdfPreview.data('fileUrl', fileURL);

                        const options = {
                            width: "100%",
                            height: "500px"
                        };
                        const embedded = PDFObject.embed(fileURL, '#pdfPreviewExist', options);

                        if (embedded) {
                            pdfPreviewContainer.show();
                        } else {
                            console.error('PDF embedding failed.');
                            alert('Gagal menampilkan pratinjau PDF. Silakan coba lagi.');
                            pdfPreviewContainer.hide();
                        }
                    } else {
                        alert('Silakan unggah file PDF yang valid.');
                        lampiranInput.val('');
                        pdfPreviewContainer.hide();
                    }
                });

                resetButton.on('click', function() {
                    lampiranInput.val('');
                    pdfPreviewContainer.hide();
                    const previousUrl = pdfPreview.data('fileUrl');
                    if (previousUrl) {
                        URL.revokeObjectURL(previousUrl);
                        pdfPreview.removeData('fileUrl');
                    }
                });

                lampiranInput.on('input', function() {
                    if (!this.value) {
                        pdfPreviewContainer.hide();
                    }
                });

                // Load PDF from server when modal is opened
                modal.on('show.bs.modal', function(event) {
                    const button = $(event.relatedTarget); // Button that triggered the modal
                    const dataIdLampiran = button.data('id-lampiran'); // Extract data-id-lampiran
                    const dataIdLinkAlatDetail = button.data('id-linkalatdetail'); // Extract data-id-linkalatdetail

                    modal.find('input[name="id_lampiran"]').val(dataIdLampiran); // Set id_lampiran
                    modal.find('input[name="id_link_alat_detail_rkb"]').val(dataIdLinkAlatDetail); // Set id_link_alat_detail_rkb

                    if (dataIdLampiran) {
                        const url = `{{ route('rkb_urgent.detail.lampiran.show', ['id' => '__dataId__']) }}`.replace('__dataId__', dataIdLampiran); // Replace placeholder with dataIdLampiran
                        $.get(url)
                            .done(function(data) {
                                const fileURL = data.pdf_url; // Assuming server returns JSON with 'pdf_url'

                                if (fileURL) {
                                    const options = {
                                        width: "100%",
                                        height: "500px"
                                    };
                                    const embedded = PDFObject.embed(fileURL, '#pdfPreviewExist', options);

                                    if (embedded) {
                                        pdfPreview.data('fileUrl', fileURL);
                                        pdfPreviewContainer.show();
                                    } else {
                                        console.error('PDF embedding failed.');
                                        alert('Gagal menampilkan pratinjau PDF. Silakan coba lagi.');
                                        pdfPreviewContainer.hide();
                                    }
                                } else {
                                    console.error('PDF file URL not provided by server.');
                                    alert('Lampiran tidak ditemukan.');
                                }
                            })
                            .fail(function() {
                                console.error('Error fetching PDF from server.');
                                alert('Gagal mengambil lampiran dari server.');
                            });
                    }
                });
            }

            initializeExistModal();
        });
    </script>

    <script>
        $(document).ready(function() {
            // Handle 'Hapus' button click in the 'Exist' modal
            $('#modalForLampiranExist').on('click', '.btn-danger', function() {
                const modal = $(this).closest('#modalForLampiranExist');
                const deleteForm = $('#deleteLampiranForm');
                const idLampiran = modal.find('input[name="id_lampiran"]').val();

                if (!idLampiran) {
                    alert('Lampiran tidak ditemukan untuk dihapus.');
                    return;
                }

                // Set the action URL for the delete form
                const deleteUrl = `{{ route('rkb_urgent.detail.lampiran.destroy', ['id' => '__dataId__']) }}`.replace('__dataId__', idLampiran);
                deleteForm.attr('action', deleteUrl);

                // Confirm the deletion
                const confirmDelete = confirm('Apakah Anda yakin ingin menghapus lampiran ini?');
                if (confirmDelete) {
                    deleteForm.submit();
                }
            });
        });
    </script>

    <script>
        // Event handler for setting data-id in the modals
        $(document).on('click', '.lampiranBtn', function() {
            const dataIdLinkAlatDetail = $(this).data('id-linkalatdetail'); // Extract data-id-linkalatdetail
            const dataIdLampiran = $(this).data('id-lampiran'); // Extract data-id-lampiran
            const targetModalId = $(this).data('bs-target'); // Get modal target
            const targetModal = $(targetModalId);

            // Set values for hidden inputs in modal
            targetModal.find('input[name="id_link_alat_detail_rkb"]').val(dataIdLinkAlatDetail);
            targetModal.find('input[name="id_lampiran"]').val(dataIdLampiran);
        });
    </script>
@endpush
