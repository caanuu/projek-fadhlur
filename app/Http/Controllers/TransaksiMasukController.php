<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiMasuk;
use App\Models\DetailBarang;
use Illuminate\Http\Request;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Support\Facades\DB;
use App\Exports\TransaksiMasukExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TransaksiMasukController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'month'); // default: bulan
        $sortBy = $request->get('sort_by', 'created_at'); // default sort by tanggal
        $sortOrder = $request->get('sort_order', 'desc'); // default descending
        $label = '';
        $dates = [];

        // --- Filter Waktu (sama seperti punyamu) ---
        if ($filter == 'week') {
            $start = $request->get('start') ? Carbon::parse($request->get('start')) : now()->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $label = 'Minggu ' . $start->format('d M') . ' - ' . $end->format('d M Y');
            $dates = CarbonPeriod::create($start, $end)->toArray();
        } elseif ($filter == 'month') {
            $month = $request->get('month', now()->month);
            $year  = $request->get('year', now()->year);
            $start = Carbon::create($year, $month, 1);
            $end   = $start->copy()->endOfMonth();
            $label = $start->translatedFormat('F Y');
            $dates = CarbonPeriod::create($start, $end)->toArray();
        } elseif ($filter == 'year') {
            $year = $request->get('year', now()->year);
            $label = 'Tahun ' . $year;
            $start = Carbon::create($year, 1, 1);
            $end   = Carbon::create($year, 12, 31);
            $dates = CarbonPeriod::create($start, $end)->toArray();
        }

        $dateStrings = array_map(fn($d) => $d->toDateString(), $dates);

        // --- Query Transaksi Masuk ---
        $transaksiMasuks = TransaksiMasuk::with('details.barang')
            ->when(!empty($dateStrings), function ($query) use ($dateStrings) {
                $query->whereDate('created_at', '>=', reset($dateStrings))
                    ->whereDate('created_at', '<=', end($dateStrings));
            })
            ->orderBy($sortBy, $sortOrder)
            ->get();

        // --- Query Jumlah Barang Masuk ---
        $jumlahbarang = DetailBarang::with('barang')
            ->select('barang_id', DB::raw('SUM(jumlah) as jumlah'))
            ->whereNull('transaksi_keluar_id')
            ->when(!empty($dateStrings), function ($query) use ($dateStrings) {
                $query->whereHas('transaksiMasuk', function ($q) use ($dateStrings) {
                    $q->whereDate('created_at', '>=', reset($dateStrings))
                    ->whereDate('created_at', '<=', end($dateStrings));
                });
            })
            ->groupBy('barang_id')
            ->get();

        return view('transaksi_masuk.index', compact(
            'transaksiMasuks', 'jumlahbarang', 'filter', 'label', 'sortBy', 'sortOrder'
        ));
    }


    public function create()
    {
        $barangs = Barang::all();
        return view('transaksi_masuk.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_transaksi'     => 'required|unique:transaksi_masuks,kode_transaksi',
            'supplier'           => 'required',
            'pegawai_penerima'   => 'required',
            'keterangan_masuk'   => 'nullable',
            'barang_id.*'        => 'required|exists:barangs,id',
            'jumlah.*'           => 'required|integer|min:1',
            'harga_beli.*'       => 'required|numeric',
        ]);

        // Hitung total qty dari semua barang yang dikirim
        $totalQty = array_sum($request->jumlah);

        // Buat transaksi utama
        $transaksi = TransaksiMasuk::create([
            'kode_transaksi'   => $request->kode_transaksi,
            'supplier'         => $request->supplier,
            'pegawai_penerima' => $request->pegawai_penerima,
            'keterangan_masuk' => $request->keterangan_masuk,
            'qty'              => $totalQty, // simpan total semua jumlah barang
        ]);

        // Simpan detail setiap barang
        foreach ($request->barang_id as $index => $barang_id) {
            $jumlah     = $request->jumlah[$index];
            $hargaBeli  = $request->harga_beli[$index];

            // Simpan detail barang
            DetailBarang::create([
                'transaksi_masuk_id' => $transaksi->id,
                'barang_id'          => $barang_id,
                'status'             => 'baik',
                'jumlah'             => $jumlah,
                'harga_beli'         => $hargaBeli,
            ]);

            // Update stok dan harga default di master barang
            $barang = Barang::find($barang_id);
            $barang->stok_baik += $jumlah;
            $barang->harga_default = $hargaBeli;
            $barang->save();
        }

        return redirect()->route('transaksi-masuk.index')
                        ->with('success', 'Transaksi masuk berhasil dicatat');
    }

    public function export(Request $request)
    {
        $filter = $request->filter ?? 'month';
        $query = TransaksiMasuk::with('details.barang');

        // Filter waktu
        if ($filter === 'week') {
            $start = Carbon::parse($request->start ?? now()->startOfWeek());
            $end   = $start->copy()->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($filter === 'month') {
            $month = $request->month ?? now()->month;
            $year  = $request->year ?? now()->year;
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($filter === 'year') {
            $year = $request->year ?? now()->year;
            $query->whereYear('created_at', $year);
        }

        $data = $query->get();

        $fileName = 'transaksi_masuk_' . $filter . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new TransaksiMasukExport($data), $fileName);
    }



}
