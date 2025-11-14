@extends('layout')

@section('title', 'Tambah Transaksi Masuk')

@section('content')
    <h2 class="mb-4">Tambah Transaksi Masuk</h2>
    <form action="{{ route('transaksi-masuk.store') }}" method="POST" class="card p-4">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode Transaksi</label>
            <input type="text" name="kode_transaksi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <input type="text" name="supplier" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pegawai Penerima</label>
            <input type="text" name="pegawai_penerima" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Keterangan Transaksi</label>
            <input type="text" name="keterangan_masuk" class="form-control" required>
        </div>

        <hr class="my-3">
        <h5>Detail Barang</h5>

        <div id="detail-barang-wrapper">
            <div class="row mb-3 detail-barang-item">
                <div class="col-md-4">
                    <label class="form-label">Barang</label>
                    <select name="barang_id[]" class="form-select" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $barang)
                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->jenis_barang }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="jumlah[]" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Harga Beli</label>
                    <input type="number" name="harga_beli[]" class="form-control" min="0" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-detail">Hapus</button>
                </div>
            </div>
        </div>

        <button type="button" id="btn-add-detail" class="btn btn-secondary mb-3">+ Tambah Barang</button>

        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('detail-barang-wrapper');
            const btnAdd = document.getElementById('btn-add-detail');

            btnAdd.addEventListener('click', function () {
                const newItem = wrapper.querySelector('.detail-barang-item').cloneNode(true);
                newItem.querySelectorAll('input').forEach(input => input.value = '');
                newItem.querySelector('select').selectedIndex = 0;
                wrapper.appendChild(newItem);
            });

            wrapper.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-remove-detail')) {
                    const items = wrapper.querySelectorAll('.detail-barang-item');
                    if (items.length > 1) {
                        e.target.closest('.detail-barang-item').remove();
                    }
                }
            });
        });
    </script>
@endsection