<div class="fade modal" id="modalForDetail" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h1 class="fs-5" id="modalForDetailLabel">Detail Master Data Sparepart</h1>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required" for="nama">Nama Sparepart</label>
                        <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Sparepart" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label required" for="part_number">Part Number</label>
                        <input class="form-control" id="part_number" name="part_number" type="text" placeholder="Part Number" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label required" for="merk">Merk</label>
                        <input class="form-control" id="merk" name="merk" type="text" placeholder="Merk" readonly>
                    </div>

                    <!-- Add Suppliers List if needed -->
                    <div class="col-12">
                        <label class="form-label" for="suppliers">Supplier</label>
                        <ul class="list-group" id="supplierList">
                            <!-- Supplier names will be appended here -->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="modal-footer d-flex w-100 justify-content-end">
                <button class="btn btn-secondary w-25" data-bs-dismiss="modal" type="button">Close</button>
            </div>
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
        // Fungsi untuk menampilkan modal detail dan mengisi data dari server
        function fillFormDetail(id) {
            // Set URL untuk mendapatkan data sparepart berdasarkan ID
            const url = `/master-data-spareparts/${id}`;

            // Lakukan AJAX GET request ke server untuk mengambil data item
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Mengisi nilai input di modal detail dengan data yang diterima
                    $('#modalForDetail #nama').val(response.data.nama);
                    $('#modalForDetail #part_number').val(response.data.part_number);
                    $('#modalForDetail #merk').val(response.data.merk);

                    // Clear previous suppliers and add new list
                    $('#supplierList').empty();
                    if (response.data.suppliers && response.data.suppliers.length > 0) {
                        response.data.suppliers.forEach(supplier => {
                            $('#supplierList').append(`<li class="list-group-item">${supplier.nama}</li>`);
                        });
                    } else {
                        $('#supplierList').append(`<li class="list-group-item">Tidak ada Supplier yang menyediakan Sparepart ini</li>`);
                    }

                    // Tampilkan modal detail
                    $('#modalForDetail').modal('show');
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                }
            });
        }

        // Event listener untuk tombol detail di tabel
        $(document).on('click', '.detailBtn', function() {
            const id = $(this).data('id'); // Ambil ID dari atribut data-id
            fillFormDetail(id); // Panggil fungsi untuk mengisi modal detail
        });
    </script>
@endpush
