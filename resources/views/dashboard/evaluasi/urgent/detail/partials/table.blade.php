@push('styles_3')
    @include('styles.tables')
@endpush

@include('dashboard.evaluasi.urgent.detail.partials.modal-preview')
@include('dashboard.evaluasi.urgent.detail.partials.modal-lampiran')

<form id="approveRkbForm" method="POST" action="">
    @csrf
    <div class="ibox-body ms-0 ps-0">
        <!-- Add Clear All Filters button -->
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_jenis_alat') || request('selected_kode_alat') || request('selected_kategori_sparepart') || request('selected_sparepart') || request('selected_part_number') || request('selected_merk') || request('selected_nama_koordinator') || request('selected_quantity_requested') || request('selected_stock_quantity'))
                <button class="btn btn-danger btn-sm btn-hide-text-mobile" type="button" onclick="clearAllFilters()">
                    <i class="bi bi-x-circle"></i> <span class="ms-2">Clear All Filters</span>
                </button>
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
                                            <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['jenis_alat'] as $jenisAlat)
                                            <div class="form-check">
                                                <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="{{ $jenisAlat }}" style="cursor: pointer">
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
                                            <input class="form-check-input kode_alat-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kode_alat'] as $kodeAlat)
                                            <div class="form-check">
                                                <input class="form-check-input kode_alat-checkbox" type="checkbox" value="{{ $kodeAlat }}" style="cursor: pointer">
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kategori..." onkeyup="filterCheckboxes('kategori_sparepart', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kategori_sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kategori_sparepart'] as $kategori)
                                            <div class="form-check">
                                                <input class="form-check-input kategori_sparepart-checkbox" type="checkbox" value="{{ $kategori }}" style="cursor: pointer">
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kategori }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kategori_sparepart')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                            <input class="form-check-input sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['sparepart'] as $sparepart)
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
                                            <input class="form-check-input part_number-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['part_number'] as $partNumber)
                                            <div class="form-check">
                                                <input class="form-check-input part_number-checkbox" type="checkbox" value="{{ $partNumber }}" style="cursor: pointer">
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
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['merk'] as $merk)
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search koordinator..." onkeyup="filterCheckboxes('nama_koordinator', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input nama_koordinator-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nama_koordinator'] as $koordinator)
                                            <div class="form-check">
                                                <input class="form-check-input nama_koordinator-checkbox" type="checkbox" value="{{ $koordinator }}" style="cursor: pointer">
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $koordinator }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nama_koordinator')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                            <input class="form-check-input quantity_requested-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['quantity_requested'] as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input quantity_requested-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer">
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity_requested')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>Quantity Approved</th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Quantity in Stock
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('stock-quantity-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_stock_quantity'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('stock_quantity')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="stock-quantity-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('stock_quantity', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input stock_quantity-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['stock_quantity'] as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input stock_quantity-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer">
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('stock_quantity')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
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
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="null" style="cursor: pointer">
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['satuan'] as $satuan)
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
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Group items by part number for stock quantities
                        $groupedByPartNumber =
                            $TableData instanceof \Illuminate\Pagination\LengthAwarePaginator
                                ? collect($TableData->items())->groupBy(function ($item) {
                                    return optional($item->masterDataSparepart)->part_number;
                                })
                                : collect($TableData)->groupBy(function ($item) {
                                    return optional($item->masterDataSparepart)->part_number;
                                });
                    @endphp

                    @forelse ($groupedByPartNumber as $partNumber => $items)
                        @foreach ($items as $item)
                            @php
                                $detail = $item->linkRkbDetails->first();
                                $alat = $detail->linkAlatDetailRkb;
                            @endphp
                            <tr>
                                <td>{{ $alat->masterDataAlat->jenis_alat ?? '-' }}</td>
                                <td>{{ $alat->masterDataAlat->kode_alat ?? '-' }}</td>
                                <td>{{ $item->kategoriSparepart->kode ?? '-' }}: {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                                <td>{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                                <td>{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                                <td>{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                                <td>{{ $alat->nama_koordinator ?? '-' }}</td>
                                <td>
                                    <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" data-id="{{ $item->id ?? '-' }}" type="button" onclick="event.preventDefault(); event.stopPropagation(); showDokumentasi({{ $item->id ?? '-' }});">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </td>
                                <td>
                                    <a class="btn {{ $alat->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('evaluasi_rkb_urgent.detail.timeline.index', ['id' => $alat->id ?? '-']) }}" onclick="event.stopPropagation();">
                                        <i class="bi bi-hourglass-split"></i>
                                    </a>
                                </td>
                                <td>
                                    <button class="btn {{ $alat->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $alat->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $alat->id ?? '-' }}" data-id-lampiran="{{ $alat->lampiranRkbUrgent ? $alat->lampiranRkbUrgent->id : '-' }}" type="button" onclick="event.stopPropagation();">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                </td>
                                <td>{{ $item->quantity_requested ?? '-' }}</td>
                                <td>
                                    <input class="form-control text-center 
                                    @if ($rkb->is_approved_svp) bg-primary-subtle
                                    @elseif ($rkb->is_approved_vp) bg-info-subtle
                                    @elseif($rkb->is_evaluated) bg-success-subtle 
                                    @else bg-warning-subtle @endif" name="quantity_approved[{{ $item->id ?? '-' }}]" type="number" value="{{ $item->quantity_approved ?? ($item->quantity_requested ?? '-') }}" min="0" {{ $rkb->is_evaluated ? 'disabled' : '' }}>
                                </td>
                                @if ($loop->first)
                                    <td rowspan="{{ $items->count() }}">{{ $stockQuantities[$item->id_master_data_sparepart] ?? '-' }}</td>
                                @endif
                                <td>{{ $item->satuan ?? '-' }}</td>
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
    </div>
    <button class="btn btn-success btn-sm approveBtn" id="hiddenApproveRkbButton" type="submit" hidden></button>
</form>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')

    <script>
        $(document).ready(function() {
            'use strict';

            const $dokumentasiPreviewContainer = $('#dokumentasiPreviewContainer');
            const $largeImagePreview = $('#largeImagePreviewForShow');
            const $imagePreviewTitle = $('#imagePreviewTitleForShow');
            const dokumentasiRoute = @json(route('evaluasi_rkb_urgent.detail.dokumentasi', ['id' => ':id']));

            window.showDokumentasi = function(id) {
                $dokumentasiPreviewContainer.empty();
                const fetchUrl = dokumentasiRoute.replace(':id', id);

                $.getJSON(fetchUrl)
                    .done(function(data) {
                        if (data.dokumentasi?.length) {
                            data.dokumentasi.forEach(file => {
                                $('<img>', {
                                        src: file.url,
                                        alt: file.name,
                                        title: file.name
                                    }).addClass('img-thumbnail')
                                    .on('click', () => {
                                        $('#dokumentasiPreviewModal').modal('hide');
                                        $largeImagePreview.attr('src', file.url);
                                        $imagePreviewTitle.text(file.name);
                                        $('#imagePreviewModalforShow').modal('show');
                                    })
                                    .appendTo($dokumentasiPreviewContainer);
                            });
                        } else {
                            $dokumentasiPreviewContainer.html(
                                '<p class="text-muted text-center">Tidak ada Dokumentasi</p>'
                            );
                        }
                        $('#dokumentasiPreviewModal').modal('show');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching dokumentasi:', textStatus, errorThrown);
                        $dokumentasiPreviewContainer.html(
                            '<p class="text-danger text-center">Failed to load dokumentasi</p>'
                        );
                        $('#dokumentasiPreviewModal').modal('show');
                    });
            };

            $('#imagePreviewModalforShow').on('hidden.bs.modal', function() {
                $('#dokumentasiPreviewModal').modal('show');
            });

            // Prevent form submission when clicking dokumentasi button
            $(document).on('click', '[data-id]', function(e) {
                if ($(this).closest('td').hasClass('text-center')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    </script>
@endpush
