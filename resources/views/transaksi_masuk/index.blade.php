@extends('layout')

@section('title', 'Transaksi Masuk')

@section('content')

    {{-- FILTER --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('transaksi-masuk.index') }}" method="GET" class="row g-3 align-items-end">
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

    {{-- TABEL TRANSAKSI MASUK --}}
    <div class="d-flex justify-content-between mb-3">
        <h2>Transaksi Masuk</h2>
        <a href="{{ route('transaksi-masuk.export', request()->query()) }}" class="btn btn-success">
            📤 Export ke Excel
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                🖨️ Cetak Halaman ini
            </button>
            <ul class="dropdown-menu">
                <li><button class="btn btn-primary" onclick="downloadPDF()">🖨️ Cetak Keseluruhan Transaksi Masuk</button>
                </li>
                <li><button class="btn btn-primary" onclick="downloadPDFtransaksimasuk()">🖨️ Cetak Transaksi Masuk</button>
                </li>
                <li><button class="btn btn-primary" onclick="downloadPDFbarangmasuk()">🖨️ Cetak Barang Masuk</button>
                </li>
            </ul>
        </div>
        <a href="{{ route('transaksi-masuk.create') }}" class="btn btn-success">+ Tambah Transaksi Masuk</a>
    </div>

    <div id="print-area">
        <div id="print-transaksi-masuk">
            @if(!empty($label))
                <p class="mt-3 fw-bold text-primary">Periode: {{ $label }}</p>
            @endif
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>
                            <a href="{{ route('transaksi-masuk.index', array_merge(request()->all(), [
        'sort_by' => 'kode_transaksi',
        'sort_order' => ($sortBy === 'kode_transaksi' && $sortOrder === 'asc') ? 'desc' : 'asc'
    ])) }}">
                                Kode Transaksi
                                @if($sortBy === 'kode_transaksi')
                                    {!! $sortOrder === 'asc' ? '&#9650;' : '&#9660;' !!}
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('transaksi-masuk.index', array_merge(request()->all(), [
        'sort_by' => 'created_at',
        'sort_order' => ($sortBy === 'created_at' && $sortOrder === 'asc') ? 'desc' : 'asc'
    ])) }}">
                                Tanggal
                                @if($sortBy === 'created_at')
                                    {!! $sortOrder === 'asc' ? '&#9650;' : '&#9660;' !!}
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('transaksi-masuk.index', array_merge(request()->all(), [
        'sort_by' => 'supplier',
        'sort_order' => ($sortBy === 'supplier' && $sortOrder === 'asc') ? 'desc' : 'asc'
    ])) }}">
                                Supplier
                                @if($sortBy === 'supplier')
                                    {!! $sortOrder === 'asc' ? '&#9650;' : '&#9660;' !!}
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('transaksi-masuk.index', array_merge(request()->all(), [
        'sort_by' => 'pegawai_penerima',
        'sort_order' => ($sortBy === 'pegawai_penerima' && $sortOrder === 'asc') ? 'desc' : 'asc'
    ])) }}">
                                Pegawai
                                @if($sortBy === 'pegawai_penerima')
                                    {!! $sortOrder === 'asc' ? '&#9650;' : '&#9660;' !!}
                                @endif
                            </a>
                        </th>
                        <th>Keterangan Transaksi</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                    </tr>
                </thead>

                <tbody>
                    @php $no = 1; @endphp
                    @foreach($transaksiMasuks as $trx)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $trx->kode_transaksi }}</td>
                            <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $trx->supplier }}</td>
                            <td>{{ $trx->pegawai_penerima }}</td>
                            <td>{{ $trx->keterangan_masuk }}</td>
                            {{-- Barang --}}
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach($trx->details as $detailBarang)
                                        <li>{{ $detailBarang->barang->nama_barang }}</li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- Jumlah --}}
                            <td>
                                <ul class="mb-0 list-unstyled">
                                    @foreach($trx->details as $detailBarang)
                                        <li>{{ $detailBarang->jumlah }}</li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- Harga Beli --}}
                            <td>
                                <ul class="mb-0 list-unstyled">
                                    @foreach($trx->details as $detailBarang)
                                        <li>Rp {{ number_format($detailBarang->harga_beli, 0, ',', '.') }} / Item</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        {{-- DATA BARANG MASUK --}}
        <div id="print-barang-masuk">
            <h3 class="mt-5">Data Barang Yang Dibeli</h3>
            @if(!empty($label))
                <p class="mt-3 fw-bold text-primary">Periode: {{ $label }}</p>
            @endif
            <div class="mt-3">
                <table class="table table-bordered table-striped">
                    <thead class="table-success">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jumlahbarang as $item)
                            <tr>
                                <td>{{ $item->barang->nama_barang }}</td>
                                <td>{{ $item->jumlah }}</td>
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
                filename: 'faktur_transaksi_masuk.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>
    <script>
        function downloadPDFtransaksimasuk() {
            const element = document.getElementById('print-transaksi-masuk');
            const options = {
                margin: 0.5,
                filename: 'faktur_transaksi_masuk.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>
    <script>
        function downloadPDFbarangmasuk() {
            const element = document.getElementById('print-barang-masuk');
            const options = {
                margin: 0.5,
                filename: 'faktur_transaksi_masuk.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            html2pdf().set(options).from(element).save();
        }
    </script>
    {{-- function downloadPDF() {
    const element = document.getElementById('print-area');
    const opt = {
    margin: 0.5,
    filename: 'faktur.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
    };
    html2pdf().set(opt).from(element).save();
    } --}}

@endsection