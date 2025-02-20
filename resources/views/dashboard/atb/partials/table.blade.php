@push('styles_3')
    @include('styles.tables')
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

    $headers = [
        [
            'title' => 'Nomor SPB',
            'filterId' => 'nomor-spb',
            'paramName' => 'nomor_spb',
            'filter' => true,
        ],
        [
            'title' => 'Tanggal',
            'filterId' => 'tanggal',
            'paramName' => 'tanggal',
            'filter' => true,
            'type' => 'date',
        ],
        [
            'title' => 'Kode',
            'filterId' => 'kode',
            'paramName' => 'kode',
            'filter' => true,
        ],
        [
            'title' => 'Supplier',
            'filterId' => 'supplier',
            'paramName' => 'supplier',
            'filter' => true,
        ],
        [
            'title' => 'Sparepart',
            'filterId' => 'sparepart',
            'paramName' => 'sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Merk',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'Part Number',
            'filterId' => 'part-number',
            'paramName' => 'part_number',
            'filter' => true,
        ],
        [
            'title' => 'Quantity',
            'filterId' => 'quantity',
            'paramName' => 'quantity',
            'filter' => true,
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
        [
            'title' => 'Harga',
            'filterId' => 'harga',
            'paramName' => 'harga',
            'filter' => true,
        ],
        [
            'title' => 'Jumlah Harga',
            'filterId' => 'jumlah-harga',
            'paramName' => 'jumlah_harga',
            'filter' => true,
        ],
        [
            'title' => 'PPN',
            'filterId' => 'ppn',
            'paramName' => 'ppn',
            'filter' => true,
        ],
        [
            'title' => 'Bruto',
            'filterId' => 'bruto',
            'paramName' => 'bruto',
            'filter' => true,
        ],
        [
            'title' => 'STT',
            'filter' => false,
        ],
        [
            'title' => 'Dokumentasi',
            'filter' => false,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
            'role' => ['koordinator_proyek', 'superadmin'],
        ],
    ];

    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    $resetUrl = request()->url();
    $queryParams = '';
    if (request()->hasAny(['search', 'id_proyek'])) {
        $queryParams = '?' . http_build_query(request()->only(['search', 'id_proyek']));
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if ($appliedFilters)
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ $resetUrl . $queryParams }}">
                    <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        @foreach ($headers as $header)
                            @include('components.table-header-filter', array_merge($header, ['uniqueValues' => $uniqueValues ?? []]))
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $item)
                        <tr>
                            <td class="spb-number" data-spb="{{ $item->spb->nomor ?? '-' }}" data-id="{{ $item->id }}" data-stt="{{ $item->surat_tanda_terima }}">
                                {{ $item->spb->nomor ?? '-' }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}</td>
                            <td>{{ $item->masterDataSupplier->nama }}</td>
                            <td>{{ $item->masterDataSparepart->nama }}</td>
                            <td>{{ $item->masterDataSparepart->merk }}</td>
                            <td>{{ $item->masterDataSparepart->part_number }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->detailSpb->satuan ?? ($item->saldo->satuan ?? '-') }}</td>
                            <td class="currency-value">{{ formatRibuan($item->harga) }}</td>
                            <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga) }}</td>
                            <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga * 0.11) }}</td>
                            <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga * 1.11) }}</td>
                            <td class="text-center stt-cell">
                                @if ($item->surat_tanda_terima)
                                    <button class="btn btn-primary mx-1 sttBtn" onclick="showSTTModal('{{ $item->id }}')">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                @else
                                    <button class="btn btn-secondary mx-1" disabled>
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </button>
                                @endif
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
                            @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                                <td class="text-center action-cell">
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" type="button">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            @endif
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
                @if ($TableData->currentPage() === $TableData->lastPage())
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

        @foreach ($headers as $header)
            @if ($header['filter'])
                <input id="selected-{{ $header['paramName'] }}" name="selected_{{ $header['paramName'] }}" type="hidden" value="{{ request("selected_{$header['paramName']}") }}">
            @endif
        @endforeach
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')

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

                    // Skip grouping if SPB number is null or '-'
                    if (spbNumber === null || spbNumber === '-') {
                        return;
                    }

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
                        const $firstRow = group.firstRow;
                        const $spbCell = $firstRow.find('.spb-number');
                        const $sttCell = $firstRow.find('.stt-cell');
                        const $actionCell = $firstRow.find('.action-cell');

                        // Apply rowspan to SPB number cell
                        $spbCell.attr('rowspan', rowCount);

                        // Apply rowspan to STT cell
                        $sttCell.attr('rowspan', rowCount);

                        // Apply rowspan to action cell if it exists
                        if ($actionCell.length) {
                            $actionCell.attr('rowspan', rowCount);
                        }

                        // Remove cells from subsequent rows
                        group.rows.slice(1).forEach($row => {
                            $row.find('.stt-cell').remove();
                            $row.find('.action-cell').remove();
                        });

                        // Store all IDs in the delete button if it exists
                        const $deleteBtn = $actionCell.find('.deleteBtn');
                        if ($deleteBtn.length) {
                            $deleteBtn.data('ids', group.ids.join(','));
                        }
                    }
                });
            }

            const groups = groupRows();
            applyRowspans(groups);
        });
    </script>
@endpush
