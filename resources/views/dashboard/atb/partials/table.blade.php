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
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>Nomor SPB</th>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Supplier</th>
                    <th>Sparepart</th>
                    <th>Merk</th>
                    <th>Part Number</th>
                    <th>Quantity</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Jumlah Harga</th>
                    <th>PPN</th>
                    <th>Bruto</th>
                    <th>STT</th>
                    <th>Dokumentasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td class="spb-number" data-spb="{{ $item->spb->nomor }}" data-id="{{ $item->id }}" data-stt="{{ $item->surat_tanda_terima }}">
                            {{ $item->spb->nomor }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                        <td>{{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}</td>
                        <td>{{ $item->masterDataSupplier->nama }}</td>
                        <td>{{ $item->masterDataSparepart->nama }}</td>
                        <td>{{ $item->masterDataSparepart->merk }}</td>
                        <td>{{ $item->masterDataSparepart->part_number }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->detailSpb->satuan ?? '-' }}</td>
                        <td class="currency-value">{{ formatRibuan($item->harga) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga * 0.11) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga * 1.11) }}</td>
                        <td class="text-center stt-cell">
                            <button class="btn btn-primary mx-1 sttBtn" onclick="showSTTModal('{{ $item->id }}')">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                        </td>
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
                            <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
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
            @if($TableData->currentPage() === $TableData->lastPage())
            <tfoot>
                <tr class="table-primary">
                    <td class="text-center fw-bold" colspan="10">Grand Total (Keseluruhan)</td>
                    <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_harga) }}</td>
                    <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_ppn) }}</td>
                    <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_bruto) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@include('dashboard.atb.partials.modal-dokumentasi')

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Group rows by SPB number and STT
            function groupRows() {
                let groups = {};
                $('#table-data tbody tr').each(function() {
                    const $row = $(this);
                    const $spbCell = $row.find('.spb-number');
                    const spbNumber = $spbCell.data('spb');
                    const stt = $spbCell.data('stt');
                    const id = $spbCell.data('id');
                    const groupKey = `${spbNumber}_${stt}`;

                    if (!groups[groupKey]) {
                        groups[groupKey] = {
                            spbNumber,
                            stt,
                            firstRow: $row,
                            rows: [$row],
                            ids: [id]
                        };
                    } else {
                        groups[groupKey].rows.push($row);
                        groups[groupKey].ids.push(id);
                        $spbCell.remove();
                    }
                });
                return groups;
            }

            function applyRowspans(groups) {
                Object.values(groups).forEach(group => {
                    const rowCount = group.rows.length;
                    if (rowCount > 1) {
                        const $spbCell = group.firstRow.find('.spb-number');
                        const $actionCell = group.firstRow.find('.action-cell');
                        $spbCell.attr('rowspan', rowCount);
                        $actionCell.attr('rowspan', rowCount);
                        group.rows.slice(1).forEach($row => {
                            $row.find('.action-cell').remove();
                        });
                        $actionCell.find('.deleteBtn').data('ids', group.ids.join(','));
                    }
                });
            }

            const groups = groupRows();
            applyRowspans(groups);
        });
    </script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
