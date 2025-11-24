<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiKeluar;
use App\Models\DetailBarang;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TransaksiKeluarExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TransaksiKeluarController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'month');
        $label = '';
        $dates = [];

        if ($filter == 'week') {
            $start = $request->get('start') ? Carbon::parse($request->get('start')) : now()->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $label = 'Minggu ' . $start->format('d M') . ' - ' . $end->format('d M Y');
            $dates = CarbonPeriod::create($start, $end)->toArray();
        } elseif ($filter == 'month') {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);
            $start = Carbon::create($year, $month, 1);
            $end = $start->copy()->endOfMonth();
            $label = $start->translatedFormat('F Y');
            $dates = CarbonPeriod::create($start, $end)->toArray();
        } elseif ($filter == 'year') {
            $year = $request->get('year', now()->year);
            $label = 'Tahun ' . $year;
            $start = Carbon::create($year, 1, 1);
            $end = Carbon::create($year, 12, 31);
            $dates = CarbonPeriod::create($start, $end)->toArray();
        }

        $dateStrings = array_map(fn($d) => $d->toDateString(), $dates);

        $transaksiKeluars = TransaksiKeluar::with('details.barang')
            ->when(!empty($dateStrings), function ($query) use ($dateStrings) {
                $query->whereDate('created_at', '>=', reset($dateStrings))
                    ->whereDate('created_at', '<=', end($dateStrings));
            })
            ->latest()
            ->get();

        $jumlahbarang = DetailBarang::with('barang')
            ->select(
                'barang_id',
                DB::raw('SUM(jumlah) as jumlah'),
                DB::raw('SUM(harga_jual * jumlah) as pendapatan')
            )
            ->whereNull('transaksi_masuk_id')
            ->when(!empty($dateStrings), function ($query) use ($dateStrings) {
                $query->whereHas('transaksiKeluar', function ($q) use ($dateStrings) {
                    $q->whereDate('created_at', '>=', reset($dateStrings))
                        ->whereDate('created_at', '<=', end($dateStrings));
                });
            })
            ->groupBy('barang_id')
            ->orderBy('jumlah', 'desc')
            ->get();

        return view('transaksi_keluar.index', compact('transaksiKeluars', 'jumlahbarang', 'filter', 'label'));
    }

    public function create()
    {
        $barangs = Barang::all();
        $customers = Customer::all();
        $kodeOtomatis = 'TRX-OUT-' . now()->format('YmdHis');
        return view('transaksi_keluar.create', compact('barangs', 'customers', 'kodeOtomatis'));
    }

    public function show($id)
    {
        $transaksi = TransaksiKeluar::with('details.barang')->findOrFail($id);
        return view('transaksi_keluar.print', compact('transaksi')); // Arahkan ke print view
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_transaksi' => 'required|unique:transaksi_keluars,kode_transaksi',
            'customer_id' => 'required',
            'keterangan_keluar' => 'nullable',
            'created_at' => 'required|date',
            'barang_id.*' => 'required|exists:barangs,id',
            'jumlah.*' => 'required|integer|min:1',
            'harga_jual.*' => 'required|numeric',
        ]);

        $customer = Customer::find($request->customer_id);
        $namaCustomer = $customer ? $customer->nama_customer : 'Umum';

        $transaksi = TransaksiKeluar::create([
            'kode_transaksi' => $request->kode_transaksi,
            'customer' => $namaCustomer,
            'keterangan_keluar' => $request->keterangan_keluar,
            'qty' => array_sum($request->jumlah),
            'created_at' => $request->created_at,
        ]);

        foreach ($request->barang_id as $index => $barang_id) {
            $jumlah = $request->jumlah[$index];
            $hargaJual = $request->harga_jual[$index];

            DetailBarang::create([
                'transaksi_keluar_id' => $transaksi->id,
                'barang_id' => $barang_id,
                'status' => 'terjual',
                'jumlah' => $jumlah,
                'harga_jual' => $hargaJual,
            ]);

            $barang = Barang::find($barang_id);
            $barang->stok_baik -= $jumlah;
            if ($barang->stok_baik < 0)
                $barang->stok_baik = 0;
            $barang->save();
        }

        return redirect()->route('transaksi-keluar.index')->with('success', 'Transaksi keluar berhasil dicatat');
    }

    public function print($id)
    {
        $transaksi = TransaksiKeluar::with(['details.barang'])->findOrFail($id);
        return view('transaksi_keluar.print', compact('transaksi'));
    }

    public function export(Request $request)
    {
        $filter = $request->filter ?? 'month';
        $query = TransaksiKeluar::with('details.barang');

        if ($filter === 'week') {
            $start = Carbon::parse($request->start ?? now()->startOfWeek());
            $end = $start->copy()->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($filter === 'month') {
            $month = $request->month ?? now()->month;
            $year = $request->year ?? now()->year;
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($filter === 'year') {
            $year = $request->year ?? now()->year;
            $query->whereYear('created_at', $year);
        }

        $data = $query->get();
        $fileName = 'transaksi_keluar_' . $filter . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new TransaksiKeluarExport($data), $fileName);
    }
}
