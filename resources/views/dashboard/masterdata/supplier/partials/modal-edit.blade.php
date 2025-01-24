<div class="fade modal" id="modalForEdit" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForEditLabel">Ubah Master Data Supplier</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editSupplierForm" style="overflow-y: auto" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Nama Supplier -->
                        <div class="col-12">
                            <label class="form-label required" for="nama">Nama Supplier</label>
                            <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Supplier" required>
                            <div class="invalid-feedback">Nama Supplier diperlukan.</div>
                        </div>

                        <!-- Alamat -->
                        <div class="col-12">
                            <label class="form-label required" for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat" required></textarea>
                            <div class="invalid-feedback">Alamat diperlukan.</div>
                        </div>

                        <!-- Contact Person -->
                        <div class="col-12">
                            <label class="form-label required" for="contact_person">Contact Person</label>
                            <input class="form-control" id="contact_person" name="contact_person" type="text" placeholder="Contact Person" required>
                            <div class="invalid-feedback">Contact Person diperlukan.</div>
                        </div>

                        <!-- Multi-select for Spareparts -->
                        <div class="col-12">
                            <label class="form-label" for="spareparts">Spareparts</label>
                            <select class="form-control" id="edit_spareparts" name="spareparts[]" multiple="multiple">
                                @foreach ($spareparts as $sparepart)
                                    <option value="{{ $sparepart->id }}">{{ $sparepart->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Sparepart diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-supplier" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles_3')
@endpush

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            // Form edit untuk validasi
            const editForm = document.querySelector('#editSupplierForm');

            // Validasi saat submit form
            editForm.addEventListener('submit', (event) => {
                if (!editForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                editForm.classList.add('was-validated');
            });

            // Validasi saat blur (out of focus) untuk setiap input di form edit
            editForm.querySelectorAll('input, textarea').forEach((input) => {
                input.addEventListener('blur', () => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    }
                });
            });
        })();

        // Initialize Select2 for multi-select in edit modal
        const editSpareparts = $('#edit_spareparts').select2({
            placeholder: "Pilih Sparepart",
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#modalForEdit'),
            width: '100%'
        });

        // Function to display modal for editing and populate form with server data
        function fillFormEdit(id) {
            const url = "{{ route('master_data_supplier.show', ':id') }}".replace(':id', id);

            // Clear previous selections
            editSpareparts.val(null).trigger('change');

            // AJAX GET request to fetch supplier data along with selected spareparts
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Populate fields with data
                    $('#editSupplierForm #nama').val(response.data.nama);
                    $('#editSupplierForm #alamat').val(response.data.alamat);
                    $('#editSupplierForm #contact_person').val(response.data.contact_person);

                    // Set selected spareparts
                    if (response.data.master_data_spareparts && response.data.master_data_spareparts.length > 0) {
                        const selectedSpareparts = response.data.master_data_spareparts.map(sparepart => sparepart.id);
                        editSpareparts.val(selectedSpareparts).trigger('change');
                    }

                    // Set action form to update the specific supplier with PUT method
                    $('#editSupplierForm').attr('action', "{{ route('master_data_supplier.update', ':id') }}".replace(':id', id));

                    // Display the edit modal
                    $('#modalForEdit').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                }
            });
        }

        // Reset form when modal is hidden
        $('#modalForEdit').on('hidden.bs.modal', function() {
            $('#editSupplierForm').removeClass('was-validated');
            editSpareparts.val(null).trigger('change');
        });
    </script>
@endpush
