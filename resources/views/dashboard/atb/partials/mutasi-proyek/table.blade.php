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
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Tanggal
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tanggal-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_tanggal'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tanggal')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="tanggal-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tanggal..." onkeyup="filterCheckboxes('tanggal')">
                                <div class="checkbox-list text-start">
                                    @foreach ($uniqueValues['tanggal'] as $tanggal)
                                        <div class="form-check">
                                            <input class="form-check-input tanggal-checkbox" type="checkbox" value="{{ $tanggal }}" style="cursor: pointer" {{ in_array($tanggal, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ formatTanggal($tanggal) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tanggal')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Asal Proyek
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('asal-proyek-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_asal_proyek'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('asal_proyek')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="asal-proyek-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search asal proyek..." onkeyup="filterCheckboxes('asal_proyek')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input asal_proyek-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['asal_proyek'] as $asalProyek)
                                        <div class="form-check">
                                            <input class="form-check-input asal_proyek-checkbox" type="checkbox" value="{{ $asalProyek }}" style="cursor: pointer" {{ in_array($asalProyek, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $asalProyek }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('asal_proyek')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Kode
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kode-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_kode'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kode')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="kode-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode..." onkeyup="filterCheckboxes('kode')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input kode-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['kode'] as $kode)
                                        <div class="form-check">
                                            <input class="form-check-input kode-checkbox" type="checkbox" value="{{ $kode }}" style="cursor: pointer" {{ in_array($kode, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kode }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kode')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                        </div>
                        <div class="filter-popup" id="supplier-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search supplier..." onkeyup="filterCheckboxes('supplier')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input supplier-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['supplier'] as $supplier)
                                        <div class="form-check">
                                            <input class="form-check-input supplier-checkbox" type="checkbox" value="{{ $supplier }}" style="cursor: pointer" {{ in_array($supplier, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $supplier }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('supplier')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Sparepart
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
                        </div>
                        <div class="filter-popup" id="sparepart-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search sparepart..." onkeyup="filterCheckboxes('sparepart')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['sparepart'] as $sparepart)
                                        <div class="form-check">
                                            <input class="form-check-input sparepart-checkbox" type="checkbox" value="{{ $sparepart }}" style="cursor: pointer" {{ in_array($sparepart, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $sparepart }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sparepart')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                        </div>
                        <div class="filter-popup" id="merk-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merk..." onkeyup="filterCheckboxes('merk')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input merk-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['merk'] as $merk)
                                        <div class="form-check">
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="{{ $merk }}" style="cursor: pointer" {{ in_array($merk, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $merk }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('merk')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Part Number
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('part-number-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_part_number'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('part_number')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="part-number-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search part number..." onkeyup="filterCheckboxes('part_number')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input part_number-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['part_number'] as $partNumber)
                                        <div class="form-check">
                                            <input class="form-check-input part_number-checkbox" type="checkbox" value="{{ $partNumber }}" style="cursor: pointer" {{ in_array($partNumber, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $partNumber }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('part_number')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th class="text-center">
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Quantity Dikirim
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-dikirim-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_quantity_dikirim'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity_dikirim')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="quantity-dikirim-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_dikirim')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input quantity_dikirim-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['quantity_dikirim'] as $quantity)
                                        <div class="form-check">
                                            <input class="form-check-input quantity_dikirim-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array($quantity, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_dikirim')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                        </div>
                        <div class="filter-popup" id="quantity-diterima-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_diterima')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input quantity_diterima-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['quantity_diterima'] as $quantity)
                                        <div class="form-check">
                                            <input class="form-check-input quantity_diterima-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array($quantity, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_diterima')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                        </div>
                        <div class="filter-popup" id="satuan-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search satuan..." onkeyup="filterCheckboxes('satuan')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input satuan-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['satuan'] as $satuan)
                                        <div class="form-check">
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="{{ $satuan }}" style="cursor: pointer" {{ in_array($satuan, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $satuan }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('satuan')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                        </div>
                        <div class="filter-popup" id="harga-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search harga..." onkeyup="filterCheckboxes('harga')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input harga-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['harga'] as $harga)
                                        <div class="form-check">
                                            <input class="form-check-input harga-checkbox" type="checkbox" value="{{ $harga }}" style="cursor: pointer" {{ in_array($harga, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ formatRibuan($harga) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('harga')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                        </div>
                        <div class="filter-popup" id="jumlah-harga-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jumlah harga..." onkeyup="filterCheckboxes('jumlah_harga')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['jumlah_harga'] as $jumlahHarga)
                                        <div class="form-check">
                                            <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="{{ $jumlahHarga }}" style="cursor: pointer" {{ in_array($jumlahHarga, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ formatRibuan($jumlahHarga) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jumlah_harga')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
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
</div>

@push('scripts_3')
    <script></script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
