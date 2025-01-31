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

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
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
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ formatTanggal($item->tanggal) }}</td>
                        <td class="text-center">{{ $item->tujuanProyek->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->jenis_alat ?? '-' }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->kode_alat ?? '-' }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->merek_alat ?? '-' }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->tipe_alat ?? '-' }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->serial_number ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->kategoriSparepart ? $item->masterDataSparepart->kategoriSparepart->kode . ': ' . $item->masterDataSparepart->kategoriSparepart->nama : '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                        <td class="text-center">{{ $item->status !== null ? $item->quantity : '-' }}</td>
                        <td class="text-center">{{ $item->status !== null ? $item->atbMutasi->quantity ?? '-' : '-' }}</td>
                        <td class="text-center">{{ $item->status === null ? $item->quantity : '-' }}</td>
                        <td class="text-center">{{ $item->saldo->satuan ?? '-' }}</td>
                        <td class="currency-value">{{ formatRibuan($item->saldo->harga ?? 0) }}</td>
                        <td class="currency-value">{{ formatRibuan(($item->saldo->harga ?? 0) * $item->quantity) }}</td>
                        <td class="text-center">
                            @if ($item->status === 'pending')
                                <span class="badge bg-warning w-100">Pending</span>
                            @elseif($item->status === 'rejected')
                                <span class="badge bg-danger w-100">Rejected</span>
                            @elseif($item->status === 'accepted')
                                <span class="badge bg-success w-100">Accepted</span>
                            @elseif($item->status === null)
                                <span class="badge bg-dark w-100">Penggunaan</span>
                            @else
                                <span class="badge bg-secondary w-100">{{ ucfirst($item->status ?? '-') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" {{ in_array($item->status, ['accepted', 'rejected']) ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="20">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No ATB records found
                        </td>
                    </tr>
                @endforelse
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
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            function calculateTotal() {
                let total = 0;
                $('#table-data tbody tr:not(.d-none)').each(function() {
                    let status = $(this).find('td:eq(18) .badge').text().trim().toLowerCase();
                    if (status === 'accepted' || status === 'penggunaan') {
                        let value = $(this).find('td:eq(17)').text()
                            .replace(/\./g, '');
                        total += parseInt(value) || 0;
                    }
                });

                let formattedTotal = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                $('#total-harga').html(formattedTotal);
            }

            $('#searchInput').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $('#table-data tbody tr').each(function() {
                    let rowText = $(this).text().toLowerCase();
                    $(this).toggleClass('d-none', rowText.indexOf(value) === -1);
                });
                calculateTotal();
            });

            calculateTotal();
        });
    </script>
@endpush
