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

<div class="fade modal" id="modalForDetail" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalForDetailLabel">Detail Supplier</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required" for="supplier_nama">Nama Supplier</label>
                        <input class="form-control" id="supplier_nama" name="supplier_nama" type="text" placeholder="Nama Supplier" readonly>
                    </div>

                    <!-- List of Spareparts provided by the supplier -->
                    <div class="col-12">
                        <label class="form-label" for="master_data_spareparts">Spareparts</label>
                        <ul class="list-group" id="sparepartList">
                            <!-- Sparepart names will be appended here -->
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
        // Fungsi untuk menampilkan modal detail Supplier dan mengisi data dari server
        function fillFormDetail(id) {
            // Create and append loading overlay
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#modalForDetail').modal('show');
            $('#modalForDetail').append($loadingOverlay);

            // Set URL untuk mendapatkan data supplier berdasarkan ID
            const url = `{{ route('master_data_supplier.show', ':id') }}`.replace(':id', id);

            // Lakukan AJAX GET request ke server untuk mengambil data supplier
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Mengisi nilai input di modal detail dengan data supplier yang diterima
                    $('#modalForDetail #supplier_nama').val(response.data.nama);

                    // Clear previous master_data_spareparts and add new list
                    $('#sparepartList').empty();
                    if (response.data.master_data_spareparts && response.data.master_data_spareparts.length > 0) {
                        response.data.master_data_spareparts.forEach(sparepart => {
                            $('#sparepartList').append(`<li class="list-group-item">${sparepart.nama} — ${sparepart.part_number} — ${sparepart.merk}</li>`);
                        });
                    } else {
                        $('#sparepartList').append(`<li class="list-group-item">Tidak ada Sparepart yang disediakan oleh Supplier ini</li>`);
                    }

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
