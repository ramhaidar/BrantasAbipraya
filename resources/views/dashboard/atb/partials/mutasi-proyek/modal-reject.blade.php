<div class="fade modal" id="modalForReject" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForRejectLabel">Konfirmasi Tolak Mutasi</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <p class="p-0 m-0">Apakah Anda yakin ingin menolak mutasi ini?</p>
                <p class="p-0 m-0">Tindakan ini tidak dapat dibatalkan!</p>

                <div class="mt-3">
                    <label class="form-label required" for="tanggal_tolak">Tanggal Penolakan</label>
                    <input class="form-control datepicker" id="tanggal_tolak" name="tanggal_tolak" type="text" autocomplete="off" placeholder="Tanggal Penolakan" required>
                    <div class="invalid-feedback">Tanggal Penolakan diperlukan.</div>
                </div>
            </div>
            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger w-25" id="confirmRejectButton">Tolak</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form untuk mengirimkan permintaan PATCH -->
<form id="rejectForm" style="display: none;" method="POST">
    @csrf
    @method('PATCH')
    <input id="tanggal_tolak_hidden" name="tanggal_tolak" type="hidden">
</form>

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            // Check if the current user is a koordinator_proyek
            const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};

            // Destroy existing datepicker to reinitialize with our settings
            $('#tanggal_tolak').datepicker('destroy');

            if (isKoordinatorProyek) {
                // ONLY FOR KOORDINATOR PROYEK - Apply date restrictions
                const today = new Date();
                const startDate = new Date(today.getFullYear(), today.getMonth() - 1, 26);
                const endDate = new Date(today.getFullYear(), today.getMonth(), 25);

                const formatDate = (date) => {
                    return date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear();
                };

                $('#tanggal_tolak').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    minDate: startDate,
                    maxDate: endDate,
                    beforeShowDay: function(date) {
                        return [date >= startDate && date <= endDate, ''];
                    },
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set initial date
                let initialDate;
                if (today >= startDate && today <= endDate) {
                    initialDate = today;
                } else if (today > endDate) {
                    initialDate = endDate;
                } else {
                    initialDate = startDate;
                }
                $('#tanggal_tolak').datepicker('setDate', initialDate);

                // Update placeholder and validation message
                $('#tanggal_tolak').attr('placeholder',
                    `Tanggal antara ${formatDate(startDate)} - ${formatDate(endDate)}`);
                $('#tanggal_tolak').closest('div').find('.invalid-feedback').text(
                    `Tanggal harus antara ${formatDate(startDate)} - ${formatDate(endDate)}`);

                // Date validation
                $('#tanggal_tolak').on('change', function() {
                    try {
                        const input = $(this).val();
                        if (!input) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const parts = input.split('-');
                        if (parts.length !== 3) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
                            return false;
                        }

                        const inputDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));

                        if (isNaN(inputDate) || inputDate < startDate || inputDate > endDate) {
                            $(this).removeClass('is-valid').addClass('is-invalid');
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
                // FOR OTHER ROLES - Initialize datepicker without restrictions
                $('#tanggal_tolak').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText) {
                        $(this).change();
                        $(this).removeClass('is-invalid').addClass('is-valid');
                    }
                });

                // Set today as default date
                $('#tanggal_tolak').datepicker('setDate', new Date());
            }
        });

        $(document).on('click', '.rejectBtn', function() {
            const id = $(this).data('id');
            showModalReject(id);
        });

        function showModalReject(id) {
            $('#confirmRejectButton').data('id', id);
            $('#modalForReject').modal('show');
        }

        $('#confirmRejectButton').on('click', function() {
            // Validate date for koordinator_proyek
            const isKoordinatorProyek = {{ auth()->user()->role === 'koordinator_proyek' ? 'true' : 'false' }};
            const tanggalValid = isKoordinatorProyek ? $('#tanggal_tolak').hasClass('is-valid') : true;

            if (!tanggalValid) {
                return;
            }

            const id = $(this).data('id');
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            // Set the date value in the hidden form
            $('#tanggal_tolak_hidden').val($('#tanggal_tolak').val());

            rejectWithForm(id);
        });

        function rejectWithForm(id) {
            const form = document.getElementById('rejectForm');
            form.action = `{{ route('atb.mutasi.reject', ['id' => ':id']) }}`.replace(':id', id);
            form.submit();
        }
    </script>
@endpush
