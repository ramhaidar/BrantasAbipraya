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
        <table class="m-0 table table-bordered table-striped" id="table-data">
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
                @forelse ($TableData as $item)
                    @foreach ($item->linkRkbDetails as $detail)
                        <tr>
                            <td>{{ $detail->linkAlatDetailRkb->masterDataAlat->jenis_alat ?? '-' }}</td>
                            <td>{{ $detail->linkAlatDetailRkb->masterDataAlat->kode_alat ?? '-' }}</td>
                            <td>{{ $item->kategoriSparepart->kode ?? '-' }}: {{ $item->kategoriSparepart->nama ?? '-' }}</td>
                            <td>{{ $item->masterDataSparepart->nama ?? '-' }}</td>
                            <td>{{ $item->masterDataSparepart->part_number ?? '-' }}</td>
                            <td>{{ $item->masterDataSparepart->merk ?? '-' }}</td>
                            <td>{{ $detail->linkAlatDetailRkb->nama_koordinator ?? '-' }}</td>
                            <td>
                                <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" onclick="showDokumentasi({{ $item->id }})">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                            </td>
                            <td>
                                <a class="btn {{ $detail->linkAlatDetailRkb->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('rkb_urgent.detail.timeline.index', ['id' => $detail->linkAlatDetailRkb->id]) }}">
                                    <i class="bi bi-hourglass-split"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn {{ $detail->linkAlatDetailRkb->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $detail->linkAlatDetailRkb->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $detail->linkAlatDetailRkb->id }}" data-id-lampiran="{{ $detail->linkAlatDetailRkb->lampiranRkbUrgent ? $detail->linkAlatDetailRkb->lampiranRkbUrgent->id : null }}">
                                    <i class="bi bi-paperclip"></i>
                                </button>
                            </td>
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
    <script>
        $(document).ready(function() {
            const $table = $('#table-data');
            const $headers = $table.find('thead th');
            const textsToCheck = ['Detail', 'Aksi', 'Supplier'];
            let indices = {};

            // Find the indices of the headers that match the texts in textsToCheck array
            $headers.each(function(index) {
                const headerText = $(this).text().trim();
                if (textsToCheck.includes(headerText)) {
                    indices[headerText] = index;
                }
            });

            // Set the width of the corresponding columns in tbody
            $.each(indices, function(text, index) {
                $table.find('tbody tr').each(function() {
                    $(this).find('td').eq(index).css('width', '1%');
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

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
