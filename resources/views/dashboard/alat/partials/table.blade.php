@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_jenis_alat') || request('selected_kode_alat') || request('selected_merek_alat') || request('selected_tipe_alat') || request('selected_serial_number'))
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
                                            <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_jenis_alat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['jenis_alat'] as $jenisAlat)
                                            <div class="form-check">
                                                <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="{{ $jenisAlat }}" style="cursor: pointer" {{ in_array($jenisAlat, explode(',', request('selected_jenis_alat', ''))) ? 'checked' : '' }}>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode alat..." onkeyup="filterCheckboxes('kode_alat')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kode_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_kode_alat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kode_alat'] as $kodeAlat)
                                            <div class="form-check">
                                                <input class="form-check-input kode_alat-checkbox" type="checkbox" value="{{ $kodeAlat }}" style="cursor: pointer" {{ in_array($kodeAlat, explode(',', request('selected_kode_alat', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kodeAlat }}</label>
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
                                            <input class="form-check-input merek_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_merek_alat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['merek_alat'] as $merekAlat)
                                            <div class="form-check">
                                                <input class="form-check-input merek_alat-checkbox" type="checkbox" value="{{ $merekAlat }}" style="cursor: pointer" {{ in_array($merekAlat, explode(',', request('selected_merek_alat', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $merekAlat }}</label>
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
                                            <input class="form-check-input tipe_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_tipe_alat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['tipe_alat'] as $tipeAlat)
                                            <div class="form-check">
                                                <input class="form-check-input tipe_alat-checkbox" type="checkbox" value="{{ $tipeAlat }}" style="cursor: pointer" {{ in_array($tipeAlat, explode(',', request('selected_tipe_alat', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tipeAlat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tipe_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Serial Number
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
                                            <input class="form-check-input serial_number-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_serial_number', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['serial_number'] as $serialNumber)
                                            <div class="form-check">
                                                <input class="form-check-input serial_number-checkbox" type="checkbox" value="{{ $serialNumber }}" style="cursor: pointer" {{ in_array($serialNumber, explode(',', request('selected_serial_number', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $serialNumber }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('serial_number')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $item)
                        <tr>
                            <td class="text-center">{{ $item->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $item->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $item->masterDataAlat->merek_alat }}</td>
                            <td class="text-center">{{ $item->masterDataAlat->tipe_alat }}</td>
                            <td class="text-center">{{ $item->masterDataAlat->serial_number }}</td>
                            <td class="text-center">
                                <button class="btn btn-danger deleteBtn" data-id="{{ $item->id }}" type="button">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="16">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data alat yang tersedia untuk proyek ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-jenis-alat" name="selected_jenis_alat" type="hidden" value="{{ request('selected_jenis_alat') }}">
        <input id="selected-kode-alat" name="selected_kode_alat" type="hidden" value="{{ request('selected_kode_alat') }}">
        <input id="selected-merek-alat" name="selected_merek_alat" type="hidden" value="{{ request('selected_merek_alat') }}">
        <input id="selected-tipe-alat" name="selected_tipe_alat" type="hidden" value="{{ request('selected_tipe_alat') }}">
        <input id="selected-serial-number" name="selected_serial_number" type="hidden" value="{{ request('selected_serial_number') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
