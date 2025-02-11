@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
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
                                            <input class="form-check-input nomor-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_nomor', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nomor'] as $nomor)
                                            <div class="form-check">
                                                <input class="form-check-input nomor-checkbox" type="checkbox" value="{{ $nomor }}" style="cursor: pointer" {{ in_array($nomor, explode(',', request('selected_nomor', ''))) ? 'checked' : '' }}>
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
                                            <input class="form-check-input proyek-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_proyek', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['proyek'] as $proyek)
                                            <div class="form-check">
                                                <input class="form-check-input proyek-checkbox" type="checkbox" value="{{ $proyek }}" style="cursor: pointer" {{ in_array($proyek, explode(',', request('selected_proyek', ''))) ? 'checked' : '' }}>
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
                                            <input class="form-check-input periode-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode(',', request('selected_periode', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['periode'] as $periode)
                                            <div class="form-check">
                                                <input class="form-check-input periode-checkbox" type="checkbox" value="{{ $periode }}" style="cursor: pointer" {{ in_array($periode, explode(',', request('selected_periode', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ \Carbon\Carbon::parse($periode)->isoFormat('MMMM Y') }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('periode')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search status..." onkeyup="filterCheckboxes('status', event)">
                                    <div class="checkbox-list text-start">
                                        @foreach (['Pengajuan', 'Evaluasi', 'Menunggu Approval VP', 'Menunggu Approval SVP', 'Disetujui', 'Tidak Diketahui'] as $status)
                                            <div class="form-check">
                                                <input class="form-check-input status-checkbox" type="checkbox" value="{{ strtolower($status) }}" style="cursor: pointer" {{ in_array(strtolower($status), explode(',', request('selected_status', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $status }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('status')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>Evaluasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $rkb)
                        <tr>
                            <td class="text-center">{{ $rkb->nomor }}</td>
                            <td class="text-center">{{ $rkb->proyek->nama }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($rkb->periode)->isoFormat('MMMM Y') }}</td>
                            <td class="text-center">
                                @if (!$rkb->is_finalized && !$rkb->is_evaluated && !$rkb->is_approved_vp && !$rkb->is_approved_svp)
                                    <span class="badge bg-primary w-100">Pengajuan</span>
                                @elseif($rkb->is_finalized && !$rkb->is_evaluated && !$rkb->is_approved_vp && !$rkb->is_approved_svp)
                                    <span class="badge bg-warning w-100">Evaluasi</span>
                                @elseif($rkb->is_finalized && $rkb->is_evaluated && !$rkb->is_approved_vp)
                                    <span class="badge bg-info w-100">Menunggu Approval VP</span>
                                @elseif($rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && !$rkb->is_approved_svp)
                                    <span class="badge bg-secondary w-100">Menunggu Approval SVP</span>
                                @elseif($rkb->is_finalized && $rkb->is_evaluated && $rkb->is_approved_vp && $rkb->is_approved_svp)
                                    <span class="badge bg-success w-100">Disetujui</span>
                                @else
                                    <span class="badge bg-dark w-100">Tidak Diketahui</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn {{ !$rkb->is_finalized ? 'btn-secondary' : ($rkb->is_finalized && !$rkb->is_evaluated ? 'btn-warning' : 'btn-primary') }} mx-1 detailBtn {{ !$rkb->is_finalized ? 'disabled' : '' }}" href="{{ route('evaluasi_rkb_urgent.detail.index', ['id' => $rkb->id]) }}">
                                    <i class="fa-solid {{ !$rkb->is_finalized ? 'fa-eye-slash' : ($rkb->is_finalized && !$rkb->is_evaluated ? 'fa-stamp' : 'fa-eye') }}"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No RKB found
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
