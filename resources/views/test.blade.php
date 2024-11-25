<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTables Merge Example</title>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <table class="display" id="example" style="width:100%">
        <thead>
            <tr>
                <th>Nama Alat</th>
                <th>Kategori Sparepart</th>
                <th>Sparepart</th>
                <th>Quantity Requested</th>
                <th>Quantity Approved</th>
                <th>Quantity in Stock</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Alat 1</td>
                <td>Kategori A</td>
                <td>Sparepart X</td>
                <td>5</td>
                <td>5</td>
                <td>50</td>
                <td>PCS</td>
                <td>Edit</td>
            </tr>
            <tr>
                <td>Alat 2</td>
                <td>Kategori B</td>
                <td>Sparepart X</td>
                <td>10</td>
                <td>10</td>
                <td>50</td>
                <td>PCS</td>
                <td>Edit</td>
            </tr>
            <tr>
                <td>Alat 3</td>
                <td>Kategori C</td>
                <td>Sparepart Y</td>
                <td>3</td>
                <td>3</td>
                <td>30</td>
                <td>PCS</td>
                <td>Edit</td>
            </tr>
            <tr>
                <td>Alat 4</td>
                <td>Kategori D</td>
                <td>Sparepart Z</td>
                <td>8</td>
                <td>6</td>
                <td>20</td>
                <td>PCS</td>
                <td>Edit</td>
            </tr>
            <tr>
                <td>Alat 5</td>
                <td>Kategori E</td>
                <td>Sparepart Z</td>
                <td>4</td>
                <td>4</td>
                <td>20</td>
                <td>PCS</td>
                <td>Edit</td>
            </tr>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                order: [
                    [2, 'asc']
                ], // Urutkan berdasarkan Sparepart
                drawCallback: function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var lastSparepart = null;
                    var lastStockCell = null;
                    var rowspan = 1;

                    api.rows({
                        page: 'current'
                    }).data().each(function(row, i) {
                        var sparepart = row[2]; // Kolom Sparepart
                        var stockCell = $('td:nth-child(6)', rows[i]); // Kolom Quantity in Stock

                        if (sparepart === lastSparepart) {
                            rowspan++; // Tambahkan rowspan
                            stockCell.remove(); // Hapus sel stock pada baris ini
                            $(lastStockCell).attr('rowspan', rowspan); // Set rowspan pada sel pertama
                        } else {
                            lastSparepart = sparepart;
                            lastStockCell = stockCell; // Simpan referensi ke sel stock terakhir
                            rowspan = 1; // Reset rowspan
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
