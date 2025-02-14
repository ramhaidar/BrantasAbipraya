@push('styles_3')
    @include('styles.tables')
@endpush

@include('dashboard.evaluasi.urgent.detail.partials.modal-preview')
@include('dashboard.evaluasi.urgent.detail.partials.modal-lampiran')

@php
    $headers = [
        [
            'title' => 'Nama Alat',
            'filterId' => 'jenis-alat',
            'paramName' => 'jenis_alat',
            'filter' => true,
        ],
        [
            'title' => 'Kode Alat',
            'filterId' => 'kode-alat',
            'paramName' => 'kode_alat',
            'filter' => true,
        ],
        [
            'title' => 'Kategori Sparepart',
            'filterId' => 'kategori-sparepart',
            'paramName' => 'kategori_sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Sparepart',
            'filterId' => 'sparepart',
            'paramName' => 'sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Part Number',
            'filterId' => 'part-number',
            'paramName' => 'part_number',
            'filter' => true,
        ],
        [
            'title' => 'Merk',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'Nama Koordinator',
            'filterId' => 'nama-koordinator',
            'paramName' => 'nama_koordinator',
            'filter' => true,
        ],
        [
            'title' => 'Dokumentasi',
            'filter' => false,
        ],
        [
            'title' => 'Timeline',
            'filter' => false,
        ],
        [
            'title' => 'Lampiran',
            'filter' => false,
        ],
        [
            'title' => 'Quantity Requested',
            'filterId' => 'quantity-requested',
            'paramName' => 'quantity_requested',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Quantity Approved',
            'filter' => false,
        ],
        [
            'title' => 'Quantity in Stock',
            'filterId' => 'stock-quantity',
            'paramName' => 'stock_quantity',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
    ];

    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    $resetUrl = request()->url();
    $queryParams = '';
    if (request()->hasAny(['search', 'id_proyek'])) {
        $queryParams = '?' . http_build_query(request()->only(['search', 'id_proyek']));
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if ($appliedFilters)
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ $resetUrl . $queryParams }}">
                    <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        @foreach ($headers as $header)
                            @include('components.table-header-filter', array_merge($header, ['uniqueValues' => $uniqueValues ?? []]))
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        // First group by linkAlatDetailRkb
                        $groupedByAlat =
                            $TableData instanceof \Illuminate\Pagination\LengthAwarePaginator
                                ? collect($TableData->items())->groupBy(function ($item) {
                                    return $item->linkRkbDetails->first()->linkAlatDetailRkb->id;
                                })
                                : collect($TableData)->groupBy(function ($item) {
                                    return $item->linkRkbDetails->first()->linkAlatDetailRkb->id;
                                });

                        // Prepare a collection to track part numbers we've seen
                        $processedPartNumbers = collect();
                    @endphp

                    @forelse ($groupedByAlat as $alatId => $alatItems)
                        @php
                            $groupedByPartNumber = $alatItems->groupBy(function ($item) {
                                return optional($item->masterDataSparepart)->part_number;
                            });

                            $firstItem = $alatItems->first();
                            $detail = $firstItem->linkRkbDetails->first();
                            $alat = $detail->linkAlatDetailRkb;
                        @endphp

                        @foreach ($groupedByPartNumber as $partNumber => $items)
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
                                        <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" data-id="{{ $item->id ?? '-' }}" type="button" onclick="event.preventDefault(); event.stopPropagation(); showDokumentasi({{ $item->id ?? '-' }});">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </button>
                                    </td>
                                    @if ($loop->parent->first && $loop->first)
                                        <td rowspan="{{ $alatItems->count() }}">
                                            <a class="btn {{ $alat->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('evaluasi_rkb_urgent.detail.timeline.index', ['id' => $alat->id]) }}">
                                                <i class="bi bi-hourglass-split"></i>
                                            </a>
                                        </td>
                                        <td rowspan="{{ $alatItems->count() }}">
                                            <button class="btn {{ $alat->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $alat->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $alat->id }}" data-id-lampiran="{{ $alat->lampiranRkbUrgent ? $alat->lampiranRkbUrgent->id : null }}" type="button">
                                                <i class="bi bi-paperclip"></i>
                                            </button>
                                        </td>
                                    @endif
                                    <td>{{ $item->quantity_requested ?? '-' }}</td>
                                    <td>
                                        <input class="form-control text-center 
                                        @if ($rkb->is_approved_svp) bg-primary-subtle
                                        @elseif ($rkb->is_approved_vp) bg-info-subtle
                                        @elseif($rkb->is_evaluated) bg-success-subtle 
                                        @else bg-warning-subtle @endif" name="quantity_approved[{{ $item->id ?? '-' }}]" type="number" value="{{ $item->quantity_approved ?? ($item->quantity_requested ?? '-') }}" min="0" {{ $rkb->is_evaluated ? 'disabled' : '' }}>
                                    </td>
                                    @if (!$processedPartNumbers->has($partNumber))
                                        @php
                                            $samePartNumberItems = collect($TableData instanceof \Illuminate\Pagination\LengthAwarePaginator ? $TableData->items() : $TableData)->filter(function ($tableItem) use ($partNumber) {
                                                return optional($tableItem->masterDataSparepart)->part_number === $partNumber;
                                            });
                                            $processedPartNumbers->put($partNumber, true);
                                        @endphp
                                        <td rowspan="{{ $samePartNumberItems->count() }}">
                                            {{ $stockQuantities[$item->id_master_data_sparepart] ?? '-' }}
                                        </td>
                                    @endif
                                    <td>{{ $item->satuan ?? '-' }}</td>
                                </tr>
                            @endforeach
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

        <!-- Add hidden inputs for all filters -->
        @foreach ($headers as $header)
            @if ($header['filter'])
                <input id="selected-{{ $header['paramName'] }}" name="selected_{{ $header['paramName'] }}" type="hidden" value="{{ request('selected_' . $header['paramName']) }}">
            @endif
        @endforeach
    </form>
</div>

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
