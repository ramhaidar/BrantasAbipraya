@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <!-- Add this button section before the table -->
    <div class="mb-3 d-flex justify-content-end">
        @if (request('selected_jenis_alat') || request('selected_kode_alat') || request('selected_kategori') || request('selected_sparepart') || request('selected_merk') || request('selected_supplier') || request('selected_quantity_po') || request('selected_quantity_diterima') || request('selected_satuan') || request('selected_harga') || request('selected_jumlah_harga'))
            <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() . (request('search') ? '?search=' . request('search') : '') }}">
                <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Nama Alat
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('jenis-alat-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_jenis_alat'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('jenis_alat')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="jenis-alat-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis alat..." onkeyup="filterCheckboxes('jenis_alat', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedJenisAlat) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueJenisAlat as $jenisAlat)
                                            <div class="form-check">
                                                <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="{{ $jenisAlat }}" style="cursor: pointer" {{ in_array($jenisAlat, $selectedJenisAlat ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $jenisAlat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jenis_alat')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Kode Alat
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kode-alat-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_kode_alat'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kode_alat')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="kode-alat-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode alat..." onkeyup="filterCheckboxes('kode_alat', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kode_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedKodeAlat) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueKodeAlat as $kodeAlat)
                                            <div class="form-check">
                                                <input class="form-check-input kode_alat-checkbox" type="checkbox" value="{{ $kodeAlat }}" style="cursor: pointer" {{ in_array($kodeAlat, $selectedKodeAlat ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kodeAlat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kode_alat')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Kategori
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kategori-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_kategori'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kategori')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="kategori-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kategori..." onkeyup="filterCheckboxes('kategori', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedKategori) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueKategori as $kategori)
                                            <div class="form-check">
                                                <input class="form-check-input kategori-checkbox" type="checkbox" value="{{ $kategori }}" style="cursor: pointer" {{ in_array($kategori, $selectedKategori ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kategori }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kategori')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Sparepart PO
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('sparepart-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_sparepart'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('sparepart')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="sparepart-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search sparepart..." onkeyup="filterCheckboxes('sparepart', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedSparepart) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueSparepart as $sparepart)
                                            <div class="form-check">
                                                <input class="form-check-input sparepart-checkbox" type="checkbox" value="{{ $sparepart }}" style="cursor: pointer" {{ in_array($sparepart, $selectedSparepart ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $sparepart }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sparepart')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Merk
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
                            <div class="filter-popup" id="merk-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merk..." onkeyup="filterCheckboxes('merk', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedMerk) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueMerk as $merk)
                                            <div class="form-check">
                                                <input class="form-check-input merk-checkbox" type="checkbox" value="{{ $merk }}" style="cursor: pointer" {{ in_array($merk, $selectedMerk ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $merk }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('merk')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Supplier
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('supplier-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_supplier'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('supplier')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="supplier-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search supplier..." onkeyup="filterCheckboxes('supplier', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input supplier-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedSupplier) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueSupplier as $supplier)
                                            <div class="form-check">
                                                <input class="form-check-input supplier-checkbox" type="checkbox" value="{{ $supplier }}" style="cursor: pointer" {{ in_array($supplier, $selectedSupplier ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $supplier }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('supplier')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Quantity PO
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-po-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_quantity_po'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity_po')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="quantity-po-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_po', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input quantity_po-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedQuantityPO) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueQuantityPO as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input quantity_po-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array($quantity, $selectedQuantityPO ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_po')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Quantity Diterima
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-diterima-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_quantity_diterima'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity_diterima')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="filter-popup" id="quantity-diterima-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_diterima', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input quantity_diterima-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedQuantityDiterima) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueQuantityDiterima as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input quantity_diterima-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array($quantity, $selectedQuantityDiterima ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_diterima')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Satuan
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
                            <div class="filter-popup" id="satuan-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search satuan..." onkeyup="filterCheckboxes('satuan', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedSatuan) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueSatuan as $satuan)
                                            <div class="form-check">
                                                <input class="form-check-input satuan-checkbox" type="checkbox" value="{{ $satuan }}" style="cursor: pointer" {{ in_array($satuan, $selectedSatuan ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $satuan }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('satuan')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Harga
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
                            <div class="filter-popup" id="harga-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search harga..." onkeyup="filterCheckboxes('harga', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input harga-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedHarga) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueHarga as $harga)
                                            <div class="form-check">
                                                <input class="form-check-input harga-checkbox" type="checkbox" value="{{ $harga }}" style="cursor: pointer" {{ in_array($harga, $selectedHarga ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Rp {{ number_format($harga, 0, ',', '.') }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('harga')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Jumlah Harga
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
                            <div class="filter-popup" id="jumlah-harga-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jumlah harga..." onkeyup="filterCheckboxes('jumlah_harga', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedJumlahHarga) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueJumlahHarga as $jumlahHarga)
                                            <div class="form-check">
                                                <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="{{ $jumlahHarga }}" style="cursor: pointer" {{ in_array($jumlahHarga, $selectedJumlahHarga ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Rp {{ number_format($jumlahHarga, 0, ',', '.') }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jumlah_harga')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    @php
                        $details = isset($item->originalSpb) ? $item->originalSpb->linkSpbDetailSpb : $item->linkSpbDetailSpb;
                    @endphp

                    @forelse ($details as $detail)
                        <tr>
                            <td class="text-center">{{ $detail->detailSPB->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->kategoriSparepart->kode }}: {{ $detail->detailSPB->masterDataSparepart->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item->masterDataSupplier->nama }}</td>
                            <td class="text-center">{{ $item->linkSpbDetailSpb[$loop->index]->detailSPB->quantity_po }}</td>
                            <td class="text-center">{{ $detail->detailSPB->atbs->sum('quantity') }}</td>
                            <td class="text-center">{{ $detail->detailSPB->satuan }}</td>
                            <td class="currency-value">{{ number_format($detail->detailSPB->harga, 0, ',', '.') }}</td>
                            <td class="currency-value">{{ number_format($detail->detailSPB->harga * $detail->detailSPB->quantity_po, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="11">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data SPB
                            </td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="11">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SPB
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-primary">
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="9">Jumlah</th>
                    <th class="currency-value" id="totalHarga">0</th>
                    <th class="currency-value" id="totalJumlahHarga">0</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="10">PPN 11%</th>
                    <th class="currency-value" id="ppn11">0</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="10">Grand Total</th>
                    <th class="currency-value" id="grandTotal">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            function formatRibuan(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatRibuan(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            function updateJumlahHarga(row) {
                const harga = unformatRibuan(row.find('input[name^="harga"]').val());
                const quantity = parseFloat(row.find('input[name^="qty"]').val()) || 0;
                const jumlahHarga = harga * quantity;
                row.find('td:nth-child(10) input').val(formatRibuan(jumlahHarga));
                updateTotalFooter();
            }

            function updateTotalFooter() {
                let totalHarga = 0;
                let totalJumlahHarga = 0;

                $('#table-data tbody tr').each(function() {
                    const harga = unformatRibuan($(this).find('td:nth-child(10)').text());
                    const quantity = parseInt($(this).find('td:nth-child(7)').text()) || 0;
                    const jumlahHarga = harga * quantity;

                    totalHarga += harga;
                    totalJumlahHarga += jumlahHarga;
                });

                const ppn11 = totalJumlahHarga * 0.11;
                const grandTotal = totalJumlahHarga + ppn11;

                $('#totalHarga').text(formatRibuan(totalHarga));
                $('#totalJumlahHarga').text(formatRibuan(totalJumlahHarga));
                $('#ppn11').text(formatRibuan(ppn11));
                $('#grandTotal').text(formatRibuan(grandTotal));
            }

            // Event handler for harga input
            $(document).on('blur', 'input[name^="harga"]', function() {
                const row = $(this).closest('tr');
                const harga = unformatRibuan($(this).val());
                $(this).val(formatRibuan(harga));
                updateJumlahHarga(row);
            });

            // Event handler for quantity input
            $(document).on('input', 'input[name^="qty"]', function() {
                const row = $(this).closest('tr');
                const max = parseInt($(this).attr('max'));
                let val = parseInt($(this).val()) || 0;

                if (val > max) {
                    alert('Quantity PO tidak boleh melebihi Quantity Sisa');
                    $(this).val(max);
                    val = max;
                }

                if (val < 0) {
                    $(this).val(0);
                    val = 0;
                }

                updateJumlahHarga(row);
            });

            // Add event handler for sparepart select change
            $(document).on('change', '.sparepart-select', function() {
                const row = $(this).closest('tr');
                const qtyInput = row.find('input[name^="qty"]');
                const hargaInput = row.find('input[name^="harga"]');

                if ($(this).val()) {
                    qtyInput.prop('disabled', false);
                    hargaInput.prop('disabled', false);
                } else {
                    qtyInput.prop('disabled', true).val(0);
                    hargaInput.prop('disabled', true).val('0');
                    updateJumlahHarga(row);
                }
            });

            // Initialize sparepart selects with Select2
            $('.sparepart-select').select2({
                placeholder: 'Pilih Sparepart',
                width: '100%',
                allowClear: true
            });

            updateTotalFooter();
        });

        function toggleFilter(filterId) {
            const popup = document.getElementById(filterId);
            const allPopups = document.querySelectorAll('.filter-popup');

            allPopups.forEach(p => {
                if (p.id !== filterId) {
                    p.style.display = 'none';
                }
            });

            popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
        }

        function filterCheckboxes(filterKey, event) {
            const searchText = event.target.value.toLowerCase();
            const checkboxes = document.querySelectorAll(`[id^=${filterKey}_]`);

            checkboxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling.textContent.toLowerCase();
                checkbox.parentElement.style.display =
                    label.includes(searchText) ? 'block' : 'none';
            });
        }

        function applyFilter(filterKey) {
            const checkboxes = document.querySelectorAll(`[id^=${filterKey}_]:checked`);
            const values = Array.from(checkboxes).map(cb => cb.value);
            const encodedValues = btoa(values.join('||'));

            const url = new URL(window.location.href);
            url.searchParams.set(`selected_${filterKey}`, encodedValues);
            window.location.href = url.toString();
        }

        function clearFilter(filterKey) {
            const url = new URL(window.location.href);
            url.searchParams.delete(`selected_${filterKey}`);
            window.location.href = url.toString();
        }
    </script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
