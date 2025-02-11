@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_nama') || request('selected_alamat') || request('selected_contact_person'))
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
                                Nama Supplier
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
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nama')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Alamat Supplier
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('alamat-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_alamat'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('alamat')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="alamat-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search alamat..." onkeyup="filterCheckboxes('alamat')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input alamat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_alamat', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['alamat'] as $alamat)
                                            <div class="form-check">
                                                <input class="form-check-input alamat-checkbox" type="checkbox" value="{{ $alamat }}" style="cursor: pointer" {{ in_array($alamat, explode(',', request('selected_alamat', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $alamat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('alamat')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Contact Person
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('contact-person-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_contact_person'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('contact_person')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="contact-person-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search contact person..." onkeyup="filterCheckboxes('contact_person')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input contact_person-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_contact_person', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['contact_person'] as $contactPerson)
                                            <div class="form-check">
                                                <input class="form-check-input contact_person-checkbox" type="checkbox" value="{{ $contactPerson }}" style="cursor: pointer" {{ in_array($contactPerson, explode(',', request('selected_contact_person', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $contactPerson }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('contact_person')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>Detail</th>
                        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="text-center">{{ $supplier->nama }}</td>
                            <td class="text-center">{{ $supplier->alamat }}</td>
                            <td class="text-center">{{ $supplier->contact_person }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary detailBtn" data-id="{{ $supplier->id }}" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal type="button" onclick="fillFormEdit({{ $supplier->id }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $supplier->id }}" type="button">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No suppliers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-nama" name="selected_nama" type="hidden" value="{{ request('selected_nama') }}">
        <input id="selected-alamat" name="selected_alamat" type="hidden" value="{{ request('selected_alamat') }}">
        <input id="selected-contact-person" name="selected_contact_person" type="hidden" value="{{ request('selected_contact_person') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
