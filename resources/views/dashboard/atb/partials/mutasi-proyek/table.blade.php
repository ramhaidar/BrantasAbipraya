@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
            width: 100%;
        }

        #table-data td,
        #table-data th {
            vertical-align: middle;
            text-align: center;
        }

        .currency-value {
            text-align: right !important;
            padding-right: 10px !important;
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Add search box styling */
        .table-search {
            margin-bottom: 1rem;
        }

        .table-search input {
            padding: 0.5rem;
            width: 100%;
            max-width: 300px;
        }
    </style>
@endpush

@php
    function formatRibuan($number)
    {
        return number_format($number, 0, ',', '.');
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
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
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                        <td class="text-center">{{ $item->asalProyek->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}</td>
                        <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->nama }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->merk }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->part_number }}</td>
                        <td class="text-center">{{ $item->apbMutasi->quantity ?? '-' }}</td>
                        @if (!isset($item->apbMutasi))
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->saldo->satuan }}</td>
                        @else
                            <td class="text-center">{!! $item->apbMutasi->status === 'rejected' ? '<span class="badge bg-danger w-100">Rejected</span>' : $item->quantity ?? '-' !!}</td>
                            <td class="text-center">{{ $item->apbMutasi->saldo->satuan }}</td>
                        @endif
                        <td class="currency-value">{{ formatRibuan($item->harga) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga) }}</td>
                        <td class="text-center doc-cell" data-id="{{ $item->id }}">
                            @php
                                $storagePath = storage_path('app/public/' . $item->dokumentasi_foto);
                                $hasImages = false;
                                if ($item->dokumentasi_foto && is_dir($storagePath)) {
                                    $files = glob($storagePath . '/*.{jpg,jpeg,png,heic}', GLOB_BRACE);
                                    $hasImages = !empty($files);
                                }
                            @endphp
                            <button class="btn {{ $hasImages ? 'btn-primary' : 'btn-secondary' }} mx-1" onclick="showDokumentasiModal('{{ $item->id }}')" {{ !$hasImages ? 'disabled' : '' }}>
                                <i class="bi bi-images"></i>
                            </button>
                        </td>
                        <td class="text-center action-cell">
                            @if (!isset($item->apbMutasi))
                                <button class="btn btn-danger mx-1 rejectBtn" disabled>
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <button class="btn btn-success acceptBtn" disabled>
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            @else
                                <button class="btn btn-danger mx-1 rejectBtn" data-id="{{ $item->id }}" {{ $item->apbMutasi->status !== 'pending' ? 'disabled' : '' }}>
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <button class="btn btn-success acceptBtn" data-id="{{ $item->id }}" data-max="{{ $item->apbMutasi->quantity }}" data-max-text="(Max: {{ $item->apbMutasi->quantity }})" type="button" {{ $item->apbMutasi->status !== 'pending' ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="16">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No ATB records found
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <td class="text-center fw-bold" colspan="11">Grand Total</td>
                    <td class="text-center fw-bold currency-value" id="total-harga">0</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Include dokumentasi modal -->
@include('dashboard.atb.partials.modal-dokumentasi')

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                const rows = document.querySelectorAll('#table-data tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });

                calculateTotal();
            });

            function calculateTotal() {
                let total = 0;
                const visibleRows = document.querySelectorAll('#table-data tbody tr:not([style*="display: none"])');

                visibleRows.forEach(row => {
                    const value = row.cells[11].textContent
                        .replace(/\./g, '');
                    total += parseInt(value) || 0;
                });

                const formattedTotal = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                document.getElementById('total-harga').textContent = formattedTotal;
            }
        });
    </script>
@endpush
