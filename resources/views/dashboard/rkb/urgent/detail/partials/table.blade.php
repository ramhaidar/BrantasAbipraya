@push('styles_3')
    @include('styles.tables')
@endpush

@include('dashboard.rkb.urgent.detail.partials.modal-preview')
@include('dashboard.rkb.urgent.detail.partials.modal-lampiran')

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
            'filterId' => 'quantity-approved',
            'paramName' => 'quantity_approved',
            'filter' => true,
            'type' => 'number',
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
            'roles' => ['koordinator_proyek', 'superadmin'],
        ],
    ];

    // Check if any filter is applied
    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    // Build query parameters for resetting filters
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
                            @include('components.table-header-filter', array_merge($header, ['uniqueValues' => $uniqueValues]))
                        @endforeach
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
                                <td>{{ $item->nama_koordinator ?? '-' }}</td>
                                <td>
                                    <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" type="button" onclick="showDokumentasi({{ $item->id }})">
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
                                        <button class="btn {{ $alat->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $alat->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $alat->id }}" data-id-lampiran="{{ $alat->lampiranRkbUrgent ? $alat->lampiranRkbUrgent->id : null }}" type="button">
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
