@extends('layout')

@section('title', 'Update Kondisi Barang')

@section('content')
    <h1 class="mb-3">Update Kondisi Barang</h1>

    <form action="{{ route('stok-barang.update', $stokBarang->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" class="form-control" value="{{ $stokBarang->barang->nama_barang }}" disabled>
        </div>

        <div class="mb-3">
            <label>Kondisi Saat Ini</label>
            <input type="text" class="form-control" value="{{ ucfirst($stokBarang->kondisi) }}" disabled>
        </div>

        <div class="mb-3">
            <label>Qty Pindah</label>
            <input type="number" name="qty_pindah" class="form-control" max="{{ $stokBarang->qty }}" required>
        </div>

        <div class="mb-3">
            <label>Kondisi Baru</label>
            <select name="kondisi_baru" class="form-control" required>
                <option value="">-- Pilih Kondisi Baru --</option>
                @if($stokBarang->kondisi == 'baik')
                    <option value="rusak">Rusak</option>
                @else
                    <option value="baik">Baik</option>
                @endif
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
@endsection