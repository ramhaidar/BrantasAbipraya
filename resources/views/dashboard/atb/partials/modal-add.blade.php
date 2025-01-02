@push('styles_3')
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endpush

<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data ATB</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto" id="addDataForm" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="p-0 m-0" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <div class="input-group">
                                <select class="form-control" id="pilihan-proyek1" name="tipe">
                                    <option value="hutang-unit-alat" {{ $page == 'Data ATB Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                    <option value="panjar-unit-alat" {{ $page == 'Data ATB Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                    <option value="mutasi-proyek" {{ $page == 'Data ATB Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                    <option value="panjar-proyek" {{ $page == 'Data ATB Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                                </select>
                            </div>
                        </div>

                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>

                        <div class="col-12">
                            <label class="form-label required" for="tanggal">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal" name="tanggal" type="text" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" autocomplete="off" placeholder="Tanggal Masuk Sparepart" required>
                            <div class="invalid-feedback">Tanggal Masuk Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="id_spb">Pilih SPB</label>
                            <select class="form-control" id="id_spb" name="id_spb" required>
                                <option value="">Pilih SPB</option>
                                @foreach ($spbs as $spb)
                                    <option value="{{ $spb->id }}">{{ $spb->nomor }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">SPB diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="surat_tanda_terima">Upload Surat Tanda Terima</label>
                            <input class="form-control" id="surat_tanda_terima" name="surat_tanda_terima" type="file" required accept="application/pdf">
                            <div class="invalid-feedback">Surat Tanda Terima diperlukan.</div>
                        </div>

                        <div class="col-12 mt-3" id="pdfPreviewContainer" style="display: none;">
                            <label class="form-label">Pratinjau PDF:</label>
                            <div id="pdfPreview" style="border: 1px solid #ccc; width: 100%; height: 500px;"></div>
                        </div>

                        <!-- Include the partials table for selected SPB details -->
                        <div class="col-12" id="tableContainer">
                            <!-- Table will be populated dynamically -->
                        </div>

                    </div>
                </div>
            </form>

            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" id="resetButton" type="button">Reset</button>
                <button class="btn btn-success w-25" id="submitButton" type="button">Tambah Data</button>
            </div>

        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js" integrity="sha512-Nr6NV16pWOefJbWJiT8SrmZwOomToo/84CNd0MN6DxhP5yk8UAoPUjNuBj9KyRYVpESUb14RTef7FKxLVA4WGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 with options
            $('#id_master_data_alat').select2({
                placeholder: "Pilih",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Initialize Select2 for #pilihan-kode1
            $('#pilihan-kode1').select2({
                placeholder: "Pilih Kode",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            });

            // Initialize Select2 for #id_spb
            $('#id_spb').select2({
                placeholder: "Pilih SPB",
                allowClear: true,
                dropdownParent: $('#modalForAdd'),
                width: '100%'
            }).on("select2:select select2:unselect", function() {
                validateSelect2();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize datepicker for #tanggal
            var dateFormat = 'yy-mm-dd';
            var options = {
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                regional: 'id'
            };

            $('#tanggal').datepicker(options);
            $.datepicker.setDefaults($.datepicker.regional['id']);
        });
    </script>

    <script>
        $(document).ready(function() {
            // Fetch all forms we want to apply validation to
            const form = document.querySelector('#alatForm');

            if (form) {
                // Apply Bootstrap validation on form submit
                form.addEventListener('submit', (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });

                // Apply validation on blur (out of focus) for each input in the form
                form.querySelectorAll('input, select').forEach((input) => {
                    input.addEventListener('blur', () => {
                        if (!input.checkValidity()) {
                            input.classList.add('is-invalid');
                        } else {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        }
                    });
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Fetch data when SPB is selected
            $('#id_spb').on('change', function() {
                const selectedValue = $(this).val();
                const tableContainer = $('#tableContainer');
                const loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                if (selectedValue) {
                    $('body').append(loadingOverlay);
                    $.ajax({
                        url: `/atb/getlinkSpbDetailSpbs/${selectedValue}`,
                        type: 'GET',
                        success: function(response) {
                            tableContainer.html(response.html);
                            $('#table-data-modal-add').DataTable({
                                paging: false,
                                ordering: false,
                                info: false,
                                searching: false,
                                responsive: true,
                                dom: '<"top"fl>t<"bottom"ip>',
                            });
                        },
                        error: function(error) {
                            console.error('Error fetching data:', error);
                            tableContainer.html('<p>Failed to load data. Please try again.</p>');
                        },
                        complete: function() {
                            loadingOverlay.remove();
                        }
                    });
                } else {
                    tableContainer.empty();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Reset form and clear table on reset button click
            $('#resetButton').on('click', function() {
                $('#addDataForm')[0].reset();
                $('#tableContainer').empty();
                $('#id_spb').val(null).trigger('change');
            });
        });
    </script>

    <script>
        function validateSelect2() {
            let isValid = true;

            // Validate SPB
            const spb = $('#id_spb');
            if (spb.val() === "" || spb.val() === null) {
                spb.next('.select2').find('.select2-selection').addClass('is-invalid');
                spb.closest('.form-group').find('.invalid-feedback').show();
                isValid = false;
            } else {
                spb.next('.select2').find('.select2-selection').removeClass('is-invalid');
                spb.closest('.form-group').find('.invalid-feedback').hide();
            }

            return isValid;
        }

        $(document).ready(function() {
            $('#submitButton').on('click', function() {
                if ($('#addDataForm')[0].checkValidity() && validateSelect2()) {
                    $('#addDataForm').submit();
                } else {
                    $('#addDataForm').addClass('was-validated');
                }
            });

            // Remove validation classes on change
            $('#id_spb').on('change', function() {
                if ($(this).val() !== '') {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Alert and set value to max if quantity_diterima exceeds max
            $(document).on('blur', '.quantity-input', function() {
                const max = $(this).attr('max');
                const value = $(this).val();
                if (parseInt(value) > parseInt(max)) {
                    alert('Quantity diterima tidak boleh melebihi Quantity Belum Diterima.');
                    $(this).val(max);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const suratTandaTerimaInput = $('#surat_tanda_terima');
            const pdfPreviewContainer = $('#pdfPreviewContainer');
            const pdfPreview = $('#pdfPreview');
            const resetButton = $('#resetButton');

            suratTandaTerimaInput.on('change', function() {
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
                    const embedded = PDFObject.embed(fileURL, '#pdfPreview', options);

                    if (embedded) {
                        pdfPreviewContainer.show();
                    } else {
                        console.error('PDF embedding failed.');
                        alert('Gagal menampilkan pratinjau PDF. Silakan coba lagi.');
                        pdfPreviewContainer.hide();
                    }
                } else {
                    alert('Silakan unggah file PDF yang valid.');
                    suratTandaTerimaInput.val('');
                    pdfPreviewContainer.hide();
                }
            });

            resetButton.on('click', function() {
                suratTandaTerimaInput.val('');
                pdfPreviewContainer.hide();
                const previousUrl = pdfPreview.data('fileUrl');
                if (previousUrl) {
                    URL.revokeObjectURL(previousUrl);
                    pdfPreview.removeData('fileUrl');
                }
            });

            suratTandaTerimaInput.on('input', function() {
                if (!this.value) {
                    pdfPreviewContainer.hide();
                }
            });
        });
    </script>

    <script>
        function validateForm() {
            let isValid = true;
            const form = $('#addDataForm');

            // Reset previous validation states
            $('.is-invalid').removeClass('is-invalid');

            // Validate date field
            const tanggal = $('#tanggal');
            if (!tanggal.val()) {
                tanggal.addClass('is-invalid');
                isValid = false;
            }

            // Validate SPB select
            const spb = $('#id_spb');
            if (!spb.val()) {
                spb.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                isValid = false;
            }

            // Validate file upload
            const suratTandaTerima = $('#surat_tanda_terima');
            if (!suratTandaTerima[0].files.length) {
                suratTandaTerima.addClass('is-invalid');
                isValid = false;
            }

            // Check if any required fields are empty
            form.find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                // Show error message
                Swal.fire({
                    title: 'Error!',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }

            return isValid;
        }

        $(document).ready(function() {
            // Replace the existing submit button click handler
            $('#submitButton').on('click', function() {
                if (validateForm()) {
                    $('#addDataForm').submit();
                }
            });

            // Add validation on input change
            $('input, select').on('change', function() {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });

            // Add validation for Select2 fields
            $('#id_spb').on('change', function() {
                if ($(this).val()) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }
            });
        });
    </script>
@endpush
