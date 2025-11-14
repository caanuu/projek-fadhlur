@extends('layout')

@section('title', 'Transaksi Keluar')

@section('content')
    <h2 class="text-lg font-bold mb-4">Input Transaksi Keluar</h2>

    <form action="{{ route('transaksi-keluar.store') }}" method="POST" class="bg-white p-4 rounded shadow">
        @csrf
        <div class="mb-2">
            <label>Nomor Transaksi</label>
            <input type="text" name="kode_transaksi" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label>Customer</label>
            <input type="text" name="customer" class="border p-2 w-full" required>
        </div>
        <div class="mb-2">
            <label>Barang</label>
            <select name="barang_id" class="border p-2 w-full" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->kode_barang }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-2">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="border p-2 w-full" min="1" required>
        </div>
        <div class="mb-2">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" class="border p-2 w-full" min="0" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Transaksi</button>
    </form>
@endsection