<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\MutasiKondisi;
use Illuminate\Http\Request;

class MutasiKondisiController extends Controller
{
    public function create()
    {
        $barangs = Barang::all();
        return view('mutasi_kondisi.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'   => 'required|exists:barangs,id',
            'jumlah'      => 'required|integer|min:1',
            'from_status' => 'required|in:baik,rusak',
            'to_status'   => 'required|in:baik,rusak',
            'tanggal'     => 'required|date',
            'keterangan'  => 'nullable|string',
        ]);

        $mutasi = MutasiKondisi::create([
            'barang_id'   => $request->barang_id,
            'tanggal'     => $request->tanggal,
            'jumlah'      => $request->jumlah,
            'from_status' => $request->from_status,
            'to_status'   => $request->to_status,
            'keterangan'  => $request->keterangan,
        ]);

        // Update stok master barang
        $barang = Barang::find($request->barang_id);
        if($request->from_status == 'baik' && $request->to_status == 'rusak') {
            $barang->stok_baik -= $request->jumlah;
            $barang->stok_rusak += $request->jumlah;
        } elseif($request->from_status == 'rusak' && $request->to_status == 'baik') {
            $barang->stok_rusak -= $request->jumlah;
            $barang->stok_baik += $request->jumlah;
        }
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Mutasi kondisi barang berhasil dicatat');
    }
}
