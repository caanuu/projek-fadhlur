<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKeluar;
use App\Models\DetailBarang;
use App\Models\Barang;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'month'); // default: bulan
        $label = '';
        $dates = [];

        // === FILTER HARIAN ===
        if ($filter == 'day') {
            $start = $request->get('start') ? Carbon::parse($request->get('start')) : now()->startOfDay();
            $end   = $start->copy()->endOfDay();
            $label = 'Tanggal ' . $start->translatedFormat('d F Y');

            $dates = CarbonPeriod::create($start, $end)->toArray();
        }

        // === FILTER MINGGUAN ===
        elseif ($filter == 'week') {
            $start = $request->get('start') ? Carbon::parse($request->get('start')) : now()->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $label = 'Minggu ' . $start->format('d M') . ' - ' . $end->format('d M Y');

            $dates = CarbonPeriod::create($start, $end)->toArray();
        }

        // === FILTER BULANAN ===
        elseif ($filter == 'month') {
            $month = $request->get('month', now()->month);
            $year  = $request->get('year', now()->year);

            $start = Carbon::create($year, $month, 1);
            $end   = $start->copy()->endOfMonth();
            $label = $start->translatedFormat('F Y');

            $dates = CarbonPeriod::create($start, $end)->toArray();
        }

        // === FILTER TAHUNAN ===
        elseif ($filter == 'year') {
            $year = $request->get('year', now()->year);
            $label = 'Tahun ' . $year;

            $start = Carbon::create($year, 1, 1);
            $end   = Carbon::create($year, 12, 31);
            $dates = CarbonPeriod::create($start, $end)->toArray();
        }

        // Konversi ke string
        $dateStrings = array_map(fn($d) => $d->toDateString(), $dates);

        // === DATA PENJUALAN ===
        $salesData = DetailBarang::select(
                DB::raw('DATE(transaksi_keluars.created_at) as tanggal'),
                DB::raw('SUM(detail_barangs.jumlah * detail_barangs.harga_jual) as total_penjualan')
            )
            ->join('transaksi_keluars', 'detail_barangs.transaksi_keluar_id', '=', 'transaksi_keluars.id')
            ->whereBetween('transaksi_keluars.created_at', [$start, $end])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->pluck('total_penjualan', 'tanggal')
            ->toArray();

        // === BARANG TERLARIS ===
        $barangTerlaris = DetailBarang::with('barang')
            ->select('barang_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereNull('transaksi_masuk_id')
            ->when(!empty($dateStrings), function ($query) use ($dateStrings) {
                $query->whereHas('transaksiKeluar', function ($q) use ($dateStrings) {
                    $q->whereDate('created_at', '>=', reset($dateStrings))
                    ->whereDate('created_at', '<=', end($dateStrings));
                });
            })
            ->groupBy('barang_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        // === STOK MENIPIS ===
        $stokMenipis = Barang::where('stok_baik', '<', 50)
            ->orderBy('stok_baik', 'asc')
            ->take(5)
            ->get();

        $totalstok = Barang::orderBy('stok_baik', 'desc')->get();
        $rusak = Barang::orderBy('stok_rusak', 'desc')->get();

    // TOTAL PENJUALAN (Rp)
    $totalPenjualan = DetailBarang::whereNotNull('transaksi_keluar_id')
        ->when($start && $end, function ($q) use ($start, $end) {
            $q->whereHas('transaksiKeluar', function ($q2) use ($start, $end) {
                // gunakan kolom tanggal yang benar di transaksi_keluars
                $q2->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
            });
        })
        ->sum(DB::raw('jumlah * harga_jual'));

    // TOTAL UNIT TERJUAL
    $totalBarangTerjual = DetailBarang::whereNotNull('transaksi_keluar_id')
        ->when($start && $end, function ($q) use ($start, $end) {
            $q->whereHas('transaksiKeluar', function ($q2) use ($start, $end) {
                $q2->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
            });
        })
        ->sum('jumlah');

    // TOTAL TRANSAKSI KELUAR (count)
    $totalTransaksi = TransaksiKeluar::when($start && $end, function ($q) use ($start, $end) {
            $q->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);
        })
        ->count();

        return view('dashboard', compact(
            'filter', 'label', 'dateStrings', 'salesData',
            'barangTerlaris', 'stokMenipis', 'rusak',
            'totalPenjualan', 'totalBarangTerjual', 'totalTransaksi', 'totalstok'
        ));
    }
}
