@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            /* padding: 4px 8px; */
            vertical-align: middle;
        }
    </style>
@endpush

@php
    function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
@endphp

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Asal Proyek</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity Dikirim</th>
                <th class="text-center">Quantity Diterima</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Jumlah Harga</th>
                <th class="text-center">Dokumentasi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($atbs as $atb)
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($atb->tanggal)->translatedFormat('l, d F Y') }}</td>
                    <td class="text-center">{{ $atb->asalProyek->nama ?? '-' }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->kategoriSparepart->kode }}: {{ $atb->masterDataSparepart->kategoriSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSupplier->nama ?? '-' }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->merk }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->part_number }}</td>
                    <td class="text-center">{{ $atb->apbMutasi->quantity ?? '-' }}</td>
                    @if (!isset($atb->apbMutasi))
                        <td class="text-center">{{ $atb->quantity }}</td>
                        <td class="text-center">{{ $atb->saldo->satuan }}</td>
                    @else
                        <td class="text-center">{!! $atb->apbMutasi->status === 'rejected' ? '<span class="badge bg-danger w-100">Rejected</span>' : $atb->quantity ?? '-' !!}</td>
                        <td class="text-center">{{ $atb->apbMutasi->saldo->satuan }}</td>
                    @endif
                    <td class="text-center">{{ formatRupiah($atb->harga) }}</td>
                    <td class="text-center">{{ formatRupiah($atb->quantity * $atb->harga) }}</td>
                    <td class="text-center doc-cell" data-id="{{ $atb->id }}">
                        @php
                            $storagePath = storage_path('app/public/' . $atb->dokumentasi_foto);
                            $hasImages = false;
                            if ($atb->dokumentasi_foto && is_dir($storagePath)) {
                                $files = glob($storagePath . '/*.{jpg,jpeg,png,heic}', GLOB_BRACE);
                                $hasImages = !empty($files);
                            }
                        @endphp
                        <button class="btn {{ $hasImages ? 'btn-primary' : 'btn-secondary' }} mx-1" onclick="showDokumentasiModal('{{ $atb->id }}')" {{ !$hasImages ? 'disabled' : '' }}>
                            <i class="bi bi-images"></i>
                        </button>
                    </td>
                    <td class="text-center action-cell">
                        @if (!isset($atb->apbMutasi))
                            <button class="btn btn-danger mx-1 rejectBtn" disabled>
                                <i class="bi bi-x-circle"></i>
                            </button>
                            <button class="btn btn-success acceptBtn" disabled>
                                <i class="bi bi-check-circle"></i>
                            </button>
                        @else
                            <button class="btn btn-danger mx-1 rejectBtn" data-id="{{ $atb->id }}" {{ $atb->apbMutasi->status !== 'pending' ? 'disabled' : '' }}>
                                <i class="bi bi-x-circle"></i>
                            </button>
                            <button class="btn btn-success acceptBtn" data-id="{{ $atb->id }}" data-max="{{ $atb->apbMutasi->quantity }}" data-max-text="(Max: {{ $atb->apbMutasi->quantity }})" type="button" {{ $atb->apbMutasi->status !== 'pending' ? 'disabled' : '' }}>
                                <i class="bi bi-check-circle"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Include dokumentasi modal -->
@include('dashboard.atb.partials.modal-dokumentasi')

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });
        });
    </script>
@endpush
