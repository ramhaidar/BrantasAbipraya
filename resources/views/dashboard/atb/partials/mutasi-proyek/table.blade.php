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

    $appliedFilters = false;
    $filterFields = ['tanggal', 'asal_proyek', 'kode', 'supplier', 'sparepart', 'merk', 'part_number', 'quantity_dikirim', 'quantity_diterima', 'satuan', 'harga', 'jumlah_harga'];

    foreach ($filterFields as $field) {
        if (request("selected_$field")) {
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
                    @include('components.table-header-filter', [
                        'title' => 'Tanggal',
                        'filterId' => 'tanggal',
                        'paramName' => 'tanggal',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Asal Proyek',
                        'filterId' => 'asal-proyek',
                        'paramName' => 'asal_proyek',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Kode',
                        'filterId' => 'kode',
                        'paramName' => 'kode',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Supplier',
                        'filterId' => 'supplier',
                        'paramName' => 'supplier',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Sparepart',
                        'filterId' => 'sparepart',
                        'paramName' => 'sparepart',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Merk',
                        'filterId' => 'merk',
                        'paramName' => 'merk',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Part Number',
                        'filterId' => 'part-number',
                        'paramName' => 'part_number',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Quantity Dikirim',
                        'filterId' => 'quantity-dikirim',
                        'paramName' => 'quantity_dikirim',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Quantity Diterima',
                        'filterId' => 'quantity-diterima',
                        'paramName' => 'quantity_diterima',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Satuan',
                        'filterId' => 'satuan',
                        'paramName' => 'satuan',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Harga',
                        'filterId' => 'harga',
                        'paramName' => 'harga',
                    ])

                    @include('components.table-header-filter', [
                        'title' => 'Jumlah Harga',
                        'filterId' => 'jumlah-harga',
                        'paramName' => 'jumlah_harga',
                    ])

                    <th class="text-center">Dokumentasi</th>
                    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                        <th class="text-center">Aksi</th>
                    @endif
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
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
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
                        <td class="text-center fw-bold" colspan="11">Grand Total (Keseluruhan)</td>
                        <td class="text-center fw-bold currency-value">{{ formatRibuan($TableData->total_harga) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <form id="filter-form" method="GET">
        @foreach ($filterFields as $field)
            <input id="selected-{{ str_replace('_', '-', $field) }}" name="selected_{{ $field }}" type="hidden" value="{{ request("selected_$field") }}">
        @endforeach
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
