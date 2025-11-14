@extends('layout')

@section('title', 'Tambah Barang')

@section('content')
    <h2 class="mb-4">Tambah Barang Baru</h2>
    <form action="{{ route('barang.store') }}" method="POST" class="card p-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jenis Barang</label>
            <input type="text" name="jenis_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
@endsection