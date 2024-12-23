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
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

@include('dashboard.evaluasi.urgent.detail.partials.modal-preview')

@include('dashboard.evaluasi.urgent.detail.partials.modal-lampiran')

<div class="ibox-body ms-0 ps-0 table-responsive" style="overflow-x: hidden">
    <form id="approveRkbForm" method="POST" action="{{ route('evaluasi_rkb_urgent.detail.approve', ['id' => $data->id]) }}">
        @csrf
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
                    <th class="text-center">Quantity in Stock</th>
                    <th class="text-center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $alatRowCount = [];
                    $processedAlat = [];
                @endphp

                @foreach ($data->linkAlatDetailRkbs as $item2)
                    @foreach ($item2->linkRkbDetails as $item3)
                        @php
                            $kodeAlat = $item2->masterDataAlat->kode_alat;
                            if (!isset($alatRowCount[$kodeAlat])) {
                                $alatRowCount[$kodeAlat] = collect($item2->linkRkbDetails)->count();
                            }
                        @endphp

                        <tr>
                            <td class="text-center">{{ $item2->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $item2->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->kategoriSparepart->kode }}:
                                {{ $item3->detailRkbUrgent->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->part_number }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->nama_mekanik }}</td>
                            <td class="text-center">
                                <button class="btn {{ $item3->detailRkbUrgent->dokumentasi ? 'btn-warning' : 'btn-primary' }}" data-id="{{ $item3->detailRkbUrgent->id }}" type="button" onclick="event.preventDefault(); event.stopPropagation(); showDokumentasi({{ $item3->detailRkbUrgent->id }});">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                            </td>

                            @if (!in_array($kodeAlat, $processedAlat))
                                <td class="text-center" rowspan="{{ $alatRowCount[$kodeAlat] }}">
                                    <a class="btn {{ $item2->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('evaluasi_rkb_urgent.detail.timeline.index', ['id' => $item2->id]) }}" onclick="event.stopPropagation();">
                                        <i class="bi bi-hourglass-split"></i>
                                    </a>
                                </td>

                                <td class="text-center" rowspan="{{ $alatRowCount[$kodeAlat] }}">
                                    <button class="btn {{ $item2->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $item2->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $item2->id }}" data-id-lampiran="{{ $item2->lampiranRkbUrgent ? $item2->lampiranRkbUrgent->id : null }}" type="button" onclick="event.stopPropagation();">
                                        <i class="bi bi-paperclip"></i>
                                    </button>
                                </td>

                                @php
                                    $processedAlat[] = $kodeAlat;
                                @endphp
                            @else
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                            @endif

                            <td class="text-center">{{ $item3->detailRkbUrgent->quantity_requested }}</td>
                            <td class="text-center">
                                @php
                                    $backgroundColor = '';
                                    if ($data->is_approved) {
                                        $backgroundColor = 'bg-primary-subtle';
                                    } else {
                                        $backgroundColor = $item3->detailRkbUrgent->quantity_approved !== null ? 'bg-success-subtle' : 'bg-warning-subtle';
                                    }

                                    $disabled = $data->is_approved || $data->is_evaluated ? 'disabled' : '';
                                @endphp
                                <input class="form-control text-center {{ $backgroundColor }}" name="quantity_approved[{{ $item3->detailRkbUrgent->id }}]" type="number" value="{{ $item3->detailRkbUrgent->quantity_approved ?? $item3->detailRkbUrgent->quantity_requested }}" min="0" {{ $disabled }} />
                            </td>
                            <td class="text-center">{{ random_int(1, 15) }}</td>
                            <td class="text-center">{{ $item3->detailRkbUrgent->satuan }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </form>
</div>

@push('scripts_3')
    <script>
        var table = $('#table-data').DataTable({
            paginate: false,
            ordering: false,
        });

        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

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
