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
            width: 160px;
            height: 160px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

@include('dashboard.evaluasi.urgent.detail.partials.modal-preview')
@include('dashboard.evaluasi.urgent.detail.partials.modal-lampiran')

<form id="approveRkbForm" method="POST" action="">
    @csrf
    <div class="ibox-body table-responsive p-0 m-0">
        <table class="table table-hover table-bordered table-striped align-middle w-100" id="table-data">
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
                    <th>Quantity in Stock</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    @forelse ($item->linkRkbDetails as $detail)
                        <tr>
                            <td>{{ $detail->linkAlatDetailRkb->masterDataAlat->jenis_alat }}</td>
                            <td>{{ $detail->linkAlatDetailRkb->masterDataAlat->kode_alat }}</td>
                            <td>{{ $item->kategoriSparepart->kode }}: {{ $item->kategoriSparepart->nama }}</td>
                            <td>{{ $item->masterDataSparepart->nama }}</td>
                            <td>{{ $item->masterDataSparepart->part_number }}</td>
                            <td>{{ $item->masterDataSparepart->merk }}</td>
                            <td>{{ $detail->linkAlatDetailRkb->nama_koordinator }}</td>
                            <td>
                                <button class="btn {{ $item->dokumentasi ? 'btn-warning' : 'btn-primary' }}" data-id="{{ $item->id }}" type="button" onclick="event.preventDefault(); event.stopPropagation(); showDokumentasi({{ $item->id }});">
                                    <i class="bi bi-file-earmark-text"></i>
                                </button>
                            </td>
                            <td>
                                <a class="btn {{ $detail->linkAlatDetailRkb->timelineRkbUrgents->count() > 0 ? 'btn-warning' : 'btn-primary' }}" href="{{ route('evaluasi_rkb_urgent.detail.timeline.index', ['id' => $detail->linkAlatDetailRkb->id]) }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-hourglass-split"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn {{ $detail->linkAlatDetailRkb->lampiranRkbUrgent ? 'btn-warning' : 'btn-primary' }} lampiranBtn" data-bs-toggle="modal" data-bs-target="{{ $detail->linkAlatDetailRkb->lampiranRkbUrgent ? '#modalForLampiranExist' : '#modalForLampiranNew' }}" data-id-linkalatdetail="{{ $detail->linkAlatDetailRkb->id }}" data-id-lampiran="{{ $detail->linkAlatDetailRkb->lampiranRkbUrgent ? $detail->linkAlatDetailRkb->lampiranRkbUrgent->id : null }}" type="button" onclick="event.stopPropagation();">
                                    <i class="bi bi-paperclip"></i>
                                </button>
                            </td>
                            <td>{{ $item->quantity_requested }}</td>
                            <td>
                                <input class="form-control text-center 
                                    @if ($rkb->is_approved_svp) bg-primary-subtle
                                    @elseif ($rkb->is_approved_vp) bg-info-subtle
                                    @elseif($rkb->is_evaluated) bg-success-subtle 
                                    @else bg-warning-subtle @endif" name="quantity_approved[{{ $item->id }}]" type="number" value="{{ $item->quantity_approved ?? $item->quantity_requested }}" min="0" {{ $rkb->is_evaluated ? 'disabled' : '' }}>
                            </td>
                            <td>{{ $stockQuantities[$item->id_master_data_sparepart] ?? 0 }}</td>
                            <td>{{ $item->satuan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="14">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No RKB details found
                            </td>
                        </tr>
                    @endforelse
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
    <button class="btn btn-success btn-sm approveBtn" id="hiddenApproveRkbButton" type="submit" hidden></button>
</form>

@push('scripts_3')
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

    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
