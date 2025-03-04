<div class="fade modal" id="modalForAdd" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForAddLabel">Tambah Data ATB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="w-100 align-items-center flex-column gap-0 overflow-auto needs-validation" id="addDataFormNormal" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
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
                            <label class="form-label required" for="tanggal_normal">Tanggal Masuk Sparepart</label>
                            <input class="form-control datepicker" id="tanggal_normal" name="tanggal" type="text" autocomplete="off" placeholder="Tanggal Masuk Sparepart (26 bulan kemarin - 25 bulan ini)" required>
                            <div class="invalid-feedback">Tanggal Masuk Sparepart diperlukan (26 bulan kemarin - 25 bulan ini).</div>
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
                <button class="btn btn-secondary me-2 w-25" id="resetButtonNormal" type="button">Reset</button>
                <button class="btn btn-success w-25" id="submitButtonNormal" type="button">Tambah Data</button>
            </div>

        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.3.0/pdfobject.min.js" integrity="sha512-Nr6NV16pWOefJbWJiT8SrmZwOomToo/84CNd0MN6DxhP5yk8UAoPUjNuBj9KyRYVpESUb14RTef7FKxLVA4WGQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            // Check if the current user is a koordinator_proyek based on position attribute
            const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};

            // Destroy existing datepicker to reinitialize with our settings
            $('#tanggal_normal').datepicker('destroy');

            if (isKoordinatorProyek) {
                // ONLY FOR KOORDINATOR PROYEK - Apply date restrictions
                // Calculate valid date range (26th of previous month to 25th of current month)
                const today = new Date();
                const startDate = new Date(today.getFullYear(), today.getMonth() - 1, 26);
                const endDate = new Date(today.getFullYear(), today.getMonth(), 25);

                // Format dates for display
                const formatDate = (date) => {
                    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
                };

                // Initialize the datepicker with strict constraints
                $('#tanggal_normal').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    minDate: startDate,
                    maxDate: endDate,
                    beforeShowDay: function(date) {
                        // Only allow dates in the specific range
                        return [date >= startDate && date <= endDate, ''];
                    },
                    onSelect: function(dateText) {
                        $(this).change();
                        // Add validation visual feedback
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set initial date - if today is in range, use today, otherwise use the closest valid date
                let initialDate;
                if (today >= startDate && today <= endDate) {
                    initialDate = today;
                } else if (today > endDate) {
                    initialDate = endDate;
                } else {
                    initialDate = startDate;
                }
                $('#tanggal_normal').datepicker('setDate', initialDate);

                // Update placeholder to show valid range
                $('#tanggal_normal').attr('placeholder',
                    `Tanggal antara ${formatDate(startDate)} - ${formatDate(endDate)}`);
                $('.invalid-feedback').first().text(
                    `Tanggal harus antara ${formatDate(startDate)} - ${formatDate(endDate)}`);

                // Custom validation to enforce date range on direct input
                $('#tanggal_normal').on('change', function() {
                    try {
                        const input = $(this).val();
                        if (!input) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        // Parse the input date 
                        const parts = input.split('-');
                        if (parts.length !== 3) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const inputDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));

                        // Check if date is within range
                        if (isNaN(inputDate) || inputDate < startDate || inputDate > endDate) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            // Reset to a valid date
                            $(this).datepicker('setDate', initialDate);
                            return false;
                        } else {
                            $(this).removeClass('is-invalid').addClass('is-valid');
                            return true;
                        }
                    } catch (e) {
                        console.error('Date validation error:', e);
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                });
            } else {
                // FOR OTHER ROLES - Initialize the datepicker without date restrictions
                $('#tanggal_normal').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set today as the default date
                $('#tanggal_normal').datepicker('setDate', new Date());
            }

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
                            // console.log('Data fetched:', response);

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
            $('#resetButtonNormal').on('click', function() {
                $('#addDataFormNormal')[0].reset();
                $('#tableContainer').empty();
                $('#id_spb').val(null).trigger('change');
                // Remove validation classes on reset
                $('.is-invalid').removeClass('is-invalid');
                $('.was-validated').removeClass('was-validated');
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
            $('#submitButtonNormal').on('click', function() {
                if ($('#addDataFormNormal')[0].checkValidity() && validateSelect2()) {
                    $('#addDataFormNormal').submit();
                } else {
                    $('#addDataFormNormal').addClass('was-validated');
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
            const suratTandaTerimaInput = $('#surat_tanda_terima');
            const pdfPreviewContainer = $('#pdfPreviewContainer');
            const pdfPreview = $('#pdfPreview');
            const resetButton = $('#resetButtonNormal');

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
            const form = $('#addDataFormNormal');

            // Check if the current user is a koordinator_proyek
            const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};

            // Reset previous validation states
            $('.is-invalid').removeClass('is-invalid');

            // Check items with quantity > 0
            $('.quantity-input').each(function() {
                const qty = parseInt($(this).val()) || 0;
                const row = $(this).closest('tr');
                const fotoInput = row.find('input[type="file"]');

                if (qty > 0 && (!fotoInput.length || !fotoInput[0].files.length)) {
                    fotoInput.addClass('is-invalid');
                    isValid = false;
                }
            });

            // Validate date field
            const tanggal = $('#tanggal_normal');
            if (!tanggal.val()) {
                tanggal.addClass('is-invalid');
                isValid = false;
            } else if (isKoordinatorProyek) {
                // ONLY FOR KOORDINATOR PROYEK - Validate date range
                // Calculate valid date range
                const today = new Date();
                const startDate = new Date(today.getFullYear(), today.getMonth() - 1, 26);
                const endDate = new Date(today.getFullYear(), today.getMonth(), 25);

                // Parse the input date
                try {
                    const parts = tanggal.val().split('-');
                    if (parts.length === 3) {
                        const selectedDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));

                        if (isNaN(selectedDate) || selectedDate < startDate || selectedDate > endDate) {
                            tanggal.addClass('is-invalid');
                            isValid = false;
                        }
                    } else {
                        tanggal.addClass('is-invalid');
                        isValid = false;
                    }
                } catch (e) {
                    console.error('Date validation error:', e);
                    tanggal.addClass('is-invalid');
                    isValid = false;
                }
            }

            // Existing validation code...
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
                // Show error message, with customized text based on user role
                let errorText = 'Mohon lengkapi semua field yang wajib diisi.';
                if (isKoordinatorProyek) {
                    errorText += ' Pastikan tanggal masuk sparepart antara tanggal 26 bulan kemarin sampai tanggal 25 bulan ini.';
                }

                Swal.fire({
                    title: 'Error!',
                    text: errorText,
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }

            return isValid;
        }

        $(document).ready(function() {
            // Replace the existing submit button click handler
            $('#submitButtonNormal').on('click', function() {
                if (validateForm()) {
                    $('#addDataFormNormal').submit();
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

    <script>
        $(document).on('change', '.quantity-input', function() {
            const qty = parseInt($(this).val()) || 0;
            const row = $(this).closest('tr');
            const fotoInput = row.find('input[type="file"]');

            if (qty > 0) {
                fotoInput.prop('required', true);
            } else {
                fotoInput.prop('required', false);
                fotoInput.removeClass('is-invalid');
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Replace multiple validation handlers with a single submit handler
            $('#submitButtonNormal').on('click', function(e) {
                e.preventDefault();
                const form = $('#addDataFormNormal');
                const submitButton = $(this);

                // Add Bootstrap's validation class
                form.addClass('was-validated');

                if (validateForm() && validateSelect2()) {
                    // Disable button and show loading spinner
                    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    form.submit();
                } else {
                    // Re-enable button if validation fails
                    submitButton.prop('disabled', false).html('Tambah Data');
                }
            });

            // Reset button should also reset the submit button state and set a valid date
            $('#resetButtonNormal').on('click', function() {
                $('#submitButtonNormal').prop('disabled', false).html('Tambah Data');

                // Check if the current user is a koordinator_proyek
                const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};

                if (isKoordinatorProyek) {
                    // FOR KOORDINATOR PROYEK - Check for valid range and set appropriate date
                    const today = new Date();
                    const range = {
                        startDate: new Date(today.getFullYear(), today.getMonth() - 1, 26),
                        endDate: new Date(today.getFullYear(), today.getMonth(), 25)
                    };

                    let dateToSet;
                    if (today >= range.startDate && today <= range.endDate) {
                        dateToSet = today;
                    } else if (today > range.endDate) {
                        dateToSet = range.endDate;
                    } else {
                        dateToSet = range.startDate;
                    }

                    $('#tanggal_normal').datepicker('setDate', dateToSet);
                } else {
                    // FOR OTHER ROLES - Just set today's date
                    $('#tanggal_normal').datepicker('setDate', new Date());
                }
            });
        });
    </script>
@endpush
