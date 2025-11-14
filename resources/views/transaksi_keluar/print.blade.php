<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Transaksi #{{ $transaksi->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #fff;
            color: #333;
        }

        .invoice {
            max-width: 800px;
            margin: auto;
            padding: 40px;
        }

        .invoice-header {
            border-bottom: 2px solid #007bff;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .invoice-title {
            font-weight: 700;
            color: #007bff;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .text-end {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice" id="print-area">
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="invoice-title">Faktur Transaksi</h2>
                <p>No. Faktur: <strong>#{{ $transaksi->kode_transaksi }}</strong></p>
                <p>Tanggal: {{ $transaksi->created_at->format('d/m/Y') }}</p>
            </div>
            <img src="https://i.ibb.co.com/FNckrp1/image.jpg" alt="Logo" height="60">
        </div>

        <div class="mb-4">
            <h5>Pelanggan:</h5>
            <p>
                {{ $transaksi->customer }}<br>
            </p>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi->details as $i => $detail)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $detail->barang->nama_barang }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-end">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($detail->jumlah * $detail->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th class="text-end">Rp
                        {{ number_format($transaksi->details->sum(fn($d) => $d->jumlah * $d->harga_jual), 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <div class="text-end mt-5">
            <p>Admin: <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong></p>
            <p><small>Dicetak pada {{ now()->format('d/m/Y H:i') }}</small></p>
        </div>

        <div class="no-print text-center mt-4">
            <button class="btn btn-primary" onclick="downloadPDF()">🖨️ Cetak Faktur</button>
            <a href="{{ route('transaksi-keluar.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        const element = document.getElementById('print-area');
        const options = {
            margin: 0.5,
            filename: 'faktur.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(options).from(element).save();
    }
</script>

</html>