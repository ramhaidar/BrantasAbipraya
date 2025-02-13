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
        @if (request('selected_tanggal') || request('selected_tujuan_proyek') || request('selected_jenis_alat') || request('selected_kode_alat') || request('selected_merek_alat') || request('selected_tipe_alat') || request('selected_serial_number') || request('selected_kode') || request('selected_supplier') || request('selected_sparepart') || request('selected_merk') || request('selected_part_number') || request('selected_quantity_dikirim') || request('selected_quantity_diterima') || request('selected_quantity_digunakan') || request('selected_satuan') || request('selected_harga') || request('selected_jumlah_harga') || request('selected_mekanik') || request('selected_status'))
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
                                    @foreach ($uniqueValues['tanggal'] ?? [] as $tanggal)
                                        <div class="form-check">
                                            <input class="form-check-input tanggal-checkbox" type="checkbox" value="{{ $tanggal }}" style="cursor: pointer">
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
                            Tujuan Proyek
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tujuan-proyek-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_tujuan_proyek'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tujuan_proyek')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="tujuan-proyek-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tujuan proyek..." onkeyup="filterCheckboxes('tujuan_proyek')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input tujuan_proyek-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['tujuan_proyek'] ?? [] as $tujuan_proyek)
                                        <div class="form-check">
                                            <input class="form-check-input tujuan_proyek-checkbox" type="checkbox" value="{{ $tujuan_proyek }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tujuan_proyek }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tujuan_proyek')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Jenis Alat
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
                        </div>
                        <div class="filter-popup" id="jenis-alat-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis alat..." onkeyup="filterCheckboxes('jenis_alat')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['jenis_alat'] ?? [] as $jenis_alat)
                                        <div class="form-check">
                                            <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="{{ $jenis_alat }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $jenis_alat }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jenis_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
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
                        </div>
                        <div class="filter-popup" id="kode-alat-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode alat..." onkeyup="filterCheckboxes('kode_alat')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input kode_alat-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['kode_alat'] ?? [] as $kode_alat)
                                        <div class="form-check">
                                            <input class="form-check-input kode_alat-checkbox" type="checkbox" value="{{ $kode_alat }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kode_alat }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kode_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Merek Alat
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('merek-alat-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_merek_alat'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('merek_alat')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="merek-alat-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merek alat..." onkeyup="filterCheckboxes('merek_alat')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input merek_alat-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['merek_alat'] ?? [] as $merek_alat)
                                        <div class="form-check">
                                            <input class="form-check-input merek_alat-checkbox" type="checkbox" value="{{ $merek_alat }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $merek_alat }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('merek_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Tipe Alat
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tipe-alat-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_tipe_alat'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tipe_alat')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="tipe-alat-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tipe alat..." onkeyup="filterCheckboxes('tipe_alat')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input tipe_alat-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['tipe_alat'] ?? [] as $tipe_alat)
                                        <div class="form-check">
                                            <input class="form-check-input tipe_alat-checkbox" type="checkbox" value="{{ $tipe_alat }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tipe_alat }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tipe_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Serial Number Alat
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('serial-number-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_serial_number'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('serial_number')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="serial-number-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search serial number..." onkeyup="filterCheckboxes('serial_number')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input serial_number-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['serial_number'] ?? [] as $serial_number)
                                        <div class="form-check">
                                            <input class="form-check-input serial_number-checkbox" type="checkbox" value="{{ $serial_number }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $serial_number }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('serial_number')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                        <input class="form-check-input kode-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['kode'] ?? [] as $kode)
                                        <div class="form-check">
                                            <input class="form-check-input kode-checkbox" type="checkbox" value="{{ $kode }}" style="cursor: pointer">
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
                                        <input class="form-check-input supplier-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['supplier'] ?? [] as $supplier)
                                        <div class="form-check">
                                            <input class="form-check-input supplier-checkbox" type="checkbox" value="{{ $supplier }}" style="cursor: pointer">
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
                                        <input class="form-check-input sparepart-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['sparepart'] ?? [] as $sparepart)
                                        <div class="form-check">
                                            <input class="form-check-input sparepart-checkbox" type="checkbox" value="{{ $sparepart }}" style="cursor: pointer">
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
                                        <input class="form-check-input merk-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['merk'] ?? [] as $merk)
                                        <div class="form-check">
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="{{ $merk }}" style="cursor: pointer">
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
                                        <input class="form-check-input part_number-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['part_number'] ?? [] as $part_number)
                                        <div class="form-check">
                                            <input class="form-check-input part_number-checkbox" type="checkbox" value="{{ $part_number }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $part_number }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('part_number')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
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
                                        <input class="form-check-input quantity_dikirim-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['quantity'] ?? [] as $quantity)
                                        @if ($quantity !== null)
                                            <div class="form-check">
                                                <input class="form-check-input quantity_dikirim-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer">
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_dikirim')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
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
                                        <input class="form-check-input quantity_diterima-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($TableData->pluck('atbMutasi.quantity')->filter()->unique() as $quantity)
                                        <div class="form-check">
                                            <input class="form-check-input quantity_diterima-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_diterima')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Quantity Digunakan
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-digunakan-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_quantity_digunakan'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity_digunakan')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="quantity-digunakan-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_digunakan')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input quantity_digunakan-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['quantity'] ?? [] as $quantity)
                                        <div class="form-check">
                                            <input class="form-check-input quantity_digunakan-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_digunakan')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                        <input class="form-check-input satuan-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['satuan'] ?? [] as $satuan)
                                        <div class="form-check">
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="{{ $satuan }}" style="cursor: pointer">
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
                                        <input class="form-check-input harga-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['harga'] ?? [] as $harga)
                                        <div class="form-check">
                                            <input class="form-check-input harga-checkbox" type="checkbox" value="{{ $harga }}" style="cursor: pointer">
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
                                        <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['jumlah_harga'] ?? [] as $jumlah_harga)
                                        <div class="form-check">
                                            <input class="form-check-input jumlah_harga-checkbox" type="checkbox" value="{{ $jumlah_harga }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ formatRibuan($jumlah_harga) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jumlah_harga')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Mekanik
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('mekanik-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_mekanik'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('mekanik')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="mekanik-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search mekanik..." onkeyup="filterCheckboxes('mekanik')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input mekanik-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                    </div>
                                    @foreach ($uniqueValues['mekanik'] ?? [] as $mekanik)
                                        <div class="form-check">
                                            <input class="form-check-input mekanik-checkbox" type="checkbox" value="{{ $mekanik }}" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $mekanik }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('mekanik')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    <th>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            Status
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('status-filter')">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                                @if (request('selected_status'))
                                    <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('status')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="filter-popup" id="status-filter" style="display: none;">
                            <div class="p-2">
                                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search status..." onkeyup="filterCheckboxes('status')">
                                <div class="checkbox-list text-start">
                                    <div class="form-check">
                                        <input class="form-check-input status-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Penggunaan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input status-checkbox" type="checkbox" value="pending" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Pending</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input status-checkbox" type="checkbox" value="accepted" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Accepted</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input status-checkbox" type="checkbox" value="rejected" style="cursor: pointer">
                                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Rejected</label>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('status')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                            </div>
                        </div>
                    </th>
                    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                        <th class="text-center">Aksi</th>
                    @endif
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
                        <td class="text-center">{{ $item->mekanik ?? '-' }}</td>
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
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" {{ in_array($item->status, ['accepted', 'rejected']) ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
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
            @if ($TableData->currentPage() === $TableData->lastPage())
                <tfoot>
                    <tr class="table-primary">
                        <td class="text-center fw-bold" colspan="17">Grand Total (Accepted & Penggunaan Only)</td>
                        <td class="text-center fw-bold currency-value" id="total-harga">{{ formatRibuan($TableData->total_amount) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
