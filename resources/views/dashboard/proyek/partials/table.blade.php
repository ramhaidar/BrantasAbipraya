@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_nama'))
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
                                Nama Proyek
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
                                            <input class="form-check-input nama-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_nama', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nama'] as $nama)
                                            <div class="form-check">
                                                <input class="form-check-input nama-checkbox" type="checkbox" value="{{ $nama }}" {{ in_array($nama, explode(',', request('selected_nama', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $nama }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nama')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $item)
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary detailBtn" data-id="{{ $item->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" onclick="fillFormEdit({{ $item->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="7">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No projects found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-nama" name="selected_nama" type="hidden" value="{{ request('selected_nama') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
