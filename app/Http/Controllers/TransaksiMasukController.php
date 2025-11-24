<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\TransaksiMasuk;
use App\Models\DetailBarang;
use App\Models\Supplier; // Tambahkan Model Supplier
use App\Models\User;     // Tambahkan Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TransaksiMasukExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TransaksiMasukController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'month');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
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

        $transaksiMasuks = TransaksiMasuk::with('details.barang')
            ->when(!empty($dateStrings), function ($query) use ($dateStrings) {
                $query->whereDate('created_at', '>=', reset($dateStrings))
                    ->whereDate('created_at', '<=', end($dateStrings));
            })
            ->orderBy($sortBy, $sortOrder)
            ->get();

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
            'transaksiMasuks',
            'jumlahbarang',
            'filter',
            'label',
            'sortBy',
            'sortOrder'
        ));
    }

    public function create()
    {
        $barangs = Barang::all();
        $suppliers = Supplier::all(); // Ambil data supplier untuk dropdown
        $users = User::all();         // Ambil data user untuk pegawai dropdown

        // Auto Generate Kode Transaksi: TRX-IN-YYYYMMDDHIS
        $kodeOtomatis = 'TRX-IN-' . now()->format('YmdHis');

        return view('transaksi_masuk.create', compact('barangs', 'suppliers', 'users', 'kodeOtomatis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_transaksi' => 'required|unique:transaksi_masuks,kode_transaksi',
            'supplier_id' => 'required', // Kita validasi ID supplier
            'pegawai_penerima' => 'required',
            'keterangan_masuk' => 'nullable',
            'created_at' => 'required|date', // Validasi Tanggal Manual
            'barang_id.*' => 'required|exists:barangs,id',
            'jumlah.*' => 'required|integer|min:1',
            'harga_beli.*' => 'required|numeric',
        ]);

        // Ambil Nama Supplier berdasarkan ID yang dipilih
        $supplier = Supplier::find($request->supplier_id);
        $namaSupplier = $supplier ? $supplier->nama_supplier : 'Umum';

        $totalQty = array_sum($request->jumlah);

        $transaksi = TransaksiMasuk::create([
            'kode_transaksi' => $request->kode_transaksi,
            'supplier' => $namaSupplier, // Simpan Nama, bukan ID (sesuai struktur tabel lama)
            'pegawai_penerima' => $request->pegawai_penerima,
            'keterangan_masuk' => $request->keterangan_masuk,
            'qty' => $totalQty,
            'created_at' => $request->created_at, // Simpan Tanggal Manual
        ]);

        foreach ($request->barang_id as $index => $barang_id) {
            $jumlah = $request->jumlah[$index];
            $hargaBeli = $request->harga_beli[$index];

            DetailBarang::create([
                'transaksi_masuk_id' => $transaksi->id,
                'barang_id' => $barang_id,
                'status' => 'baik',
                'jumlah' => $jumlah,
                'harga_beli' => $hargaBeli,
            ]);

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

        $fileName = 'transaksi_masuk_' . $filter . '_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new TransaksiMasukExport($data), $fileName);
    }
}
