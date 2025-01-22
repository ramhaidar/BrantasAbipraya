@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            /* padding: 4px 8px; */
            vertical-align: middle;
        }

        .img-thumbnail {
            width: 160px;
            height: 160px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

@include('dashboard.evaluasi.urgent.detail.partials.modal-preview')

@include('dashboard.evaluasi.urgent.detail.partials.modal-lampiran')

<form id="approveRkbForm" method="POST" action="{{ route('evaluasi_rkb_urgent.detail.approve', ['id' => $rkb->id]) }}">
    @csrf
    <div class="ibox-body ms-0 ps-0 table-responsive">
        <table class="m-0 table table-bordered table-striped" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Nama Alat</th>
                    <th class="text-center">Kode Alat</th>
                    <th class="text-center">Kategori Sparepart</th>
                    <th class="text-center">Sparepart</th>
                    <th class="text-center">Part Number</th>
                    <th class="text-center">Merk</th>
                    <th class="text-center">Nama Koordinator</th>
                    <th class="text-center">Dokumentasi</th>
                    <th class="text-center">Timeline</th>
                    <th class="text-center">Lampiran</th>
                    <th class="text-center">Quantity Requested</th>
                    <th class="text-center">Quantity Approved</th>
                    <th class="text-center">Quantity in Stock</th>
                    <th class="text-center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $currentPartNumber = null;
                    $rowspan = 0;
                    $showStock = true;
                    $alatRowCount = [];
                    $processedAlat = [];
                @endphp

                @foreach ($alat_detail_rkbs as $alat_detail)
                    @foreach ($alat_detail->linkRkbDetails as $rkb_detail)
                        @php
                            $kodeAlat = $alat_detail->masterDataAlat->kode_alat;
                            $sparepart = $rkb_detail->detailRkbUrgent->masterDataSparepart;

                            if (!isset($alatRowCount[$kodeAlat])) {
                                $alatRowCount[$kodeAlat] = collect($alat_detail->linkRkbDetails)->count();
                            }

                            if ($currentPartNumber !== $sparepart->part_number) {
                                $currentPartNumber = $sparepart->part_number;
                                $rowspan = $alat_detail_rkbs
                                    ->flatMap(function ($item) use ($currentPartNumber) {
                                        return $item->linkRkbDetails->filter(function ($detail) use ($currentPartNumber) {
                                            return $detail->detailRkbUrgent->masterDataSparepart->part_number === $currentPartNumber;
                                        });
                                    })
                                    ->count();
                                $showStock = true;
                            }
                        @endphp

                        <tr>
                            <td class="text-center">{{ $alat_detail->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $alat_detail->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $rkb_detail->detailRkbUrgent->kategoriSparepart->kode }}:
                                {{ $rkb_detail->detailRkbUrgent->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $sparepart->nama }}</td>
                            <td class="text-center">{{ $sparepart->part_number }}</td>
                            <td class="text-center">{{ $sparepart->merk }}</td>
                            <td class="text-center">{{ $rkb_detail->detailRkbUrgent->nama_koordinator }}</td>
                            <td class="text-center">
                                <button class="btn {{ $rkb_detail->detailRkbUrgent->dokumentasi ? 'btn-warning' : 'btn-primary' }}" data-id="{{ $rkb_detail->detailRkbUrgent->id }}" type="button" onclick="event.preventDefault(); event.stopPropagation(); showDokumentasi({{ $rkb_detail->detailRkbUrgent->id }});">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                            </td>

                            @if (!in_array($kodeAlat, $processedAlat))
                                <td class="text-center" rowspan="{{ $alatRowCount[$kodeAlat] }}">
                                    <a class="btn {{ $alat_detail->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('evaluasi_rkb_urgent.detail.timeline.index', ['id' => $alat_detail->id]) }}" onclick="event.stopPropagation();">
                                        <i class="bi bi-hourglass-split"></i>
                                    </a>
                                </td>

                                <td class="text-center" rowspan="{{ $alatRowCount[$kodeAlat] }}">
                                    <button class="btn {{ $alat_detail->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $alat_detail->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $alat_detail->id }}" data-id-lampiran="{{ $alat_detail->lampiranRkbUrgent ? $alat_detail->lampiranRkbUrgent->id : null }}" type="button" onclick="event.stopPropagation();">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                </td>

                                @php
                                    $processedAlat[] = $kodeAlat;
                                @endphp
                            @else
                                <td style="display: none;">{{ $alat_detail->timelineRkbUrgents->count() }}</td>
                                <td style="display: none;">{{ $alat_detail->lampiranRkbUrgent ? 1 : 0 }}</td>
                            @endif

                            <td class="text-center">{{ $rkb_detail->detailRkbUrgent->quantity_requested }}</td>
                            <td class="text-center">
                                @php
                                    $backgroundColor = '';
                                    if ($rkb->is_approved_svp) {
                                        $backgroundColor = 'bg-primary-subtle';
                                    } elseif ($rkb->is_approved_vp) {
                                        $backgroundColor = 'bg-info-subtle';
                                    } else {
                                        $backgroundColor = $rkb_detail->detailRkbUrgent->quantity_approved !== null ? 'bg-success-subtle' : 'bg-warning-subtle';
                                    }

                                    $disabled = $rkb->is_approved_vp || $rkb->is_approved_svp || $rkb->is_evaluated ? 'disabled' : '';
                                @endphp
                                <input class="form-control text-center {{ $backgroundColor }}" name="quantity_approved[{{ $rkb_detail->detailRkbUrgent->id }}]" type="number" value="{{ $rkb_detail->detailRkbUrgent->quantity_approved ?? $rkb_detail->detailRkbUrgent->quantity_requested }}" min="0" {{ $disabled }} />
                            </td>

                            @if ($showStock)
                                <td class="text-center" rowspan="{{ $rowspan }}">
                                    {{ $stockQuantities[$sparepart->id] ?? 0 }}
                                </td>
                                @php $showStock = false; @endphp
                            @else
                                <td style="display: none;">{{ $stockQuantities[$sparepart->id] ?? 0 }}</td>
                            @endif

                            <td class="text-center">{{ $rkb_detail->detailRkbUrgent->satuan }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</form>

@push('scripts_3')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });

            const dokumentasiPreviewContainer = document.getElementById('dokumentasiPreviewContainer');
            const largeImagePreviewForShow = document.getElementById('largeImagePreviewForShow');
            const dokumentasiPreviewModal = new bootstrap.Modal(document.getElementById('dokumentasiPreviewModal'));
            const imagePreviewModalforShow = new bootstrap.Modal(document.getElementById(
                'imagePreviewModalforShow'));

            const dokumentasiRoute = @json(route('evaluasi_rkb_urgent.detail.dokumentasi', ['id' => ':id']));

            window.showDokumentasi = function(id) {
                dokumentasiPreviewContainer.innerHTML = '';

                const fetchUrl = dokumentasiRoute.replace(':id', id);

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

                                img.addEventListener('click', () => {
                                    $('#dokumentasiPreviewModal').modal('hide');

                                    largeImagePreviewForShow.src = file.url;
                                    document.getElementById('imagePreviewTitleForShow')
                                        .textContent = file.name;
                                    imagePreviewModalforShow.show();
                                });

                                dokumentasiPreviewContainer.appendChild(img);
                            });
                        } else {
                            dokumentasiPreviewContainer.innerHTML =
                                '<p class="text-muted text-center">Tidak ada Dokumentasi</p>';
                        }

                        dokumentasiPreviewModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching dokumentasi:', error);
                        dokumentasiPreviewContainer.innerHTML =
                            '<p class="text-danger text-center">Failed to load dokumentasi</p>';
                        dokumentasiPreviewModal.show();
                    });
            };

            document.getElementById('imagePreviewModalforShow').addEventListener('hidden.bs.modal', function() {
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
