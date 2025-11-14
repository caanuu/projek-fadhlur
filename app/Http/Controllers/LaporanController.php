<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiMasuk;
use App\Models\TransaksiKeluar;
use App\Models\DetailBarang;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'month');
        $label = '';
        $dates = [];

        // === Filter Waktu ===
        if ($filter == 'week') {
            $start = $request->get('start')
                ? Carbon::parse($request->get('start'))->startOfWeek()
                : now()->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $label = 'Minggu ' . $start->format('d M') . ' - ' . $end->format('d M Y');
        } elseif ($filter == 'month') {
            $monthInput = $request->get('month', now()->format('Y-m'));
            [$year, $month] = explode('-', $monthInput);
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $label = $start->translatedFormat('F Y');
        } elseif ($filter == 'year') {
            $year = $request->get('year', now()->year);
            $start = Carbon::create($year, 1, 1);
            $end = Carbon::create($year, 12, 31);
            $label = 'Tahun ' . $year;
        }

        // === Transaksi Masuk & Keluar ===
        $transaksiMasuk = TransaksiMasuk::whereBetween('created_at', [$start, $end])->get();
        $transaksiKeluar = TransaksiKeluar::whereBetween('created_at', [$start, $end])->get();

        // === CASH FLOW ===
        $cashIn = DetailBarang::whereNotNull('transaksi_keluar_id')
            ->whereHas('transaksiKeluar', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->sum(DB::raw('jumlah * harga_jual'));

        $cashOut = DetailBarang::whereNotNull('transaksi_masuk_id')
            ->whereHas('transaksiMasuk', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->sum(DB::raw('jumlah * harga_beli'));

        $cashFlow = $cashIn - $cashOut;

        return view('laporan.index', compact(
            'filter',
            'label',
            'start',
            'end',
            'transaksiMasuk',
            'transaksiKeluar',
            'cashIn',
            'cashOut',
            'cashFlow'
        ));
    }
}
