@push('styles_3')
@endpush

<!-- Modal Add -->
<div class="modal fade" id="modalForAdd" aria-hidden="true" aria-labelledby="modalForAddLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title" id="modalForAddLabel">Tambah Data Pekerjaan</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <form class="needs-validation" id="addJobForm" novalidate method="POST" action="{{ route('rkb_urgent.detail.timeline.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <input class="form-control" id="id_link_alat_detail_rkb_add" name="id_link_alat_detail_rkb" type="hidden" value="{{ $data->id }}">

                        <div class="col-12">
                            <label class="form-label required" for="uraian_pekerjaan_add">Uraian Pekerjaan</label>
                            <input class="form-control" id="uraian_pekerjaan_add" name="uraian_pekerjaan" type="text" placeholder="Masukkan uraian pekerjaan" required>
                            <div class="invalid-feedback">Uraian Pekerjaan diperlukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="tanggal_awal_rencana_add">Tanggal Awal Rencana</label>
                            <input class="form-control datepicker" id="tanggal_awal_rencana_add" name="tanggal_awal_rencana" type="text" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal Awal Rencana diperlukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="tanggal_akhir_rencana_add">Tanggal Akhir Rencana</label>
                            <input class="form-control datepicker" id="tanggal_akhir_rencana_add" name="tanggal_akhir_rencana" type="text" autocomplete="off" required>
                            <div class="invalid-feedback">Tanggal Akhir Rencana diperlukan.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex w-100 justify-content-end">
                    <button class="btn btn-secondary me-2 w-25" type="reset">Reset</button>
                    <button class="btn btn-primary w-25" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            $('#addJobForm').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            });

            $('#tanggal_awal_rencana_add').on('change', function() {
                var minDate = $(this).datepicker('getDate');
                $('#tanggal_akhir_rencana_add').datepicker('option', 'minDate', minDate);
            });

            $('#tanggal_akhir_rencana_add').on('change', function() {
                var maxDate = $(this).datepicker('getDate');
                $('#tanggal_awal_rencana_add').datepicker('option', 'maxDate', maxDate);
            });
        });
    </script>
@endpush
