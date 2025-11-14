@extends('layout')

@section('title', 'Transaksi Keluar')

@section('content')


    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('transaksi-keluar.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter" class="form-label">Filter Berdasarkan</label>
                    <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
                        <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>Mingguan</option>
                        <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Bulanan</option>
                        <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>

                @if($filter == 'week')
                    <div class="col-md-3">
                        <label for="start" class="form-label">Mulai Minggu</label>
                        <input type="date" name="start" id="start" class="form-control"
                            value="{{ request('start', now()->startOfWeek()->toDateString()) }}">
                    </div>
                @elseif($filter == 'month')
                    <div class="col-md-2">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == request('month', now()->month) ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="year" class="form-label">Tahun</label>
                        <input type="number" name="year" id="year" class="form-control"
                            value="{{ request('year', now()->year) }}">
                    </div>
                @elseif($filter == 'year')
                    <div class="col-md-2">
                        <label for="year" class="form-label">Tahun</label>
                        <input type="number" name="year" id="year" class="form-control"
                            value="{{ request('year', now()->year) }}">
                    </div>
                @endif

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>

            @if(!empty($label))
                <p class="mt-3 fw-bold text-primary">Periode: {{ $label }}</p>
            @endif
        </div>
    </div>


    <div class="d-flex justify-content-between mb-3">
        <h2>Transaksi Keluar</h2>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('transaksi-keluar.export', request()->query()) }}" class="btn btn-success">
                📤 Export ke Excel
            </a>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                🖨️ Cetak Halaman ini
            </button>
            <ul class="dropdown-menu">
                <li><button class="btn btn-primary" onclick="downloadPDF()">🖨️ Cetak Keseluruhan Transaksi Keluar</button>
                </li>
                <li><button class="btn btn-primary" onclick="downloadPDFtransaksikeluar()">🖨️ Cetak
                        Transaksi Keluar</button>
                </li>
                <li><button class="btn btn-primary" onclick="downloadPDFbarangkeluar()">🖨️ Cetak Barang Keluar</button>
                </li>
            </ul>
        </div>
        <a href="{{ route('transaksi-keluar.create') }}" class="btn btn-success">+ Tambah Transaksi Keluar</a>
    </div>
    <div id="print-area">
        <div id="print-transaksi-keluar">
            @if(!empty($label))
                <p class="mt-3 fw-bold text-primary">Periode: {{ $label }}</p>
            @endif
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Keterangan Transaksi</th>
                        <th>Jumlah</th>
                        <th>Harga Jual</th>
                        <th>Pendapatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($transaksiKeluars as $trx)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $trx->kode_transaksi }}</td>
                                        <td>{{ $trx->customer }}</td>
                                        <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>

                                        {{-- Kolom barang --}}
                                        <td>
                                            <ul class="mb-0 ps-3">
                                                @foreach($trx->details as $detail)
                                                    <li>{{ $detail->barang->nama_barang }}</li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        <td>{{ $trx->keterangan_keluar }}</td>

                                        {{-- Kolom jumlah --}}
                                        <td>
                                            <ul class="mb-0 ps-3">
                                                @foreach($trx->details as $detail)
                                                    <li>{{ $detail->jumlah }}</li>
                                                @endforeach
                                            </ul>
                                        </td>

                                        {{-- Kolom harga jual --}}
                                        <td>
                                            <ul class="mb-0 ps-3">
                                                @foreach($trx->details as $detail)
                                                    <li>Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            Rp
                                            {{number_format(
                            $trx->details->sum(function ($detail) {
                                return $detail->harga_jual * $detail->jumlah; }),
                            0,
                            ',',
                            '.'
                        )
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('transaksi-keluar.print', $trx->id) }}" class="btn btn-sm btn-outline-primary"
                                                target="_blank">
                                                <i class="bi bi-printer"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div id="print-barang-keluar">
            <h3 class="mt-5">Data Barang Yang Terjual</h3>
            <div class="mt-3">
                @if(!empty($label))
                    <p class="mt-3 fw-bold text-primary">Periode: {{ $label }}</p>
                @endif
                <table class="table table-bordered table-striped">
                    <thead class="table-success">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jumlahbarang as $item)
                            <tr>
                                <td>{{ $item->barang->nama_barang }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp {{ number_format($item->pendapatan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('print-area');
            const options = {
                margin: 0.5,
                filename: 'faktur_transaksi_keluar.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>
    <script>
        function downloadPDFtransaksikeluar() {
            const element = document.getElementById('print-transaksi-keluar');
            const options = {
                margin: 0.5,
                filename: 'faktur_transaksi_keluar.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>
    <script>
        function downloadPDFbarangkeluar() {
            const element = document.getElementById('print-barang-keluar');
            const options = {
                margin: 0.5,
                filename: 'faktur_transaksi_keluar.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>

@endsection