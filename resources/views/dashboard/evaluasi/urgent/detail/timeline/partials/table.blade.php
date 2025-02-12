@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_uraian') || request('selected_status'))
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
                                Uraian Pekerjaan
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('uraian-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_uraian'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('uraian')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="uraian-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search uraian..." onkeyup="filterCheckboxes('uraian')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input uraian-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_uraian', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['uraian'] as $uraian)
                                            <div class="form-check">
                                                <input class="form-check-input uraian-checkbox" type="checkbox" value="{{ $uraian }}" style="cursor: pointer" {{ in_array($uraian, explode(',', request('selected_uraian', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $uraian }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('uraian')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Waktu Penyelesaian (Rencana)
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('durasi-rencana-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_durasi_rencana'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('durasi_rencana')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="durasi-rencana-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search durasi..." onkeyup="filterCheckboxes('durasi_rencana')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input durasi_rencana-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_durasi_rencana', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['durasi_rencana'] as $durasi)
                                            <div class="form-check">
                                                <input class="form-check-input durasi_rencana-checkbox" type="checkbox" value="{{ $durasi }}" style="cursor: pointer" {{ in_array($durasi, explode(',', request('selected_durasi_rencana', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $durasi }} Hari</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('durasi_rencana')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Tanggal Awal Rencana
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tanggal-awal-rencana-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_tanggal_awal_rencana'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tanggal_awal_rencana')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="tanggal-awal-rencana-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tanggal..." onkeyup="filterCheckboxes('tanggal_awal_rencana')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input tanggal_awal_rencana-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_tanggal_awal_rencana', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['tanggal_awal_rencana'] as $tanggal)
                                            <div class="form-check">
                                                <input class="form-check-input tanggal_awal_rencana-checkbox" type="checkbox" value="{{ $tanggal }}" style="cursor: pointer" {{ in_array($tanggal, explode(',', request('selected_tanggal_awal_rencana', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tanggal }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tanggal_awal_rencana')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Tanggal Akhir Rencana
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tanggal-akhir-rencana-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_tanggal_akhir_rencana'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tanggal_akhir_rencana')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="tanggal-akhir-rencana-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tanggal..." onkeyup="filterCheckboxes('tanggal_akhir_rencana')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input tanggal_akhir_rencana-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_tanggal_akhir_rencana', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['tanggal_akhir_rencana'] as $tanggal)
                                            <div class="form-check">
                                                <input class="form-check-input tanggal_akhir_rencana-checkbox" type="checkbox" value="{{ $tanggal }}" style="cursor: pointer" {{ in_array($tanggal, explode(',', request('selected_tanggal_akhir_rencana', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tanggal }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tanggal_akhir_rencana')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Waktu Penyelesaian (Actual)
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('durasi-actual-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_durasi_actual'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('durasi_actual')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="durasi-actual-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search durasi..." onkeyup="filterCheckboxes('durasi_actual')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input durasi_actual-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_durasi_actual', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['durasi_actual'] as $durasi)
                                            <div class="form-check">
                                                <input class="form-check-input durasi_actual-checkbox" type="checkbox" value="{{ $durasi }}" style="cursor: pointer" {{ in_array($durasi, explode(',', request('selected_durasi_actual', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $durasi }} Hari</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('durasi_actual')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Tanggal Awal Actual
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tanggal-awal-actual-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_tanggal_awal_actual'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tanggal_awal_actual')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="tanggal-awal-actual-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tanggal..." onkeyup="filterCheckboxes('tanggal_awal_actual')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input tanggal_awal_actual-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_tanggal_awal_actual', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['tanggal_awal_actual'] as $tanggal)
                                            <div class="form-check">
                                                <input class="form-check-input tanggal_awal_actual-checkbox" type="checkbox" value="{{ $tanggal }}" style="cursor: pointer" {{ in_array($tanggal, explode(',', request('selected_tanggal_awal_actual', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tanggal }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tanggal_awal_actual')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Tanggal Akhir Actual
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tanggal-akhir-actual-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_tanggal_akhir_actual'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tanggal_akhir_actual')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="tanggal-akhir-actual-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tanggal..." onkeyup="filterCheckboxes('tanggal_akhir_actual')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input tanggal_akhir_actual-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_tanggal_akhir_actual', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['tanggal_akhir_actual'] as $tanggal)
                                            <div class="form-check">
                                                <input class="form-check-input tanggal_akhir_actual-checkbox" type="checkbox" value="{{ $tanggal }}" style="cursor: pointer" {{ in_array($tanggal, explode(',', request('selected_tanggal_akhir_actual', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $tanggal }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tanggal_akhir_actual')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input status-checkbox" type="checkbox" value="1" style="cursor: pointer" {{ in_array('1', explode(',', request('selected_status', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Sudah Selesai</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input status-checkbox" type="checkbox" value="0" style="cursor: pointer" {{ in_array('0', explode(',', request('selected_status', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Belum Selesai</label>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('status')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $item)
                        <tr>
                            <td class="text-center">{{ $item->nama_rencana }}</td>
                            <td class="text-center">{{ $item->diff_in_days_rencana ? $item->diff_in_days_rencana . ' Hari' : '-' }}</td>
                            <td class="text-center">{{ $item->tanggal_awal_rencana ? $item->tanggal_awal_rencana->format('Y-m-d') : '-' }}</td>
                            <td class="text-center">{{ $item->tanggal_akhir_rencana ? $item->tanggal_akhir_rencana->format('Y-m-d') : '-' }}</td>
                            <td class="text-center">{{ $item->diff_in_days_actual ? $item->diff_in_days_actual . ' Hari' : '-' }}</td>
                            <td class="text-center">{{ $item->tanggal_awal_actual ? $item->tanggal_awal_actual->format('Y-m-d') : '-' }}</td>
                            <td class="text-center">{{ $item->tanggal_akhir_actual ? $item->tanggal_akhir_actual->format('Y-m-d') : '-' }}</td>
                            <td class="text-center"><span class="badge {{ $item->is_done ? 'bg-success' : 'bg-warning' }} w-100">{{ $item->is_done ? 'Sudah Selesai' : 'Belum Selesai' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3" colspan="9">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <span class="text-muted">No data found</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-uraian" name="selected_uraian" type="hidden" value="{{ request('selected_uraian') }}">
        <input id="selected-status" name="selected_status" type="hidden" value="{{ request('selected_status') }}">
        <input id="selected-durasi-rencana" name="selected_durasi_rencana" type="hidden" value="{{ request('selected_durasi_rencana') }}">
        <input id="selected-tanggal-awal-rencana" name="selected_tanggal_awal_rencana" type="hidden" value="{{ request('selected_tanggal_awal_rencana') }}">
        <input id="selected-tanggal-akhir-rencana" name="selected_tanggal_akhir_rencana" type="hidden" value="{{ request('selected_tanggal_akhir_rencana') }}">
        <input id="selected-durasi-actual" name="selected_durasi_actual" type="hidden" value="{{ request('selected_durasi_actual') }}">
        <input id="selected-tanggal-awal-actual" name="selected_tanggal_awal_actual" type="hidden" value="{{ request('selected_tanggal_awal_actual') }}">
        <input id="selected-tanggal-akhir-actual" name="selected_tanggal_akhir_actual" type="hidden" value="{{ request('selected_tanggal_akhir_actual') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
