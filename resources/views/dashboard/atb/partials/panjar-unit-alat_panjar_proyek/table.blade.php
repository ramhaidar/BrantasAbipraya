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
            'type' => 'number',
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
            'type' => 'price',
        ],
        [
            'title' => 'Jumlah Harga',
            'filterId' => 'jumlah-harga',
            'paramName' => 'jumlah_harga',
            'filter' => true,
            'type' => 'price',
        ],
        [
            'title' => 'PPN',
            'filterId' => 'ppn',
            'paramName' => 'ppn',
            'filter' => true,
            'type' => 'price',
        ],
        [
            'title' => 'Bruto',
            'filterId' => 'bruto',
            'paramName' => 'bruto',
            'filter' => true,
            'type' => 'price',
        ],
        [
            'title' => 'Dokumentasi',
            'filter' => false,
        ],
    ];

    if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin') {
        $headers[] = [
            'title' => 'Aksi',
            'filter' => false,
        ];
    }

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
                        @include(
                            'components.table-header-filter',
                            array_merge($header, [
                                'uniqueValues' => $uniqueValues ?? [],
                            ]))
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ formatTanggal($item->tanggal) }}</td>
                        <td class="text-center">
                            @if ($item->masterDataSparepart && $item->masterDataSparepart->kategoriSparepart)
                                {{ $item->masterDataSparepart->kategoriSparepart->kode }}: {{ $item->masterDataSparepart->kategoriSparepart->nama }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ $item->masterDataSupplier->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->nama }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->merk }}</td>
                        <td class="text-center">{{ $item->masterDataSparepart->part_number }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->saldo->satuan }}</td>
                        <td class="currency-value">{{ formatRibuan($item->harga) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga * 0.11) }}</td>
                        <td class="currency-value">{{ formatRibuan($item->quantity * $item->harga * 1.11) }}</td>
                        <td class="text-center doc-cell" data-id="{{ $item->id }}">
                            @php
                                $storagePath = storage_path('app/public/' . $item->dokumentasi_foto);
                                $hasImages = false;
                                if ($item->dokumentasi_foto && is_dir($storagePath)) {
                                    $files = glob($storagePath . '/*.{jpg,jpeg,png,heic}', GLOB_BRACE);
                                    $hasImages = !empty($files);
                                }
                            @endphp
                            <button class="btn {{ $hasImages ? 'btn-primary' : 'btn-secondary' }} mx-1" type="button" onclick="showDokumentasiModal('{{ $item->id }}')" {{ !$hasImages ? 'disabled' : '' }}>
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
                        <td class="text-center fw-bold" colspan="9">Grand Total</td>
                        <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_harga) }}</td>
                        <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_harga * 0.11) }}</td>
                        <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_harga * 1.11) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</div>

<!-- Include dokumentasi modal -->
@include('dashboard.atb.partials.modal-dokumentasi')

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
