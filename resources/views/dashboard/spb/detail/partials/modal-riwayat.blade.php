<!-- Modal for SPB History -->
<div class="fade modal" id="modalRiwayatSPB" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content rounded-4">
            <div class="pt-3 px-3 m-0 d-flex w-100 justify-content-between">
                <h5 class="modal-title w-100 pb-2">Riwayat SPB</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <hr class="p-0 m-0 border border-secondary-subtle border-2 opacity-50">
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-striped table-bordered">
                        <thead class="table-primary text-center">
                            <tr>
                                <th class="text-center">Nomor SPB</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center" style="width: 1%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatSpb as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $item->nomor }}</td>
                                    <td class="text-center">{{ $item->supplier->nama }}</td>
                                    <td class="text-center" style="width: 1%">
                                        <a href="{{ route('spb.detail.riwayat.index', $item->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-secondary d-block"></i>
                                        <p class="text-secondary mt-2 mb-0">Belum ada riwayat SPB</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Show history modal when button is clicked
            $(document).on('click', '.showRiwayatSPB', function() {
                $('#modalRiwayatSPB').modal('show');
            });
        });
    </script>
@endpush

@php
    function getStatusColor($status)
    {
        return match (strtolower($status)) {
            'draft' => 'secondary',
            'submitted' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
@endphp
