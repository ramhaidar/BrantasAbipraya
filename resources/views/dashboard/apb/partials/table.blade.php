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
    if (!function_exists('formatRibuan')) {
        function formatRibuan($number)
        {
            return number_format($number, 0, ',', '.');
        }
    }

    if (!function_exists('formatTanggal')) {
        function formatTanggal($date)
        {
            setlocale(LC_TIME, 'id_ID');
            return \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y');
        }
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Tanggal</th>
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
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Harga</th>
                    <th class="text-center">Mekanik</th>
                    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                        <th class="text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ formatTanggal($item->tanggal) }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->jenis_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->kode_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->merek_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->tipe_alat }}</td>
                        <td class="text-center">{{ $item->alatProyek->masterDataAlat->serial_number }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}</td>
                        <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->saldo->satuan ?? '-' }}</td>
                        <td class="currency-value">{{ formatRibuan($item->saldo->harga ?? 0) }}</td>
                        <td class="currency-value">{{ formatRibuan(($item->saldo->harga ?? 0) * $item->quantity) }}</td>
                        <!-- Removed root_cause cell -->
                        <td class="text-center">{{ $item->mekanik ?? '-' }}</td>
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="17">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No ATB records found
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($TableData->currentPage() === $TableData->lastPage())
                <tfoot>
                    <tr class="table-primary">
                        <td class="text-center fw-bold" colspan="14">Grand Total</td>
                        <td class="text-center fw-bold currency-value" id="total-harga">{{ formatRibuan($TableData->total_amount) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts_3')
    <script></script>
@endpush
