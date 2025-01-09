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
    </style>
@endpush

@php
    function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
@endphp

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Nomor SPB</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Supplier</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Net</th>
                <th class="text-center">PPN</th>
                <th class="text-center">Bruto</th>
                <th class="text-center">STT</th>
                <th class="text-center">Dokumentasi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($atbs as $atb)
                <tr>
                    <td class="text-center spb-number" data-spb="{{ $atb->spb->nomor }}" data-id="{{ $atb->id }}" data-stt="{{ $atb->surat_tanda_terima }}">{{ $atb->spb->nomor }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($atb->tanggal)->translatedFormat('l, d F Y') }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->kategoriSparepart->kode }}: {{ $atb->masterDataSparepart->kategoriSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSupplier->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->nama }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->merk }}</td>
                    <td class="text-center">{{ $atb->masterDataSparepart->part_number }}</td>
                    <td class="text-center">{{ $atb->quantity }}</td>
                    <td class="text-center">{{ $atb->detailSpb->satuan }}</td>
                    <td class="text-center">{{ formatRupiah($atb->harga) }}</td>
                    <td class="text-center">{{ formatRupiah($atb->quantity * $atb->harga) }}</td>
                    <td class="text-center">{{ formatRupiah($atb->quantity * $atb->harga * 0.11) }}</td>
                    <td class="text-center">{{ formatRupiah($atb->quantity * $atb->harga * 1.11) }}</td>
                    <td class="text-center stt-cell">
                        <button class="btn btn-primary mx-1 sttBtn" onclick="showSTTModal('{{ $atb->id }}')">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </button>
                    </td>
                    <td class="text-center doc-cell" data-id="{{ $atb->id }}">
                        @php
                            $storagePath = storage_path('app/public/' . $atb->dokumentasi_foto);
                            $hasImages = false;
                            if ($atb->dokumentasi_foto && is_dir($storagePath)) {
                                $files = glob($storagePath . '/*.{jpg,jpeg,png,heic}', GLOB_BRACE);
                                $hasImages = !empty($files);
                            }
                        @endphp
                        <button class="btn {{ $hasImages ? 'btn-primary' : 'btn-secondary' }} mx-1" onclick="showDokumentasiModal('{{ $atb->id }}')" {{ !$hasImages ? 'disabled' : '' }}>
                            <i class="bi bi-images"></i>
                        </button>
                    </td>
                    <td class="text-center action-cell">
                        <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $atb->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Include dokumentasi modal -->
@include('dashboard.atb.partials.modal-dokumentasi')

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });

            // Group rows by SPB number and STT
            function groupRows() {
                let groups = {};

                $('#table-data tbody tr').each(function() {
                    const $row = $(this);
                    const $spbCell = $row.find('.spb-number');
                    const spbNumber = $spbCell.data('spb');
                    const stt = $spbCell.data('stt');
                    const id = $spbCell.data('id');
                    const groupKey = `${spbNumber}_${stt}`;

                    if (!groups[groupKey]) {
                        groups[groupKey] = {
                            spbNumber,
                            stt,
                            firstRow: $row,
                            rows: [$row],
                            ids: [id]
                        };
                    } else {
                        groups[groupKey].rows.push($row);
                        groups[groupKey].ids.push(id);
                        $spbCell.remove();
                    }
                });

                return groups;
            }

            // Apply rowspans to grouped cells
            function applyRowspans(groups) {
                Object.values(groups).forEach(group => {
                    const rowCount = group.rows.length;
                    if (rowCount > 1) {
                        // Find first instance of this SPB number
                        const $spbCell = group.firstRow.find('.spb-number');
                        const $actionCell = group.firstRow.find('.action-cell');
                        const $sttCell = group.firstRow.find('.stt-cell');

                        // Set rowspans
                        $spbCell.attr('rowspan', rowCount);
                        $actionCell.attr('rowspan', rowCount);
                        $sttCell.attr('rowspan', rowCount);

                        // Remove extra cells from subsequent rows
                        group.rows.slice(1).forEach($row => {
                            $row.find('.action-cell, .stt-cell').remove();
                        });

                        // Update delete button data
                        $actionCell.find('.deleteBtn').data('ids', group.ids.join(','));
                    }
                });
            }

            // Execute grouping
            const groups = groupRows();
            applyRowspans(groups);
        });
    </script>
@endpush
