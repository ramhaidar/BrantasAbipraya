@push('styles_3')
    <style>
        #table-history {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-history td,
        #table-history th {
            vertical-align: middle;
            text-align: center;
        }

        .modal-dialog-adaptive {
            max-height: fit-content;
            margin: auto;
        }

        .modal-dialog-centered-adaptive {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
            margin: 0 auto;
        }

        .modal-dialog-adaptive .modal-content {
            height: auto;
            max-height: 100%;
        }

        .modal-dialog-adaptive .modal-body {
            overflow-y: auto;
            max-height: calc(100vh - 300px);
            height: auto;
        }

        .loading-overlay {
            position: fixed;
            /* Change from absolute to fixed */
            top: 0;
            left: 0;
            width: 100vw;
            /* Change to viewport width */
            height: 100vh;
            /* Change to viewport height */
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1060;
            /* Increase z-index to be above modal */
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endpush

<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-adaptive modal-dialog-centered-adaptive">
        <div class="modal-content px-2">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Penempatan Alat</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="m-0 table table-bordered table-striped" id="table-history">
                        <thead class="table-primary">
                            <tr>
                                <th>Proyek</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        function showHistory(id) {
            // Add loading overlay to body instead of modal
            const $loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $('#historyModal').modal('show');
            $('#historyModal').append($loadingOverlay);

            $.get("{{ route('master_data_alat.history', '') }}/" + id, function(data) {
                let rows = '';
                data.forEach(item => {
                    const status = item.removed_at ?
                        `<span class="badge bg-danger w-100">Selesai (${moment(item.removed_at).format('DD/MM/YYYY')})</span>` :
                        `<span class="badge bg-success w-100">Masih Aktif</span>`;

                    rows += `
                        <tr>
                            <td>${item.proyek.nama}</td>
                            <td>${moment(item.assigned_at).format('DD/MM/YYYY')}</td>
                            <td>${status}</td>
                        </tr>
                    `;
                });
                $('#historyTableBody').html(rows || '<tr><td colspan="3" class="text-center">Tidak ada riwayat</td></tr>');
                $loadingOverlay.remove();
            }).fail(function() {
                alert("Gagal memuat riwayat. Silakan coba lagi.");
                $loadingOverlay.remove();
            });
        }

        $(document).ready(function() {
            $('.historyBtn').click(function() {
                const id = $(this).data('id');
                showHistory(id);
            });
        });
    </script>
@endpush
