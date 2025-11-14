<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Keluar</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Laporan Transaksi Keluar</h2>
    <p><strong>Periode:</strong> {{ $label }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Harga Jual</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksiKeluars as $i => $trx)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $trx->kode_transaksi }}</td>
                    <td>{{ $trx->customer }}</td>
                    <td>{{ $trx->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        @foreach($trx->details as $d)
                            {{ $d->barang->nama_barang }} ({{ $d->jumlah }})<br>
                        @endforeach
                    </td>
                    <td>
                        Rp {{ number_format($trx->details->sum(fn($d) => $d->jumlah * $d->harga_jual), 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>