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
                        <label class="form-label" for="nama">Nama Sparepart</label>
                        <input class="form-control" id="nama" name="nama" type="text" placeholder="Nama Sparepart" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="part_number">Part Number</label>
                        <input class="form-control" id="part_number" name="part_number" type="text" placeholder="Part Number" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="merk">Merk</label>
                        <input class="form-control" id="merk" name="merk" type="text" placeholder="Merk" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="kategori">Kategori</label>
                        <input class="form-control" id="kategori" name="kategori" type="text" placeholder="Kategori" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="kode">Kode</label>
                        <input class="form-control" id="kode" name="kode" type="text" placeholder="Kode" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="jenis">Jenis</label>
                        <input class="form-control" id="jenis" name="jenis" type="text" placeholder="Jenis" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="sub_jenis">Sub Jenis</label>
                        <input class="form-control" id="sub_jenis" name="sub_jenis" type="text" placeholder="---" readonly>
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

@push('scripts_3')
    <script>
        // Fungsi untuk menampilkan modal detail dan mengisi data dari server
        function fillFormDetail(id) {
            // Set URL untuk mendapatkan data sparepart berdasarkan ID
            const url = `{{ route('master_data_sparepart.show', ':id') }}`.replace(':id', id);

            // Lakukan AJAX GET request ke server
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Mengisi nilai input di modal detail dengan data yang diterima
                    $('#modalForDetail #nama').val(response.data.nama);
                    $('#modalForDetail #part_number').val(response.data.part_number);
                    $('#modalForDetail #merk').val(response.data.merk);

                    // Mengisi data kategori, kode, jenis, dan sub jenis
                    $('#modalForDetail #kategori').val(response.data.kategori ? response.data.kategori.nama : 'Tidak ada kategori');
                    $('#modalForDetail #kode').val(response.data.kategori ? response.data.kategori.kode : 'Tidak ada kode');
                    $('#modalForDetail #jenis').val(response.data.kategori ? response.data.kategori.jenis : 'Tidak ada jenis');
                    $('#modalForDetail #sub_jenis').val(response.data.kategori ? response.data.kategori.sub_jenis : 'Tidak ada sub jenis');

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
