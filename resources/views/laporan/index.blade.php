@extends('layout')

@section('title', 'Laporan')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">📊 Laporan Transaksi ({{ $label }})</h3>

        {{-- 🔽 Filter --}}
        <form method="GET" class="d-flex gap-2 align-items-center mb-4">
            <select name="filter" class="form-select" style="width:auto;" onchange="this.form.submit()">
                <option value="week" {{ $filter == 'week' ? 'selected' : '' }}>Mingguan</option>
                <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Bulanan</option>
                <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Tahunan</option>
            </select>

            @if($filter == 'month')
                <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
            @elseif($filter == 'year')
                <input type="number" name="year" class="form-control" min="2000" max="2100"
                    value="{{ request('year', now()->year) }}">
            @endif

            <button class="btn btn-outline-primary" type="submit">Tampilkan</button>
        </form>

        {{-- Navigasi Periode --}}
        @php
            use Carbon\Carbon;

            if ($filter == 'week') {
                $currentStart = request('start')
                    ? Carbon::parse(request('start'))->startOfWeek()
                    : Carbon::now()->startOfWeek();

                $prevStart = $currentStart->copy()->subWeek()->format('Y-m-d');
                $nextStart = $currentStart->copy()->addWeek()->format('Y-m-d');
                $todayStart = Carbon::now()->startOfWeek();
                $disableNext = $currentStart->greaterThanOrEqualTo($todayStart);

                $labelPeriode = $currentStart->format('d M') . ' - ' . $currentStart->copy()->endOfWeek()->format('d M Y');

            } elseif ($filter == 'month') {
                $currentMonth = request('month')
                    ? Carbon::parse(request('month') . '-01')
                    : Carbon::now()->startOfMonth();

                $prevMonth = $currentMonth->copy()->subMonth();
                $nextMonth = $currentMonth->copy()->addMonth();
                $disableNext = $currentMonth->isSameMonth(Carbon::now());
                $labelPeriode = $currentMonth->translatedFormat('F Y');

            } elseif ($filter == 'year') {
                $currentYear = request('year')
                    ? Carbon::createFromDate(request('year'), 1, 1)
                    : Carbon::now()->startOfYear();

                $prevYear = $currentYear->copy()->subYear();
                $nextYear = $currentYear->copy()->addYear();
                $disableNext = $currentYear->year >= Carbon::now()->year;
                $labelPeriode = 'Tahun ' . $currentYear->year;
            }
        @endphp

        <div class="d-flex align-items-center gap-2 mb-3">
            {{-- Tombol Sebelumnya --}}
            <form method="GET" class="d-inline">
                <input type="hidden" name="filter" value="{{ $filter }}">

                @if($filter == 'week')
                    <input type="hidden" name="start" value="{{ $prevStart }}">
                @elseif($filter == 'month')
                    <input type="hidden" name="month" value="{{ $prevMonth->format('Y-m') }}">
                @elseif($filter == 'year')
                    <input type="hidden" name="year" value="{{ $prevYear->year }}">
                @endif

                <button class="btn btn-outline-secondary btn-sm" type="submit" title="Sebelumnya">
                    &larr;
                </button>
            </form>

            {{-- Label Periode --}}
            <span class="fw-semibold">
                {{ $labelPeriode }}
            </span>

            {{-- Tombol Berikutnya --}}
            <form method="GET" class="d-inline">
                <input type="hidden" name="filter" value="{{ $filter }}">

                @if($filter == 'week')
                    <input type="hidden" name="start" value="{{ $nextStart }}">
                @elseif($filter == 'month')
                    <input type="hidden" name="month" value="{{ $nextMonth->format('Y-m') }}">
                @elseif($filter == 'year')
                    <input type="hidden" name="year" value="{{ $nextYear->year }}">
                @endif

                <button class="btn btn-outline-secondary btn-sm" type="submit" title="Berikutnya" {{ $disableNext ? 'disabled' : '' }}>
                    &rarr;
                </button>
            </form>
        </div>



        {{-- 💵 CASH FLOW --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <h6 class="text-white mb-1">Pemasukan (Penjualan)</h6>
                        <h4 class="text-white">Rp {{ number_format($cashIn, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <h6 class="text-white mb-1">Pengeluaran (Pembelian)</h6>
                        <h4 class="text-white">Rp {{ number_format($cashOut, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info">
                    <div class="card-body">
                        <h6 class=" mb-1">Saldo / Cash Flow Bersih</h6>
                        <h4 class="">Rp {{ number_format($cashFlow, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📦 Transaksi Masuk --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <div class="">Transaksi Masuk</div>
                <div><a href="{{ route('transaksi-masuk.index') }}" class="text-white">Lihat Detail -->></a></div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Total Pembelian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiMasuk as $tm)
                                            <tr>
                                                <td>{{ $tm->kode_transaksi }}</td>
                                                <td>{{ $tm->created_at->format('d M Y') }}</td>
                                                <td>{{ $tm->supplier }}</td>
                                                <td>{{ $tm->qty }}</td>
                                                <td>
                                                    Rp
                                                    {{number_format(
                                $tm->details->sum(function ($detail) {
                                    return $detail->harga_beli * $detail->jumlah; }),
                                0,
                                ',',
                                '.'
                            )
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }}
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 📤 Transaksi Keluar --}}
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between">
                <div>Transaksi Keluar</div>
                <div><a href="{{ route('transaksi-keluar.index') }}" class="text-white">Lihat Detail -->></a></div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Qty</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiKeluar as $tk)
                                            <tr>
                                                <td>{{ $tk->kode_transaksi }}</td>
                                                <td>{{ $tk->created_at->format('d M Y') }}</td>
                                                <td>{{ $tk->customer }}</td>
                                                <td>{{ $tk->qty }}</td>
                                                <td>
                                                    Rp
                                                    {{number_format(
                                $tk->details->sum(function ($detail) {
                                    return $detail->harga_jual * $detail->jumlah; }),
                                0,
                                ',',
                                '.'
                            )
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }}
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection