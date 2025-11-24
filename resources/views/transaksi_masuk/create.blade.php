@extends('layout')

@section('title', 'Tambah Transaksi Masuk')

@section('content')
    <h2 class="mb-4">Tambah Transaksi Masuk</h2>
    <form action="{{ route('transaksi-masuk.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kode Transaksi (Otomatis)</label>
                {{-- Readonly agar user tidak perlu ketik manual --}}
                <input type="text" name="kode_transaksi" class="form-control bg-light" value="{{ $kodeOtomatis }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                {{-- Input Tanggal Manual dengan datetime-local --}}
                <input type="datetime-local" name="created_at" class="form-control"
                    value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Supplier</label>
                {{-- Dropdown Supplier --}}
                <select name="supplier_id" class="form-select" required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Pegawai Penerima</label>
                {{-- Dropdown Pegawai (User) --}}
                <select name="pegawai_penerima" class="form-select" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->name }}" {{ Auth::user()->name == $user->name ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Keterangan Transaksi</label>
            <textarea name="keterangan_masuk" class="form-control" rows="2"></textarea>
        </div>

        <hr class="my-3">
        <h5>Detail Barang</h5>

        <div id="detail-barang-wrapper">
            <div class="row mb-3 align-items-end detail-barang-item border-bottom pb-3">
                <div class="col-md-4">
                    <label class="form-label">Barang</label>
                    <select name="barang_id[]" class="form-select barang-select" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }} ({{ $barang->jenis_barang }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah</label>
                    <small class="text-muted d-block stok-info"></small>
                    <input type="number" name="jumlah[]" class="form-control" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga Beli (Satuan)</label>
                    <input type="number" name="harga_beli[]" class="form-control" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-remove-detail w-100"><i class="bi bi-trash"></i>
                        Hapus</button>
                </div>
            </div>
        </div>

        <button type="button" id="btn-add-detail" class="btn btn-secondary mb-3"><i class="bi bi-plus-circle"></i> Tambah
            Barang</button>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">Simpan Transaksi</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('detail-barang-wrapper');
            const btnAdd = document.getElementById('btn-add-detail');
            const template = wrapper.querySelector('.detail-barang-item').cloneNode(true);

            function fetchStok(selectElement) {
                const barangId = selectElement.value;
                const parent = selectElement.closest('.detail-barang-item');
                const stokInfo = parent.querySelector('.stok-info');
                if (!barangId) {
                    stokInfo.textContent = '';
                    return;
                }
                fetch(`/get-stok-barang/${barangId}`)
                    .then(response => response.json())
                    .then(data => {
                        stokInfo.textContent = `Stok: ${data.stok_baik} (Baik)`;
                    })
                    .catch(() => {
                        stokInfo.textContent = 'Gagal ambil stok';
                    });
            }

            btnAdd.addEventListener('click', function() {
                const newItem = template.cloneNode(true);
                newItem.querySelectorAll('input').forEach(input => input.value = '');
                newItem.querySelector('.stok-info').textContent = '';
                wrapper.appendChild(newItem);
            });

            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-detail')) {
                    if (wrapper.querySelectorAll('.detail-barang-item').length > 1) {
                        e.target.closest('.detail-barang-item').remove();
                    }
                }
            });

            wrapper.addEventListener('change', function(e) {
                if (e.target.classList.contains('barang-select')) {
                    fetchStok(e.target);
                }
            });
        });
    </script>
@endsection
