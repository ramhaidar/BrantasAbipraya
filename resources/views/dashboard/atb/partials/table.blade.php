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
    <div class="mb-3 d-flex justify-content-end">
        @if (request('selected_nomor_spb') || request('selected_tanggal') || request('selected_kode') || request('selected_supplier') || request('selected_sparepart') || request('selected_merk') || request('selected_part_number') || request('selected_quantity') || request('selected_satuan') || request('selected_harga') || request('selected_jumlah_harga') || request('selected_ppn') || request('selected_bruto'))
            <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() }}{{ request()->hasAny(['search', 'id_proyek']) ? '?' . http_build_query(request()->only(['search', 'id_proyek'])) : '' }}">
                <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Nomor SPB
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('nomor-spb-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_nomor_spb'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('nomor_spb')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="nomor-spb-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search nomor SPB..." onkeyup="filterCheckboxes('nomor_spb')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input nomor_spb-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['nomor_spb'] as $nomor)
                                        <div class="form-check">
                                            <input class="form-check-input nomor_spb-checkbox" type="checkbox" value="{{ $nomor }}" style="cursor: pointer" {{ in_array($nomor, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $nomor }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nomor_spb')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
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
                    <th>
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
                    <th>
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
                    <th>
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
                    <th>
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
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Quantity
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
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input quantity-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['quantity'] as $quantity)
                                        <div class="form-check">
                                            <input class="form-check-input quantity-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array($quantity, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
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
                    <th>
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
                    <th>
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
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            PPN
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('ppn-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_ppn'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('ppn')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="ppn-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search PPN..." onkeyup="filterCheckboxes('ppn')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input ppn-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['ppn'] as $ppn)
                                        <div class="form-check">
                                            <input class="form-check-input ppn-checkbox" type="checkbox" value="{{ $ppn }}" style="cursor: pointer" {{ in_array($ppn, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ formatRibuan($ppn) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('ppn')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Bruto
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('bruto-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_bruto'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('bruto')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="bruto-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search bruto..." onkeyup="filterCheckboxes('bruto')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input bruto-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedValues ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['bruto'] as $bruto)
                                        <div class="form-check">
                                            <input class="form-check-input bruto-checkbox" type="checkbox" value="{{ $bruto }}" style="cursor: pointer" {{ in_array($bruto, $selectedValues ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ formatRibuan($bruto) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('bruto')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>STT</th>
                    <th>Dokumentasi</th>
                    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                        <th>Aksi</th>
                    @endif
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
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
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
</div>

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

    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
