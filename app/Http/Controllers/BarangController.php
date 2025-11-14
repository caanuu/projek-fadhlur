<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar barang
     */
    public function index()
    {
        $barangs = Barang::with('mutasiKondisis')->get();
        return view('barang.index', compact('barangs'));
    }

    public function list()
    {
        $barangs = Barang::with('mutasiKondisis')->get();
        return view('barang.list', compact('barangs'));
    }

    /**
     * Menampilkan form tambah barang
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Simpan barang baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'jenis_barang' => 'required',
        ]);

        Barang::create([
            'kode_barang'   => $request->kode_barang,
            'nama_barang'   => $request->nama_barang,
            'jenis_barang'  => $request->jenis_barang,
            'keterangan'    => $request->keterangan,
            'harga_default' => null, // awalnya null
            'stok_baik'     => 0,
            'stok_rusak'    => 0,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    /**
     * Menampilkan detail barang
     */
    public function show(Barang $barang)
    {
        return view('barang.show', compact('barang'));
    }

    /**
     * Menampilkan form edit barang
     */
    public function edit(Barang $barang)
    {
        $mutasiKondisis = $barang->mutasiKondisis()->latest()->get();
        return view('barang.edit', compact('barang', 'mutasiKondisis'));
    }

    /**
     * Update barang
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required',
            'jenis_barang' => 'required',
            'mutasi_jumlah' => 'nullable|integer|min:1',
            'from_status' => 'nullable|in:baik,rusak',
            'to_status' => 'nullable|in:baik,rusak',
            'mutasi_keterangan' => 'nullable|string',
        ]);

        // Update master barang
        $barang->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'keterangan' => $request->keterangan,
        ]);

        // Jika ada mutasi kondisi
        if($request->mutasi_jumlah && $request->from_status && $request->to_status){
            $mutasi = $barang->mutasiKondisis()->create([
                'tanggal' => now(),
                'jumlah' => $request->mutasi_jumlah,
                'from_status' => $request->from_status,
                'to_status' => $request->to_status,
                'keterangan' => $request->mutasi_keterangan,
            ]);

            // Update stok master barang
            if($request->from_status == 'baik' && $request->to_status == 'rusak'){
                $barang->stok_baik -= $request->mutasi_jumlah;
                $barang->stok_rusak += $request->mutasi_jumlah;
            } elseif($request->from_status == 'rusak' && $request->to_status == 'baik'){
                $barang->stok_rusak -= $request->mutasi_jumlah;
                $barang->stok_baik += $request->mutasi_jumlah;
            }
            $barang->save();
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }


    /**
     * Hapus barang
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function rusak()
    {
        $barangs = Barang::with('mutasiKondisis')->get();
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
}
