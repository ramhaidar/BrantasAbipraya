@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            vertical-align: middle;
            text-align: center;
        }

        .img-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

@include('dashboard.rkb.urgent.detail.partials.modal-preview')

@include('dashboard.rkb.urgent.detail.partials.modal-lampiran')

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>Nama Alat</th>
                    <th>Kode Alat</th>
                    <th>Kategori Sparepart</th>
                    <th>Sparepart</th>
                    <th>Part Number</th>
                    <th>Merk</th>
                    <th>Nama Koordinator</th>
                    <th>Dokumentasi</th>
                    <th>Timeline</th>
                    <th>Lampiran</th>
                    <th>Quantity Requested</th>
                    <th>Quantity Approved</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
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
                            <td>{{ $alat->nama_koordinator ?? '-' }}</td>
                            <td>
                                <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" onclick="showDokumentasi({{ $item->id }})">
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
                                    <button class="btn {{ $alat->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $alat->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $alat->id }}" data-id-lampiran="{{ $alat->lampiranRkbUrgent ? $alat->lampiranRkbUrgent->id : null }}">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                </td>
                            @endif
                            <td>{{ $item->quantity_requested }}</td>
                            <td>{{ $item->quantity_approved ?? '-' }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>
                                <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }} onclick="fillFormEditDetailRKB({{ $item->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}" {{ $detail->linkAlatDetailRkb->rkb->is_finalized ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
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

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')

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
