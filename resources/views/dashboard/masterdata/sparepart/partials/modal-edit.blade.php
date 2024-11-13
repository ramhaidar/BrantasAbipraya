<div class="fade modal" id="modalForEdit" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h1 class="fs-5" id="modalForEditLabel">Ubah Master Data Sparepart</h1>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="editSparepartForm" novalidate method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="nama">Nama Sparepart</label>
                            <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Sparepart" required>
                            <div class="invalid-feedback">Nama Sparepart diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="part_number">Part Number</label>
                            <input class="form-control" id="part_number" name="part_number" type="text" placeholder="Part Number" required>
                            <div class="invalid-feedback">Part Number diperlukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" for="merk">Merk</label>
                            <input class="form-control" id="merk" name="merk" type="text" placeholder="Merk" required>
                            <div class="invalid-feedback">Merk diperlukan.</div>
                        </div>

                        <!-- Multi-select for Supplier -->
                        <div class="col-12">
                            <label class="form-label" for="suppliers">Supplier</label>
                            <select class="form-control" id="edit_suppliers" name="suppliers[]" multiple="multiple">
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Supplier diperlukan.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" data-bs-dismiss="modal" type="button">Batal</button>
                    <button class="btn btn-primary w-25" id="update-sparepart" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles_3')
    <style>
        /* CSS for required asterisk */
        .form-label.required::after {
            content: " *";
            color: red;
            font-weight: bold;
            margin-left: 2px;
        }
    </style>
@endpush

@push('scripts_3')
    <script>
        (() => {
            'use strict'

            // Form edit untuk validasi
            const editForm = document.querySelector('#editSparepartForm');

            // Validasi saat submit form
            editForm.addEventListener('submit', (event) => {
                if (!editForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                editForm.classList.add('was-validated');
            });

            // Validasi saat blur (out of focus) untuk setiap input di form edit
            editForm.querySelectorAll('input').forEach((input) => {
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
    </script>

    <script>
        // Initialize Select2 for multi-select in edit modal
        $('#edit_suppliers').select2({
            placeholder: "Pilih Supplier",
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#modalForEdit'),
            width: '100%'
        });

        // Function to display modal for editing and populate form with server data
        function fillFormEdit(id) {
            const url = "{{ route('master_data_sparepart.update', ':id') }}".replace(':id', id);

            // AJAX GET request to fetch sparepart data along with selected suppliers
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Populate fields with data
                    $('#editSparepartForm #nama').val(response.data.nama);
                    $('#editSparepartForm #part_number').val(response.data.part_number);
                    $('#editSparepartForm #merk').val(response.data.merk);

                    // Set selected suppliers
                    const selectedSuppliers = response.data.suppliers.map(supplier => supplier.id);
                    $('#edit_suppliers').val(selectedSuppliers).trigger('change');

                    // Set action form to update the specific item with PUT method
                    $('#editSparepartForm').attr('action', url);

                    // Display the edit modal
                    $('#modalForEdit').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                }
            });
        }

        // Event listener untuk tombol edit di tabel
        $(document).on('click', '.ubahBtn', function() {
            const id = $(this).data('id'); // Ambil ID dari atribut data-id
            fillFormEdit(id); // Panggil fungsi untuk mengisi form edit
        });
    </script>
@endpush
