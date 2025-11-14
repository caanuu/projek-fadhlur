@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">📊 Dashboard</h3>
            <form method="GET" class="d-flex gap-2">
                <select name="filter" class="form-select w-auto shadow-sm">
                    <option value="day" {{ request('filter') == 'day' ? 'selected' : '' }}>Harian</option>
                    <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>Mingguan</option>
                    <option value="month" {{ request('filter', 'month') == 'month' ? 'selected' : '' }}>Bulanan</option>
                    <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>Tahunan</option>
                </select>
                <button class="btn btn-primary shadow-sm px-4">Terapkan</button>
            </form>
        </div>

        <div class="alert alert-info shadow-sm mb-4">
            <strong>Periode:</strong> {{ $label }}
        </div>

        {{-- Statistik Ringkas --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center py-4"
                    style="background: linear-gradient(135deg, #007bff, #00c6ff);">
                    <h6 class="fw-bold text-uppercase mb-1">Total Penjualan</h6>
                    <h3 class="fw-bolder mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                    <small>Dalam periode ini</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center py-4"
                    style="background: linear-gradient(135deg, #28a745, #6ddf5b);">
                    <h6 class="fw-bold text-uppercase mb-1">Barang Terjual</h6>
                    <h3 class="fw-bolder mb-0">{{ $totalBarangTerjual }}</h3>
                    <small>Total unit keluar</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center py-4"
                    style="background: linear-gradient(135deg, #ffc107, #ffb347);">
                    <h6 class="fw-bold text-uppercase mb-1">Jumlah Transaksi</h6>
                    <h3 class="fw-bolder mb-0">{{ $totalTransaksi }}</h3>
                    <small>Transaksi tercatat</small>
                </div>
            </div>
        </div>

        {{-- Grafik Penjualan --}}
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-3">📈 Grafik Penjualan</h5>
            <canvas id="salesChart" height="250"></canvas>
        </div>

        {{-- Barang Terlaris & Stok Menipis --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <h5 class="fw-bold mb-3">🔥 Barang Terlaris</h5>
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangTerlaris as $item)
                                <tr>
                                    <td>{{ $item->barang->nama_barang }}</td>
                                    <td class="text-center fw-bold text-success">{{ $item->total_terjual }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <h5 class="fw-bold mb-3">⚠️ Stok Menipis (Dibawah 50)</h5>
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Stok Baik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stokMenipis as $b)
                                <tr>
                                    <td>{{ $b->nama_barang }}</td>
                                    <td class="text-center fw-bold text-danger">{{ $b->stok_baik }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Semua stok aman</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Total Stok & Stok Rusak --}}
        <div class="row g-3 mb-5">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <h5 class="fw-bold mb-3">📦 Total Stok Barang</h5>
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Jumlah Baik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($totalstok as $item)
                                <tr>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td class="text-center">{{ $item->stok_baik }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3">
                    <h5 class="fw-bold mb-3">❌ Stok Rusak</h5>
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-center">Stok Rusak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rusak as $b)
                                <tr>
                                    <td>{{ $b->nama_barang }}</td>
                                    <td class="text-center text-danger fw-bold">{{ $b->stok_rusak }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($dateStrings),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json(array_values($salesData)),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(54,162,235,1)',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endsection