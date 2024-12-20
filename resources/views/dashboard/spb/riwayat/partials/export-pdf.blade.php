<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pemesanan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: left;
            margin-left: 35px;
            margin-bottom: 10px;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 4px;
            /* Padding lebih kecil */
            text-align: center;
            font-size: 9px;
            /* Ukuran font dalam tabel lebih kecil */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tfoot th {
            text-align: center;
            padding-right: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <p><strong style="text-decoration: underline;">Lampiran : Surat Pesanan Barang (SPB)</strong></p>
        <p><strong>Nomor : {{ $spb->nomor }}</strong></p>
        <p><strong>Tanggal : {{ \Carbon\Carbon::parse($spb->tanggal)->isoFormat('DD MMMM YYYY') }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>JENIS BARANG</th>
                <th>MERK</th>
                <th>SPESIFIKASI/TIPE/NO SERI</th>
                <th>Jumlah</th>
                <th>Sat</th>
                <th>Harga</th>
                <th>Jumlah Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($spb->linkSpbDetailSpb as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left">{{ $item->detailSpb->masterDataSparepart->nama }}</td>
                    <td>{{ $item->detailSpb->masterDataSparepart->merk ?? '-' }}</td>
                    <td>{{ $item->detailSpb->masterDataSparepart->part_number }}</td>
                    <td>{{ $item->detailSpb->quantity }}</td>
                    <td>{{ $item->detailSpb->satuan }}</td>
                    <td>Rp {{ number_format($item->detailSpb->harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->detailSpb->quantity * $item->detailSpb->harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th style="text-align: left" colspan="7">Jumlah</th>
                <th>Rp {{ number_format($totalJumlahHarga, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th style="text-align: left" colspan="7">PPN 11%</th>
                <th>Rp {{ number_format($ppn, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th style="text-align: left" colspan="7">Grand Total</th>
                <th>Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
