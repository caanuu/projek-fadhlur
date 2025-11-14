@extends('layout')

@section('title', 'Transaksi Keluar')

@section('content')
    <h2 class="text-lg font-bold mb-4">Input Transaksi Keluar</h2>

    <form action="{{ route('transaksi-keluar.store') }}" method="POST" class="bg-white p-4 rounded shadow">
        @csrf

        <div class="mb-3">
            <label class="form-label">Kode Transaksi</label>
            <input type="text" name="kode_transaksi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Customer</label>
            <input type="text" name="customer" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Keterangan Transaksi</label>
            <input type="text" name="keterangan_keluar" class="form-control">
        </div>

        <hr class="my-3">
        <h5>Detail Barang</h5>

        <div id="detail-barang-wrapper">
            <div class="row mb-3 detail-barang-item">
                <div class="row  align-items-end barang-row">
                    <div class="col-md-4">
                        <label class="form-label">Barang</label>
                        <select name="barang_id[]" class="form-select barang-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}">
                                    {{ $barang->nama_barang }} ({{ $barang->jenis_barang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Jumlah</label>
                        <small class="text-muted stok-info"></small>
                        <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="harga_jual[]" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-remove-detail">Hapus</button>
                    </div>
                </div>

            </div>
        </div>

        <button type="button" id="btn-add-detail" class="btn btn-secondary">+ Tambah Barang</button>
        <button type="submit" class="btn btn-success text-white rounded float-end">Simpan Transaksi</button>
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

            document.querySelectorAll('.barang-select').forEach(select => {
                select.addEventListener('change', function () {
                    const barangId = this.value;
                    const parent = this.closest('.barang-row');
                    const stokInfo = parent.querySelector('.stok-info');
                    const jumlahInput = parent.querySelector('.jumlah-input');

                    if (!barangId) {
                        stokInfo.textContent = '';
                        jumlahInput.placeholder = '';
                        return;
                    }

                    fetch(`/get-stok-barang/${barangId}`)
                        .then(response => response.json())
                        .then(data => {
                            stokInfo.textContent = `Stok tersedia: ${data.stok_baik}`;
                            jumlahInput.placeholder = data.stok_baik;
                            jumlahInput.max = data.stok_baik;
                        })
                        .catch(() => {
                            stokInfo.textContent = 'Gagal mengambil stok.';
                        });
                });
            });
        });
    </script>
@endsection