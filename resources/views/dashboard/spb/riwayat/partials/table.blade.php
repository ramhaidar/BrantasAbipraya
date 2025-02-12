@push('styles_3')
    @include('styles.tables')

    <style>
        #table-data th:nth-child(7),
        #table-data th:nth-child(8),
        #table-data td:nth-child(7),
        #table-data td:nth-child(8) {
            min-width: 10dvw;
            width: 10dvw;
        }
    </style>
@endpush

@php
    // Calculate totals
    $totalHarga = 0;
    $totalJumlahHarga = 0;

    if (isset($TableData)) {
        foreach ($TableData as $spb) {
            foreach ($spb->linkSpbDetailSpb as $item) {
                $totalHarga += $item->detailSpb->harga;
                $totalJumlahHarga += $item->detailSpb->quantity_po * $item->detailSpb->harga;
            }
        }
    }

    $ppn = $totalJumlahHarga * 0.11;
    $grandTotal = $totalJumlahHarga + $ppn;
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="container-fluid p-0 m-0 pb-3">
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Lampiran:</span>
            </div>
            <div class="col">
                <span>Surat Pemesanan Barang</span>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Nomor:</span>
            </div>
            <div class="col">
                <span>{{ $spb->nomor }}</span>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Tanggal:</span>
            </div>
            <div class="col">
                <span>{{ \Carbon\Carbon::parse($spb->tanggal)->isoFormat('DD MMMM YYYY') }}</span>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-auto" style="width: 90px;">
                <span class="fw-medium">Supplier:</span>
            </div>
            <div class="col">
                <span>{{ $spb->masterDataSupplier->nama }}</span>
            </div>
        </div>
    </div>

    <div class="mb-3 d-flex justify-content-end">
        @if (request()->hasAny(['selected_jenis_barang', 'selected_merk', 'selected_spesifikasi', 'selected_quantity', 'selected_satuan']))
            <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() }}">
                <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">NO</th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            JENIS BARANG
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('jenis-barang-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_jenis_barang'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('jenis_barang')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="jenis-barang-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis barang..." onkeyup="filterCheckboxes('jenis_barang')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input jenis_barang-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['jenis_barang'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['jenis_barang'] as $jenisBarang)
                                        <div class="form-check">
                                            <input class="form-check-input jenis_barang-checkbox" type="checkbox" value="{{ $jenisBarang }}" style="cursor: pointer" {{ in_array($jenisBarang, $selectedValues['jenis_barang'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $jenisBarang }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jenis_barang')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            MERK
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('merk-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_merk'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('merk')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="merk-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merk..." onkeyup="filterCheckboxes('merk')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input merk-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['merk'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['merk'] as $merk)
                                        <div class="form-check">
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="{{ $merk }}" style="cursor: pointer" {{ in_array($merk, $selectedValues['merk'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $merk }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('merk')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            SPESIFIKASI/TIPE/NO SERI
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('spesifikasi-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_spesifikasi'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('spesifikasi')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="spesifikasi-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search spesifikasi..." onkeyup="filterCheckboxes('spesifikasi')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input spesifikasi-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['spesifikasi'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['spesifikasi'] as $spesifikasi)
                                        <div class="form-check">
                                            <input class="form-check-input spesifikasi-checkbox" type="checkbox" value="{{ $spesifikasi }}" style="cursor: pointer" {{ in_array($spesifikasi, $selectedValues['spesifikasi'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $spesifikasi }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('spesifikasi')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            JUMLAH
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_quantity'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="quantity-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jumlah..." onkeyup="filterCheckboxes('quantity')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input quantity-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['quantity'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['quantity'] as $quantity)
                                        <div class="form-check">
                                            <input class="form-check-input quantity-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array((string) $quantity, $selectedValues['quantity'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            SAT
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('satuan-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_satuan'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('satuan')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="satuan-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search satuan..." onkeyup="filterCheckboxes('satuan')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input satuan-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['satuan'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['satuan'] as $satuan)
                                        <div class="form-check">
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="{{ $satuan }}" style="cursor: pointer" {{ in_array($satuan, $selectedValues['satuan'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $satuan }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('satuan')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            HARGA
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('harga-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_harga'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('harga')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="harga-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search harga..." onkeyup="filterCheckboxes('harga')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input harga-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['harga'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['harga'] as $harga)
                                        <div class="form-check">
                                            <input class="form-check-input harga-checkbox" type="checkbox" value="{{ $harga }}" style="cursor: pointer" {{ in_array((string) $harga, $selectedValues['harga'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ number_format($harga, 0, ',', '.') }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('harga')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            JUMLAH HARGA
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('jumlah-harga-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_jumlah_harga'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('jumlah_harga')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="jumlah-harga-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jumlah harga..." onkeyup="filterCheckboxes('jumlah_harga')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues['jumlah_harga'] ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['jumlah_harga'] as $jumlahHarga)
                                        <div class="form-check">
                                            <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="{{ $jumlahHarga }}" style="cursor: pointer" {{ in_array((string) $jumlahHarga, $selectedValues['jumlah_harga'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ number_format($jumlahHarga, 0, ',', '.') }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jumlah_harga')">
                                    <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                </button>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($TableData as $spb)
                    @forelse ($spb->linkSpbDetailSpb as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $item->detailSpb->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $item->detailSpb->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item->detailSpb->masterDataSparepart->part_number }}</td>
                            <td class="text-center">{{ $item->detailSpb->quantity_po }}</td>
                            <td class="text-center">{{ $item->detailSpb->satuan }}</td>
                            <td class="currency-value">{{ number_format($item->detailSpb->harga, 0, ',', '.') }}</td>
                            <td class="currency-value">{{ number_format($item->detailSpb->quantity_po * $item->detailSpb->harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="8">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data SPB
                            </td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="8">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SPB
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot class="table-primary">
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="6">Jumlah</th>
                    <th class="currency-value">{{ number_format($totalHarga, 0, ',', '.') }}</th>
                    <th class="currency-value">{{ number_format($totalJumlahHarga, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="7">PPN 11%</th>
                    <th class="currency-value">{{ number_format($ppn, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="7">Grand Total</th>
                    <th class="currency-value">{{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
