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
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                {{-- <th class="text-center">No</th> --}}
                <th class="text-center">Nama Alat</th>
                <th class="text-center">Kode Alat</th>
                <th class="text-center">Sparepart</th>
                <th class="text-center">Part Number</th>
                <th class="text-center">Merk</th>
                {{-- <th class="text-center">Spesifikasi / Tipe / Nomor Seri</th> --}}
                <th class="text-center">Quantity</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Jumlah Harga</th>
            </tr>
        </thead>
        <tbody>
            {{ $rkb->linkAlatDetailRkbs }}
            @foreach ($rkb->linkAlatDetailRkbs as $detail1)
                @foreach ($detail1->linkRkbDetails as $detail2)
                    <tr>
                        {{-- <td class="text-center">{{ $loop->iteration }}</td> --}}
                        <td class="text-center">{{ $detail1->masterDataAlat->jenis_alat }}</td>
                        <td class="text-center">{{ $detail1->masterDataAlat->kode_alat }}</td>
                        <td class="text-center">{{ $detail2->detailRkbUrgent->masterDataSparepart->nama ?? $detail2->detailRkbGeneral->masterDataSparepart->nama }}</td>
                        <td class="text-center">{{ $detail2->detailRkbUrgent->masterDataSparepart->part_number ?? $detail2->detailRkbGeneral->masterDataSparepart->part_number }}</td>
                        <td class="text-center">{{ $detail2->detailRkbUrgent->masterDataSparepart->merk ?? $detail2->detailRkbGeneral->masterDataSparepart->merk }}</td>
                        <td class="text-center">{{ $detail2->detailRkbUrgent->quantity_approved ?? $detail2->detailRkbGeneral->quantity_approved }}</td>
                        <td class="text-center">{{ $detail2->detailRkbUrgent->satuan ?? $detail2->detailRkbGeneral->satuan }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                // pageLength: 0,
                paginate: false,
                ordering: false,
                // lengthMenu: [
                //     [100],
                //     [100]
                // ],
            });
        });
    </script>
@endpush
