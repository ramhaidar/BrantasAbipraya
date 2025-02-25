@include('dashboard.atb.partials.mutasi-proyek.modal-preview')

<div class="fade modal" id="modalForAccept" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAcceptLabel">Konfirmasi Terima Mutasi</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">Apakah Anda yakin ingin menerima mutasi ini?</p>
                <p class="p-0 m-0">Tindakan ini tidak dapat dibatalkan!</p>

                <div class="mt-3">
                    <label class="form-label required" for="quantity"><span>Quantity Diterima</span><span id="maxQuantityPlaceholder"></span></label>
                    <input class="form-control" id="quantity" name="quantity" type="number" min="1" required>
                    <div class="invalid-feedback">
                        Quantity diperlukan.
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label required" for="dokumentasi">Dokumentasi</label>
                    <input class="form-control" id="dokumentasiInput" name="dokumentasi[]" type="file" accept="image/*" multiple required>
                    <div class="invalid-feedback" id="dokumentasi-invalid-feedback">
                        Dokumentasi diperlukan.
                    </div>
                    <div class="mt-3 d-flex flex-wrap gap-2" id="dokumentasiPreview"></div>
                </div>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success w-25" id="confirmAcceptButton">Terima</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form untuk mengirimkan permintaan PATCH -->
<form id="acceptForm" style="display: none;" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <input id="id_atb" name="id_atb" type="hidden">
</form>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            const dokumentasiInput = document.getElementById('dokumentasiInput');
            const dokumentasiPreview = document.getElementById('dokumentasiPreview');
            const invalidFeedback = document.getElementById('dokumentasi-invalid-feedback');
            const largeImagePreviewForAccept = document.getElementById('largeImagePreviewForAccept');
            const imagePreviewTitleForAccept = document.getElementById('imagePreviewTitleForAccept');
            const imagePreviewModalforAccept = new bootstrap.Modal(document.getElementById('imagePreviewModalforAccept'));

            function clearPreview() {
                if (dokumentasiPreview) {
                    dokumentasiPreview.innerHTML = '';
                }
            }

            const validateFiles = () => {
                if (dokumentasiInput.files.length === 0) {
                    dokumentasiInput.classList.add('is-invalid');
                    invalidFeedback.style.display = 'block';
                    return false;
                } else {
                    dokumentasiInput.classList.remove('is-invalid');
                    invalidFeedback.style.display = 'none';
                    return true;
                }
            };

            dokumentasiInput.addEventListener('change', function() {
                clearPreview();

                const files = Array.from(dokumentasiInput.files);
                const maxFileSize = 2 * 1024 * 1024; // 2 MB

                files.forEach((file) => {
                    if (!file.type.startsWith('image/')) {
                        alert(`File "${file.name}" is not an image.`);
                        return;
                    }

                    if (file.size > maxFileSize) {
                        alert(`File "${file.name}" exceeds the 2 MB size limit.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewContainer = document.createElement('div');
                        previewContainer.classList.add('d-flex', 'flex-column', 'align-items-center', 'me-2');

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        img.classList.add('img-thumbnail');
                        img.title = file.name;

                        img.addEventListener('click', function() {
                            largeImagePreviewForAccept.src = e.target.result;
                            imagePreviewTitleForAccept.textContent = file.name;
                            $('#modalForAccept').modal('hide');
                            imagePreviewModalforAccept.show();
                        });

                        const removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.textContent = 'Remove';
                        removeButton.classList.add('btn', 'btn-sm', 'btn-danger', 'mt-2');
                        removeButton.onclick = () => {
                            previewContainer.remove();
                            const remainingFiles = Array.from(dokumentasiInput.files)
                                .filter(f => f !== file);
                            const dataTransfer = new DataTransfer();
                            remainingFiles.forEach(f => dataTransfer.items.add(f));
                            dokumentasiInput.files = dataTransfer.files;
                            validateFiles();
                        };

                        previewContainer.appendChild(img);
                        previewContainer.appendChild(removeButton);
                        dokumentasiPreview.appendChild(previewContainer);
                    };
                    reader.readAsDataURL(file);
                });

                validateFiles();
            });

            document.getElementById('imagePreviewModalforAccept').addEventListener('hidden.bs.modal', function() {
                $('#modalForAccept').modal('show');
            });

            // Add quantity validation
            const quantityInput = document.getElementById('quantity');

            function setQuantityLimits(btn) {
                const max = btn.dataset.max;
                const maxText = btn.dataset.maxText;
                const min = 1;
                quantityInput.setAttribute('max', max);
                quantityInput.setAttribute('min', min);
                quantityInput.value = ''; // Reset value when modal opens
                document.getElementById('maxQuantityPlaceholder').textContent = ` ${maxText}`;
            }

            // Validate quantity
            function validateQuantity() {
                const value = parseInt(quantityInput.value);
                const max = parseInt(quantityInput.getAttribute('max'));
                const min = parseInt(quantityInput.getAttribute('min'));

                if (isNaN(value) || value < min || value > max) {
                    quantityInput.classList.add('is-invalid');
                    return false;
                }
                quantityInput.classList.remove('is-invalid');
                return true;
            }

            // Add quantity validation to existing validation chain
            const validateForm = () => {
                return validateFiles() && validateQuantity();
            };

            // Update quantity on input
            quantityInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                const max = parseInt(this.getAttribute('max'));

                if (value > max) {
                    alert(`Quantity tidak boleh melebihi ${max}`);
                    this.value = max;
                }
            });

            // Update showModalAccept function to include quantity limits
            window.showModalAccept = function(id) {
                const btn = document.querySelector(`.acceptBtn[data-id="${id}"]`);
                setQuantityLimits(btn);
                $('#confirmAcceptButton').data('id', id);
                $('#modalForAccept').modal('show');
            };

            // Update submit handler to include id_atb
            $('#confirmAcceptButton').off('click').on('click', function() {
                if (!validateForm()) {
                    return;
                }

                const id = $(this).data('id');
                const form = document.getElementById('acceptForm');
                form.action = `{{ route('atb.mutasi.accept', ['id' => ':id']) }}`.replace(':id', id);

                // Set id_atb value
                document.getElementById('id_atb').value = id;

                // Create hidden inputs for quantity
                const quantityHidden = document.createElement('input');
                quantityHidden.type = 'hidden';
                quantityHidden.name = 'quantity';
                quantityHidden.value = quantityInput.value;
                form.appendChild(quantityHidden);

                // Create hidden inputs for files
                const files = dokumentasiInput.files;
                for (let i = 0; i < files.length; i++) {
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.name = 'dokumentasi[]';
                    fileInput.style.display = 'none';

                    // Create a new FileList-like object
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[i]);
                    fileInput.files = dataTransfer.files;

                    form.appendChild(fileInput);
                }

                form.submit();
            });
        });

        $(document).on('click', '.acceptBtn', function() {
            const id = $(this).data('id');
            showModalAccept(id);
        });

        function showModalAccept(id) {
            $('#confirmAcceptButton').data('id', id);
            $('#modalForAccept').modal('show');
        }
    </script>
@endpush
