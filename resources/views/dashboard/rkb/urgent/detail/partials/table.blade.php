@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            padding: 4px 8px;
            vertical-align: middle;
        }

        .img-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

<!-- Modal for Mini Preview -->
<div class="modal fade" id="dokumentasiPreviewModal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dokumentasiPreviewTitle">Dokumentasi Preview</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-3" id="dokumentasiPreviewContainer"></div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.rkb.urgent.detail.partials.modal-preview')

<div class="ibox-body ms-0 ps-0 table-responsive" style="overflow-x: hidden">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Nama Alat</th>
                <th class="text-center">Kode Alat</th>
                <th class="text-center">Kategori Sparepart</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Nama Mekanik</th>
                <th class="text-center">Dokumentasi</th>
                <th class="text-center">Quantity Requested</th>
                <th class="text-center">Quantity Approved</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                @foreach ($item->linkAlatDetailRkbs as $item2)
                    @foreach ($item2->linkRkbDetails as $item3)
                        <tr>
                            <td class="text-center">{{ $item2->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $item2->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->kategoriSparepart->kode }}: {{ $item3->detailRkbUrgent->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->part_number }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->nama_mekanik }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary" onclick="showDokumentasi({{ $item3->detailRkbUrgent->id }})">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                            </td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->quantity_requested }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->quantity_approved ?? '-' }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->satuan }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning mx-1 ubahBtn" onclick="fillFormEditDetailRKB({{ $item3->detailRkbUrgent->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" onclick="deleteDetailRKB({{ $item3->detailRkbUrgent->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>    
</div>

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            const dokumentasiPreviewContainer = document.getElementById('dokumentasiPreviewContainer');
            const largeImagePreviewForShow = document.getElementById('largeImagePreviewForShow');
            const dokumentasiPreviewModal = new bootstrap.Modal(document.getElementById('dokumentasiPreviewModal'));
            const imagePreviewModalforShow = new bootstrap.Modal(document.getElementById('imagePreviewModalforShow'));

            // Fetch and display dokumentasi in modal
            window.showDokumentasi = function(id) {
                // Clear previous previews
                dokumentasiPreviewContainer.innerHTML = '';

                // Fetch dokumentasi data
                fetch(`/detail-rkb-urgent/${id}/dokumentasi`)
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
                                    largeImagePreviewForShow.src = file.url;
                                    document.getElementById('imagePreviewTitleForShow').textContent = file.name;
                                    imagePreviewModalforShow.show();
                                });

                                dokumentasiPreviewContainer.appendChild(img);
                            });
                        } else {
                            dokumentasiPreviewContainer.innerHTML = '<p class="text-muted text-center">No dokumentasi available</p>';
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
        });
    </script>
@endpush
