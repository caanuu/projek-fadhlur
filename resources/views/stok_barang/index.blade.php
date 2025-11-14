@extends('layout')

@section('title', 'Stok Barang')

@section('content')
    <h1 class="mb-3">Stok Barang</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Kondisi</th>
                <th>Qty</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokBarangs as $stok)
                <tr>
                    <td>{{ $stok->barang->nama_barang }}</td>
                    <td>{{ $stok->barang->jenis_barang }}</td>
                    <td>{{ ucfirst($stok->kondisi) }}</td>
                    <td>{{ $stok->qty }}</td>
                    <td>{{ $stok->keterangan }}</td>
                    <td>
                        <a href="{{ route('stok-barang.edit', $stok->id) }}" class="btn btn-sm btn-warning">Update Kondisi</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection