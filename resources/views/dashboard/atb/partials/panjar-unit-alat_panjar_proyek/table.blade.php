@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
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
                <th class="text-center">Kode</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity</th>
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
                    <td class="text-center">{{ $atb->masterDataSparepart->kategoriSparepart->kode }}: {{ $atb->masterDataSparepart->kategoriSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSupplier->nama ?? '-' }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->merk }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->part_number }}</td>
                    <td class="text-center">{{ $atb->quantity }}</td>
                    <td class="text-center">{{ $atb->saldo->satuan }}</td>
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
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $atb->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-primary">
                <td class="text-center fw-bold" colspan="9">Grand Total</td>
                <td class="text-center fw-bold" id="total-harga">0</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Include dokumentasi modal -->
@include('dashboard.atb.partials.modal-dokumentasi')

@push('scripts_3')
    <script>
        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                paginate: false,
                ordering: false,
                drawCallback: function() {
                    calculateTotal();
                }
            });

            function calculateTotal() {
                let total = 0;
                $('#table-data tbody tr:visible').each(function() {
                    let value = $(this).find('td:eq(9)').text()
                        .replace('Rp ', '')
                        .replace(/\./g, '');
                    total += parseInt(value) || 0;
                });

                let formattedTotal = 'Rp ' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                $('#total-harga').html(formattedTotal);
            }

            calculateTotal();

            table.on('search.dt draw.dt', function() {
                setTimeout(calculateTotal, 100);
            });

            $('.dataTables_filter input').on('input', function() {
                setTimeout(calculateTotal, 100);
            });
        });
    </script>
@endpush
