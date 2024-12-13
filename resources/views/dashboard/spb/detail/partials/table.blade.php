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
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Jenis Barang</th>
                <th class="text-center">Merk</th>
                <th class="text-center">Spesifikasi / Tipe / Nomor Seri</th>
                <th class="text-center">Jumlah</th>
                <th class="text-center">Satuan</th>
                <th class="text-center">Harga</th>
                <th class="text-center">Jumlah Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Catridge Oil Filter</td>
                <td>GB</td>
                <td>6736-51-5142</td>
                <td>4</td>
                <td>Pcs</td>
                <td>235.000</td>
                <td>940.000</td>
            </tr>
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                // pageLength: 0,
                paginate: false,
                // lengthMenu: [
                //     [100],
                //     [100]
                // ],
            });
        });
    </script>
@endpush
