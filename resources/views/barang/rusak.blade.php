@extends('layout')

@section('title', 'Daftar Barang')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2>Daftar Barang Rusak</h2>
        <a href="{{ route('barang.create') }}" class="btn btn-success">+ Tambah Barang</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama</th>
                <th>Stok Rusak</th>
                <th>Keterangan Rusak</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $index => $barang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->stok_rusak }}</td>
                    <td>
                        @forelse($barang->mutasiKondisis as $mutasi)
                            <div>• {{ $mutasi->keterangan }} = {{ $mutasi->jumlah }}</div>
                        @empty
                            <span class="text-muted">Tidak ada catatan</span>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus barang?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection