<div class="fade modal" id="modalForEdit" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Data Pekerjaan</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editJobForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <input id="id_job" name="id_job" type="hidden">

                        <div class="col-12">
                            <label class="form-label required" for="uraian_pekerjaan_edit">Uraian Pekerjaan</label>
                            <input class="form-control" id="uraian_pekerjaan_edit" name="uraian_pekerjaan" type="text" placeholder="Masukkan uraian pekerjaan" required>
                            <div class="invalid-feedback">Uraian Pekerjaan diperlukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="tanggal_awal_rencana_edit">Tanggal Awal Rencana</label>
                            <input class="form-control datepicker" id="tanggal_awal_rencana_edit" name="tanggal_awal_rencana" type="text" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal Awal Rencana diperlukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="tanggal_akhir_rencana_edit">Tanggal Akhir Rencana</label>
                            <input class="form-control datepicker" id="tanggal_akhir_rencana_edit" name="tanggal_akhir_rencana" type="text" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal Akhir Rencana diperlukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="tanggal_awal_actual_edit">Tanggal Awal Actual</label>
                            <input class="form-control datepicker" id="tanggal_awal_actual_edit" name="tanggal_awal_actual" type="text" autocomplete="off">
                            <div class="invalid-feedback">Tanggal Awal Actual diperlukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="tanggal_akhir_actual_edit">Tanggal Akhir Actual</label>
                            <input class="form-control datepicker" id="tanggal_akhir_actual_edit" name="tanggal_akhir_actual" type="text" autocomplete="off">
                            <div class="invalid-feedback">Tanggal Akhir Actual diperlukan.</div>
                        </div>

                        {{-- <div class="col-12">
                            <label class="form-label required" for="status">Status</label>
                            <select class="form-control select2" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Sudah Selesai">Sudah Selesai</option>
                                <option value="Belum Selesai">Belum Selesai</option>
                            </select>
                            <div class="invalid-feedback">Status diperlukan.</div>
                        </div> --}}

                    </div>
                </div>
                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-job" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            'use strict';

            $('#editJobForm').on('submit', function() {
                $('#update-job').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            });

            // Initialize datepicker
            const dateFormat = 'yy-mm-dd';
            const options = {
                dateFormat: dateFormat,
                changeMonth: true,
                changeYear: true,
                // minDate: 0,
                regional: 'id'
            };

            $('#tanggal_awal_rencana_edit').datepicker(options).on('change', function() {
                const minDate = $(this).datepicker('getDate');
                $('#tanggal_akhir_rencana_edit').datepicker('option', 'minDate', minDate);
            });

            $('#tanggal_akhir_rencana_edit').datepicker(options).on('change', function() {
                const maxDate = $(this).datepicker('getDate');
                $('#tanggal_awal_rencana_edit').datepicker('option', 'maxDate', maxDate);
            });

            $('#tanggal_awal_actual_edit').datepicker(options).on('change', function() {
                const minDate = $(this).datepicker('getDate');
                $('#tanggal_akhir_actual_edit').datepicker('option', 'minDate', minDate);
            });

            $('#tanggal_akhir_actual_edit').datepicker(options).on('change', function() {
                const maxDate = $(this).datepicker('getDate');
                $('#tanggal_awal_actual_edit').datepicker('option', 'maxDate', maxDate);
            });

            $.datepicker.setDefaults($.datepicker.regional['id']);

            // Event listener for edit buttons
            $(document).on('click', '.editBtn', function() {
                const id = $(this).data('id'); // Get ID from the button
                fillFormEditJob(id); // Populate and show the modal
            });
        });

        // Function to display modal for editing and populate form with server data
        function fillFormEditJob(id) {
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#modalForEdit').modal('show');
            $('#modalForEdit').append($loadingOverlay);

            const showUrl = "{{ route('rkb_urgent.detail.timeline.show', ':id') }}".replace(':id', id);
            const updateUrl = "{{ route('rkb_urgent.detail.timeline.update', ':id') }}".replace(':id', id);

            // AJAX GET request to fetch data
            $.ajax({
                url: showUrl,
                type: 'GET',
                success: function(response) {
                    // Format date to yyyy-mm-dd
                    const formatDate = (dateStr) => {
                        const date = new Date(dateStr);
                        const year = date.getFullYear();
                        const month = ('0' + (date.getMonth() + 1)).slice(-2);
                        const day = ('0' + date.getDate()).slice(-2);
                        return `${year}-${month}-${day}`;
                    };

                    // Populate fields with data
                    $('#id_job').val(response.data.id);
                    $('#uraian_pekerjaan_edit').val(response.data.nama_rencana);
                    $('#tanggal_awal_rencana_edit').val(formatDate(response.data.tanggal_awal_rencana));
                    $('#tanggal_akhir_rencana_edit').val(formatDate(response.data.tanggal_akhir_rencana));

                    if (response.data.tanggal_awal_actual) {
                        $('#tanggal_awal_actual_edit').val(formatDate(response.data.tanggal_awal_actual));
                    } else {
                        $('#tanggal_awal_actual_edit').val('');
                    }

                    if (response.data.tanggal_akhir_actual) {
                        $('#tanggal_akhir_actual_edit').val(formatDate(response.data.tanggal_akhir_actual));
                    } else {
                        $('#tanggal_akhir_actual_edit').val('');
                    }

                    // $('#status').val(response.data.is_done ? 'Sudah Selesai' : 'Belum Selesai').trigger('change');

                    // Set action form to update the specific record with PUT method
                    $('#editJobForm').attr('action', updateUrl);

                    // Display the edit modal
                    $('#modalForEdit').modal('show');
                    $loadingOverlay.remove();
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                    $loadingOverlay.remove();
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#status').select2({
                placeholder: "Pilih Status",
                allowClear: true,
                dropdownParent: $('#modalForEdit'),
                width: '100%'
            });
        });
    </script>
@endpush
