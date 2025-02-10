@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_nama') || request('selected_part_number') || request('selected_merk') || request('selected_kode') || request('selected_kategori') || request('selected_jenis') || request('selected_sub_jenis'))
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() . (request('search') ? '?search=' . request('search') . '&id_proyek=' . request('id_proyek') : '?id_proyek=' . request('id_proyek')) }}">
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
                                Nama Sparepart
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('nama-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_nama'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('nama')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="nama-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search nama..." onkeyup="filterCheckboxes('nama')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input nama-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_nama', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nama'] as $nama)
                                            <div class="form-check">
                                                <input class="form-check-input nama-checkbox" type="checkbox" value="{{ $nama }}" style="cursor: pointer" {{ in_array($nama, explode(',', request('selected_nama', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $nama }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nama')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                            <input class="form-check-input part_number-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_part_number', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['part_number'] as $partNumber)
                                            <div class="form-check">
                                                <input class="form-check-input part_number-checkbox" type="checkbox" value="{{ $partNumber }}" style="cursor: pointer" {{ in_array($partNumber, explode(',', request('selected_part_number', ''))) ? 'checked' : '' }}>
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
                                Merk Sparepart
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
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_merk', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['merk'] as $merk)
                                            <div class="form-check">
                                                <input class="form-check-input merk-checkbox" type="checkbox" value="{{ $merk }}" style="cursor: pointer" {{ in_array($merk, explode(',', request('selected_merk', ''))) ? 'checked' : '' }}>
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
                                            <input class="form-check-input kode-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_kode', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kode'] as $kode)
                                            <div class="form-check">
                                                <input class="form-check-input kode-checkbox" type="checkbox" value="{{ $kode }}" style="cursor: pointer" {{ in_array($kode, explode(',', request('selected_kode', ''))) ? 'checked' : '' }}>
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
                                Jenis
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('jenis-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_jenis'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('jenis')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="jenis-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis..." onkeyup="filterCheckboxes('jenis')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input jenis-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_jenis', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['jenis'] as $jenis)
                                            <div class="form-check">
                                                <input class="form-check-input jenis-checkbox" type="checkbox" value="{{ $jenis }}" style="cursor: pointer" {{ in_array($jenis, explode(',', request('selected_jenis', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $jenis }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jenis')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Sub Jenis
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('sub-jenis-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_sub_jenis'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('sub_jenis')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="sub-jenis-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search sub jenis..." onkeyup="filterCheckboxes('sub_jenis')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input sub_jenis-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ request('selected_sub_jenis') && in_array('null', explode(',', request('selected_sub_jenis'))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['sub_jenis'] as $subJenis)
                                            @if (!empty($subJenis))
                                                <div class="form-check">
                                                    <input class="form-check-input sub_jenis-checkbox" type="checkbox" value="{{ $subJenis }}" style="cursor: pointer" {{ request('selected_sub_jenis') && in_array($subJenis, explode(',', request('selected_sub_jenis'))) ? 'checked' : '' }}>
                                                    <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $subJenis }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sub_jenis')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
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
                            </div>
                            <div class="filter-popup" id="kategori-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kategori..." onkeyup="filterCheckboxes('kategori')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_kategori', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kategori'] as $kategori)
                                            <div class="form-check">
                                                <input class="form-check-input kategori-checkbox" type="checkbox" value="{{ $kategori }}" style="cursor: pointer" {{ in_array($kategori, explode(',', request('selected_kategori', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kategori }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kategori')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>Supplier</th>
                        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $sparepart)
                        <tr>
                            <td class="text-center">{{ $sparepart['nama'] }}</td>
                            <td class="text-center">{{ $sparepart['part_number'] }}</td>
                            <td class="text-center">{{ $sparepart['merk'] }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->kode }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->jenis }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->sub_jenis ?? '-' }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->nama }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary detailBtn" data-id="{{ $sparepart['id'] }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $sparepart['id'] }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $sparepart['id'] }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="9">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No spareparts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-nama" name="selected_nama" type="hidden" value="{{ request('selected_nama') }}">
        <input id="selected-part-number" name="selected_part_number" type="hidden" value="{{ request('selected_part_number') }}">
        <input id="selected-merk" name="selected_merk" type="hidden" value="{{ request('selected_merk') }}">
        <input id="selected-kode" name="selected_kode" type="hidden" value="{{ request('selected_kode') }}">
        <input id="selected-jenis" name="selected_jenis" type="hidden" value="{{ request('selected_jenis') }}">
        <input id="selected-sub-jenis" name="selected_sub_jenis" type="hidden" value="{{ request('selected_sub_jenis') }}">
        <input id="selected-kategori" name="selected_kategori" type="hidden" value="{{ request('selected_kategori') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
