@push('styles_3')
    @include('styles.tables')
@endpush

@include('dashboard.rkb.urgent.detail.partials.modal-preview')

@include('dashboard.rkb.urgent.detail.partials.modal-lampiran')

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_jenis_alat') || request('selected_kode_alat') || request('selected_kategori_sparepart') || request('selected_sparepart') || request('selected_part_number') || request('selected_merk') || request('selected_satuan') || request('selected_quantity_requested') || request('selected_quantity_approved'))
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() . (request('search') ? '?search=' . request('search') : '') }}">
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
                            </div>
                            <div class="filter-popup" id="jenis-alat-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis alat..." onkeyup="filterCheckboxes('jenis_alat', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_jenis_alat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['jenis_alat'] as $jenisAlat)
                                            <div class="form-check">
                                                <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="{{ base64_encode($jenisAlat) }}" style="cursor: pointer" {{ in_array(base64_encode($jenisAlat), explode(',', request('selected_jenis_alat', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $jenisAlat }}</label>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode alat..." onkeyup="filterCheckboxes('kode_alat', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kode_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_kode_alat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kode_alat'] as $kodeAlat)
                                            <div class="form-check">
                                                <input class="form-check-input kode_alat-checkbox" type="checkbox" value="{{ base64_encode($kodeAlat) }}" style="cursor: pointer" {{ in_array(base64_encode($kodeAlat), explode(',', request('selected_kode_alat', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kodeAlat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kode_alat')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Kategori Sparepart
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kategori-sparepart-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_kategori_sparepart'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kategori_sparepart')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="kategori-sparepart-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kategori sparepart..." onkeyup="filterCheckboxes('kategori_sparepart', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kategori_sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_kategori_sparepart', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kategori_sparepart'] as $kategoriSparepart)
                                            <div class="form-check">
                                                <input class="form-check-input kategori_sparepart-checkbox" type="checkbox" value="{{ base64_encode($kategoriSparepart) }}" style="cursor: pointer" {{ in_array(base64_encode($kategoriSparepart), explode(',', request('selected_kategori_sparepart', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kategoriSparepart }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kategori_sparepart')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search sparepart..." onkeyup="filterCheckboxes('sparepart', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_sparepart', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['sparepart'] as $sparepart)
                                            <div class="form-check">
                                                <input class="form-check-input sparepart-checkbox" type="checkbox" value="{{ base64_encode($sparepart) }}" style="cursor: pointer" {{ in_array(base64_encode($sparepart), explode(',', request('selected_sparepart', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $sparepart }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sparepart')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search part number..." onkeyup="filterCheckboxes('part_number', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input part_number-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_part_number', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['part_number'] as $partNumber)
                                            <div class="form-check">
                                                <input class="form-check-input part_number-checkbox" type="checkbox" value="{{ base64_encode($partNumber) }}" style="cursor: pointer" {{ in_array(base64_encode($partNumber), explode(',', request('selected_part_number', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $partNumber }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('part_number')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merk..." onkeyup="filterCheckboxes('merk', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_merk', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['merk'] as $merk)
                                            <div class="form-check">
                                                <input class="form-check-input merk-checkbox" type="checkbox" value="{{ base64_encode($merk) }}" style="cursor: pointer" {{ in_array(base64_encode($merk), explode(',', request('selected_merk', ''))) ? 'checked' : '' }}>
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
                                Nama Koordinator
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('nama-koordinator-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_nama_koordinator'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('nama_koordinator')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="nama-koordinator-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search nama koordinator..." onkeyup="filterCheckboxes('nama_koordinator', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input nama_koordinator-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_nama_koordinator', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nama_koordinator'] as $namaKoordinator)
                                            <div class="form-check">
                                                <input class="form-check-input nama_koordinator-checkbox" type="checkbox" value="{{ base64_encode($namaKoordinator) }}" style="cursor: pointer" {{ in_array(base64_encode($namaKoordinator), explode(',', request('selected_nama_koordinator', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $namaKoordinator }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nama_koordinator')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>Dokumentasi</th>
                        <th>Timeline</th>
                        <th>Lampiran</th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Quantity Requested
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-requested-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_quantity_requested'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity_requested')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="quantity-requested-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_requested', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input quantity_requested-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_quantity_requested', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['quantity_requested'] as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input quantity_requested-checkbox" type="checkbox" value="{{ base64_encode($quantity) }}" style="cursor: pointer" {{ in_array(base64_encode($quantity), explode(',', request('selected_quantity_requested', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_requested')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>

                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Quantity Approved
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-approved-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_quantity_approved'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity_approved')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="quantity-approved-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity_approved', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input quantity_approved-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_quantity_approved', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['quantity_approved'] as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input quantity_approved-checkbox" type="checkbox" value="{{ base64_encode($quantity) }}" style="cursor: pointer" {{ in_array(base64_encode($quantity), explode(',', request('selected_quantity_approved', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_approved')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search satuan..." onkeyup="filterCheckboxes('satuan', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_satuan', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['satuan'] as $satuan)
                                            <div class="form-check">
                                                <input class="form-check-input satuan-checkbox" type="checkbox" value="{{ base64_encode($satuan) }}" style="cursor: pointer" {{ in_array(base64_encode($satuan), explode(',', request('selected_satuan', ''))) ? 'checked' : '' }}>
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
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Group items by linkAlatDetailRkb ID
                        $groupedItems = collect($TableData->items())->groupBy(function ($item) {
                            return $item->linkRkbDetails->first()->linkAlatDetailRkb->id;
                        });
                    @endphp

                    @forelse ($groupedItems as $alatId => $items)
                        @php
                            $firstItem = $items->first();
                            $detail = $firstItem->linkRkbDetails->first();
                            $alat = $detail->linkAlatDetailRkb;
                        @endphp

                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $alat->masterDataAlat->jenis_alat ?? '-' }}</td>
                                <td>{{ $alat->masterDataAlat->kode_alat ?? '-' }}</td>
                                <td>{{ $item->kategoriSparepart->kode ?? '-' }}: {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                                <td>{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                                <td>{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                                <td>{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                                <td>{{ $alat->nama_koordinator ?? '-' }}</td>
                                <td>
                                    <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" onclick="showDokumentasi({{ $item->id }})">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </td>
                                @if ($loop->first)
                                    <td rowspan="{{ $items->count() }}">
                                        <a class="btn {{ $alat->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('rkb_urgent.detail.timeline.index', ['id' => $alat->id]) }}">
                                            <i class="bi bi-hourglass-split"></i>
                                        </a>
                                    </td>

                                    <td rowspan="{{ $items->count() }}">
                                        <button class="btn {{ $alat->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $alat->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $alat->id }}" data-id-lampiran="{{ $alat->lampiranRkbUrgent ? $alat->lampiranRkbUrgent->id : null }}">
                                            <i class="bi bi-paperclip"></i>
                                        </button>
                                    </td>
                                @endif
                                <td>{{ $item->quantity_requested }}</td>
                                <td>{{ $item->quantity_approved ?? '-' }}</td>
                                <td>{{ $item->satuan }}</td>
                                @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                                    <td>
                                        <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" type="button" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditDetailRKB({{ $item->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" type="button" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="14">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-jenis-alat" name="selected_jenis_alat" type="hidden" value="{{ request('selected_jenis_alat') }}">
        <input id="selected-kode-alat" name="selected_kode_alat" type="hidden" value="{{ request('selected_kode_alat') }}">
        <input id="selected-kategori-sparepart" name="selected_kategori_sparepart" type="hidden" value="{{ request('selected_kategori_sparepart') }}">
        <input id="selected-sparepart" name="selected_sparepart" type="hidden" value="{{ request('selected_sparepart') }}">
        <input id="selected-part-number" name="selected_part_number" type="hidden" value="{{ request('selected_part_number') }}">
        <input id="selected-merk" name="selected_merk" type="hidden" value="{{ request('selected_merk') }}">
        <input id="selected-nama-koordinator" name="selected_nama_koordinator" type="hidden" value="{{ request('selected_nama_koordinator') }}">
        <input id="selected-quantity-requested" name="selected_quantity_requested" type="hidden" value="{{ request('selected_quantity_requested') }}">
        <input id="selected-quantity-approved" name="selected_quantity_approved" type="hidden" value="{{ request('selected_quantity_approved') }}">
        <input id="selected-satuan" name="selected_satuan" type="hidden" value="{{ request('selected_satuan') }}">
        <!-- Add other hidden inputs for filters -->
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')

    <script>
        $(document).ready(function() {
            const dokumentasiPreviewContainer = document.getElementById('dokumentasiPreviewContainer');
            const largeImagePreviewForShow = document.getElementById('largeImagePreviewForShow');
            const dokumentasiPreviewModal = new bootstrap.Modal(document.getElementById('dokumentasiPreviewModal'));
            const imagePreviewModalforShow = new bootstrap.Modal(document.getElementById('imagePreviewModalforShow'));

            // Laravel route name for dokumentasi
            const dokumentasiRoute = @json(route('rkb_urgent.detail.dokumentasi', ['id' => ':id']));

            // Fetch and display dokumentasi in modal
            window.showDokumentasi = function(id) {
                // Clear previous previews
                dokumentasiPreviewContainer.innerHTML = '';

                // Replace ':id' with the actual id
                const fetchUrl = dokumentasiRoute.replace(':id', id);

                // Fetch dokumentasi data
                fetch(fetchUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.dokumentasi && data.dokumentasi.length > 0) {
                            data.dokumentasi.forEach(file => {
                                const img = document.createElement('img');
                                img.src = file.url;
                                img.alt = file.name;
                                img.title = file.name;
                                img.classList.add('img-thumbnail');

                                // Add click event to open large preview
                                img.addEventListener('click', () => {
                                    $('#dokumentasiPreviewModal').modal('hide');

                                    largeImagePreviewForShow.src = file.url;
                                    document.getElementById('imagePreviewTitleForShow').textContent = file.name;
                                    imagePreviewModalforShow.show();
                                });

                                dokumentasiPreviewContainer.appendChild(img);
                            });
                        } else {
                            dokumentasiPreviewContainer.innerHTML = '<p class="text-muted text-center">Tidak ada Dokumentasi</p>';
                        }

                        // Show dokumentasi preview modal
                        dokumentasiPreviewModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching dokumentasi:', error);
                        dokumentasiPreviewContainer.innerHTML = '<p class="text-danger text-center">Failed to load dokumentasi</p>';
                        dokumentasiPreviewModal.show();
                    });
            };

            // Event listener for when the preview modal is closed
            document.getElementById('imagePreviewModalforShow').addEventListener('hidden.bs.modal', function() {
                // Reopen #modalForAdd using jQuery
                $('#dokumentasiPreviewModal').modal('show');
            });
        });
    </script>
@endpush
