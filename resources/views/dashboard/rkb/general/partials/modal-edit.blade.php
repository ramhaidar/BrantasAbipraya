@push('styles_3')
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1060;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endpush

<div class="fade modal" id="modalForEdit" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Data RKB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editRKBForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="nomor">Nomor RKB</label>
                            <input class="form-control" id="nomor" name="nomor" type="text" placeholder="Nomor RKB" required>
                            <div class="invalid-feedback">Nomor RKB diperlukan.</div>
                        </div>
                        <!-- Single-select for Proyek -->
                        <div class="col-12">
                            <label class="form-label required" for="proyek">Proyek</label>
                            <select class="form-control" id="proyek2" name="proyek" required>
                                <option value="">Pilih Proyek</option>
                                @foreach ($proyeks as $proyek)
                                    <option value="{{ $proyek->id }}">{{ $proyek->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Proyek diperlukan.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label required" for="periode">Periode</label>
                            <input class="form-control" id="periode2" name="periode" type="month" placeholder="Periode" required>
                            <div class="invalid-feedback">Periode diperlukan.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-rkb" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

    <script>
        $(document).ready(function() {
            'use strict';

            // Form edit untuk validasi
            const editForm = document.querySelector('#editRKBForm');

            // Validasi saat submit form
            editForm.addEventListener('submit', (event) => {
                if (!editForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                editForm.classList.add('was-validated');
            });

            // Validasi saat blur (out of focus) untuk setiap input di form edit
            editForm.querySelectorAll('input, select').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });

            // Initialize Select2 for Proyek
            $('#proyek2').select2({
                placeholder: "Pilih Proyek",
                allowClear: true,
                dropdownParent: $('#modalForEdit'),
                width: '100%'
            });

            document.getElementById('periode2').addEventListener('click', function() {
                this.showPicker();
            })
        });

        // Function to display modal for editing and populate form with server data
        function fillFormEditRKB(id) {
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#modalForEdit').modal('show');
            $('#modalForEdit').append($loadingOverlay);

            const url = "{{ route('rkb_general.show', ':id') }}".replace(':id', id);
            moment.locale('id');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#editRKBForm #nomor').val(response.data.nomor);
                    const formattedPeriode = moment(response.data.periode).format('YYYY-MM');
                    $('#editRKBForm #periode2').val(formattedPeriode);
                    $('#editRKBForm #proyek2').val(response.data.proyek.id).trigger('change');
                    $('#editRKBForm').attr('action', url);

                    $loadingOverlay.remove();
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                    $loadingOverlay.remove();
                }
            });
        }
    </script>
@endpush
