@php
    use Carbon\Carbon;
@endphp

@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_nomor') || request('selected_proyek') || request('selected_periode') || request('selected_tipe'))
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() }}{{ request()->has('search') ? '?search=' . request('search') : '' }}">
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
                                No RKB
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('nomor-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_nomor'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('nomor')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="nomor-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search nomor..." onkeyup="filterCheckboxes('nomor', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input nomor-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode('||', base64_decode(request('selected_nomor', '')))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nomor'] as $nomor)
                                            <div class="form-check">
                                                <input class="form-check-input nomor-checkbox" type="checkbox" value="{{ $nomor }}" style="cursor: pointer" {{ in_array($nomor, explode('||', base64_decode(request('selected_nomor', '')))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $nomor }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nomor')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Proyek
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('proyek-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_proyek'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('proyek')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="proyek-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search proyek..." onkeyup="filterCheckboxes('proyek', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input proyek-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode('||', base64_decode(request('selected_proyek', '')))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['proyek'] as $proyek)
                                            <div class="form-check">
                                                <input class="form-check-input proyek-checkbox" type="checkbox" value="{{ $proyek }}" style="cursor: pointer" {{ in_array($proyek, explode('||', base64_decode(request('selected_proyek', '')))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $proyek }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('proyek')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Periode
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('periode-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_periode'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('periode')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="periode-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search periode..." onkeyup="filterCheckboxes('periode', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input periode-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode('||', base64_decode(request('selected_periode', '')))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['periode'] as $periode)
                                            <div class="form-check">
                                                <input class="form-check-input periode-checkbox" type="checkbox" value="{{ $periode }}" style="cursor: pointer" {{ in_array($periode, explode('||', base64_decode(request('selected_periode', '')))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ Carbon::parse($periode)->translatedFormat('F Y') }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('periode')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Tipe
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tipe-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_tipe'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tipe')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="tipe-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tipe..." onkeyup="filterCheckboxes('tipe', event)">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['tipe'] as $tipe)
                                            <div class="form-check">
                                                <input class="form-check-input tipe-checkbox" type="checkbox" value="{{ $tipe }}" style="cursor: pointer" {{ in_array($tipe, explode('||', base64_decode(request('selected_tipe', '')))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ ucfirst($tipe) }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tipe')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $item)
                        <tr>
                            <td>{{ $item->nomor }}</td>
                            <td>{{ $item->proyek->nama ?? '-' }}</td>
                            <td>{{ Carbon::parse($item->periode)->translatedFormat('F Y') }}</td>
                            <td>
                                @if ($item->tipe == 'general')
                                    <span class="badge bg-primary w-100">General</span>
                                @else
                                    <span class="badge bg-danger w-100">Urgent</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn btn-primary mx-1 detailBtn" data-id="{{ $item->id }}" href="{{ route('spb.proyek.detail.index', $item->id) }}">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data RKB
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
