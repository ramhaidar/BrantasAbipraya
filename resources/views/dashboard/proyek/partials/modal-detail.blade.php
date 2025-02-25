<div class="fade modal" id="modalDetailProyek" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2" id="modalDetailProyekLabel">Detail Proyek</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required" for="proyek_nama">Nama Proyek</label>
                        <input class="form-control" id="proyek_nama" name="proyek_nama" type="text" placeholder="Nama Proyek" readonly>
                    </div>

                    <!-- List of Users associated with the project -->
                    <div class="col-12">
                        <label class="form-label" for="list-users">Users</label>
                        <ol class="list-group" id="list-users">
                            <!-- User names will be appended here -->
                        </ol>
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
        // Fungsi untuk menampilkan modal detail Proyek dan mengisi data dari server
        function fillFormDetail(id) {
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#modalDetailProyek').modal('show');
            $('#modalDetailProyek').append($loadingOverlay);

            // Set URL untuk mendapatkan data proyek berdasarkan ID
            const url = `{{ route('proyek.show', ':id') }}`.replace(':id', id);

            // Lakukan AJAX GET request ke server untuk mengambil data proyek
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Mengisi nilai input di modal detail dengan data proyek yang diterima
                    $('#modalDetailProyek #proyek_nama').val(response.data.nama);

                    // Clear previous user list and add new list
                    $('#list-users').empty();
                    if (response.data.users && response.data.users.length > 0) {
                        response.data.users.forEach(user => {
                            $('#list-users').append(`<li class="list-group-item">${user.name} — ${user.email}</li>`);
                        });
                    } else {
                        $('#list-users').append(`<li class="list-group-item">Tidak ada pengguna terkait dengan proyek ini</li>`);
                    }

                    $loadingOverlay.remove();
                },
                error: function(xhr) {
                    alert("Gagal mengambil data. Silakan coba lagi.");
                    $loadingOverlay.remove();
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
