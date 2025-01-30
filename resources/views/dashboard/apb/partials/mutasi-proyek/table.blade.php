@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
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
    </style>
@endpush

@php
    function formatRibuan($number)
    {
        return number_format($number, 0, ',', '.');
    }

    function formatTanggal($date)
    {
        setlocale(LC_TIME, 'id_ID');
        return \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
    }
@endphp

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Tujuan Proyek</th>
                <th class="text-center">Jenis Alat</th>
                <th class="text-center">Kode Alat</th>
                <th class="text-center">Merek Alat</th>
                <th class="text-center">Tipe Alat</th>
                <th class="text-center">Serial Number Alat</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity Dikirim</th>
                <th class="text-center">Quantity Diterima</th>
                <th class="text-center">Quantity Digunakan</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Jumlah Harga</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apbs as $apb)
                <tr>
                    <td class="text-center">{{ formatTanggal($apb->tanggal) }}</td>
                    <td class="text-center">{{ $apb->tujuanProyek->nama ?? '-' }}</td>
                    <td class="text-center">{{ $apb->alatProyek->masterDataAlat->jenis_alat ?? '-' }}</td>
                    <td class="text-center">{{ $apb->alatProyek->masterDataAlat->kode_alat ?? '-' }}</td>
                    <td class="text-center">{{ $apb->alatProyek->masterDataAlat->merek_alat ?? '-' }}</td>
                    <td class="text-center">{{ $apb->alatProyek->masterDataAlat->tipe_alat ?? '-' }}</td>
                    <td class="text-center">{{ $apb->alatProyek->masterDataAlat->serial_number ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->kategoriSparepart ? $apb->masterDataSparepart->kategoriSparepart->kode . ': ' . $apb->masterDataSparepart->kategoriSparepart->nama : '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSupplier->nama ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->nama ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->merk ?? '-' }}</td>
                    <td class="text-center">{{ $apb->masterDataSparepart->part_number ?? '-' }}</td>
                    <td class="text-center">{{ $apb->status !== null ? $apb->quantity : '-' }}</td>
                    <td class="text-center">{{ $apb->status !== null ? $apb->atbMutasi->quantity ?? '-' : '-' }}</td>
                    <td class="text-center">{{ $apb->status === null ? $apb->quantity : '-' }}</td>
                    <td class="text-center">{{ $apb->saldo->satuan ?? '-' }}</td>
                    <td class="currency-value">{{ formatRibuan($apb->saldo->harga ?? 0) }}</td>
                    <td class="currency-value">{{ formatRibuan(($apb->saldo->harga ?? 0) * $apb->quantity) }}</td>
                    <td class="text-center">
                        @if ($apb->status === 'pending')
                            <span class="badge bg-warning w-100">Pending</span>
                        @elseif($apb->status === 'rejected')
                            <span class="badge bg-danger w-100">Rejected</span>
                        @elseif($apb->status === 'accepted')
                            <span class="badge bg-success w-100">Accepted</span>
                        @elseif($apb->status === null)
                            <span class="badge bg-dark w-100">Penggunaan</span>
                        @else
                            <span class="badge bg-secondary w-100">{{ ucfirst($apb->status ?? '-') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $apb->id }}" {{ in_array($apb->status, ['accepted', 'rejected']) ? 'disabled' : '' }}>
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-primary">
                <td class="text-center fw-bold" colspan="17">Grand Total (Accepted & Penggunaan Only)</td>
                <td class="text-center fw-bold currency-value" id="total-harga">0</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

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
                    // Get status from the badge text
                    let status = $(this).find('td:eq(18) .badge').text().trim().toLowerCase();

                    // Only calculate if status is 'accepted' or 'penggunaan'
                    if (status === 'accepted' || status === 'penggunaan') {
                        let value = $(this).find('td:eq(17)').text()
                            .replace(/\./g, '');
                        total += parseInt(value) || 0;
                    }
                });

                let formattedTotal = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
