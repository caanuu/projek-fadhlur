@extends('layout')

@section('title', 'Edit Barang')

@section('content')
    <h2 class="mb-4">Edit Barang</h2>
    <form action="{{ route('barang.update', $barang->id) }}" method="POST" class="card p-4 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" value="{{ $barang->kode_barang }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="{{ $barang->nama_barang }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jenis Barang</label>
            <input type="text" name="jenis_barang" class="form-control" value="{{ $barang->jenis_barang }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $barang->keterangan }}</textarea>
        </div>

        <p>Stok Saat Ini: Baik: {{ $barang->stok_baik }}, Rusak: {{ $barang->stok_rusak }}</p>

        <hr style="height:5px;border-width:0;color:black;background-color:black">

        <h5 class="mt-4">Mutasi Kondisi Barang</h5>
        <div class="mb-3">
            <label class="form-label">Jumlah Barang</label>
            <input type="number" name="mutasi_jumlah" class="form-control" min="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Dari Status</label>
            <select name="from_status" class="form-select">
                <option value="">-- Pilih Status Awal --</option>
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Ke Status</label>
            <select name="to_status" class="form-select">
                <option value="">-- Pilih Status Tujuan --</option>
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Keterangan Mutasi</label>
            <textarea name="mutasi_keterangan" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>

    @if($mutasiKondisis->count() > 0)
        <h4>Riwayat Mutasi Kondisi</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-secondary">
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Dari Status</th>
                    <th>Ke Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasiKondisis as $mutasi)
                    <tr>
                        <td>{{ $mutasi->tanggal }}</td>
                        <td>{{ $mutasi->jumlah }}</td>
                        <td>{{ $mutasi->from_status }}</td>
                        <td>{{ $mutasi->to_status }}</td>
                        <td>{{ $mutasi->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection