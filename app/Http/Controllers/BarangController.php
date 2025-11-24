<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Exports\BarangExport; // Load Export Class
use Maatwebsite\Excel\Facades\Excel; // Load Facade Excel

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        return view('barang.index', compact('barangs'));
    }

    public function list()
    {
        $barangs = Barang::all();
        return view('barang.list', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'jenis_barang' => 'required',
        ]);

        Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'keterangan' => $request->keterangan,
            'harga_default' => null,
            'stok_baik' => 0,
            'stok_rusak' => 0,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        $mutasiKondisis = $barang->mutasiKondisis()->latest()->get();
        return view('barang.edit', compact('barang', 'mutasiKondisis'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required',
            'jenis_barang' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $barang->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->forceDelete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus permanen');
    }

    public function rusak()
    {
        $barangs = Barang::where('stok_rusak', '>', 0)->get();
        return view('barang.rusak', compact('barangs'));
    }

    public function getStok($id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json(['stok_baik' => 0]);
        }
        return response()->json([
            'stok_baik' => $barang->stok_baik,
            'stok_rusak' => $barang->stok_rusak ?? 0,
        ]);
    }

    // --- FUNGSI BARU UNTUK EXPORT EXCEL ---
    public function export()
    {
        return Excel::download(new BarangExport, 'daftar_barang_' . date('Y-m-d_H-i') . '.xlsx');
    }
}
