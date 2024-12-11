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

@include('dashboard.rkb.urgent.detail.partials.modal-preview')

@include('dashboard.rkb.urgent.detail.partials.modal-lampiran')

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
                <th class="text-center">Timeline</th>
                <th class="text-center">Lampiran</th>
                <th class="text-center">Quantity Requested</th>
                <th class="text-center">Quantity Approved</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $alatRowCount = []; // Untuk menyimpan jumlah baris per kode_alat
                $processedAlat = []; // Untuk melacak kode alat yang sudah diproses
            @endphp

            @foreach ($data->linkAlatDetailRkbs as $item2)
                @foreach ($item2->linkRkbDetails as $item3)
                    @php
                        // Hitung jumlah baris untuk setiap kode alat
                        $kodeAlat = $item2->masterDataAlat->kode_alat;
                        if (!isset($alatRowCount[$kodeAlat])) {
                            $alatRowCount[$kodeAlat] = collect($item2->linkRkbDetails)->count();
                        }
                    @endphp

                    <tr>
                        <td class="text-center">{{ $item2->masterDataAlat->jenis_alat }}</td>
                        <td class="text-center">{{ $item2->masterDataAlat->kode_alat }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->kategoriSparepart->kode }}: {{ $item3->detailRkbUrgent->kategoriSparepart->nama }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->nama }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->part_number }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->merk }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->nama_mekanik }}</td>
                        <td class="text-center">
                            <button class="btn btn-primary" data-id="{{ $item3->detailRkbUrgent->id }}" onclick="showDokumentasi({{ $item3->detailRkbUrgent->id }})">
                                <i class="bi bi-file-earmark-text"></i>
                            </button>
                        </td>

                        @if (!in_array($kodeAlat, $processedAlat))
                            <!-- Jika belum diproses -->
                            <td class="text-center" rowspan="{{ $alatRowCount[$kodeAlat] }}">
                                <a class="btn btn-primary" href="{{ route('rkb_urgent.detail.timeline.index', ['id' => $item2->id]) }}">
                                    <i class="bi bi-hourglass-split"></i>
                                </a>
                            </td>

                            <td class="text-center" rowspan="{{ $alatRowCount[$kodeAlat] }}">
                                <button class="btn {{ $item2->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $item2->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $item2->id }}" data-id-lampiran="{{ $item2->lampiranRkbUrgent ? $item2->lampiranRkbUrgent->id : null }}" title="Unggah Lampiran" aria-label="Unggah Lampiran">
                                    <i class="bi bi-paperclip"></i>
                                </button>
                            </td>

                            @php
                                $processedAlat[] = $kodeAlat; // Tandai kode alat sebagai sudah diproses
                            @endphp
                        @else
                            <td style="display: none;"></td> <!-- Placeholder untuk konsistensi kolom Timeline -->
                            <td style="display: none;"></td> <!-- Placeholder untuk konsistensi kolom Lampiran -->
                        @endif

                        <td class="text-center">{{ $item3->detailRkbUrgent->quantity_requested }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->quantity_approved ?? '-' }}</td>
                        <td class="text-center">{{ $item3->detailRkbUrgent->satuan }}</td>
                        <td class="text-center">
                            <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item3->detailRkbUrgent->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        var table = $('#table-data').DataTable({
            ordering: false,
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
