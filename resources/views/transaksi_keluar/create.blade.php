@extends('layout')

@section('title', 'Transaksi Keluar')

@section('content')
    <h2 class="text-lg font-bold mb-4">Input Transaksi Keluar</h2>

    <form action="{{ route('transaksi-keluar.store') }}" method="POST" class="bg-white p-4 rounded shadow">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kode Transaksi (Otomatis)</label>
                {{-- Readonly --}}
                <input type="text" name="kode_transaksi" class="form-control bg-light" value="{{ $kodeOtomatis }}" readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                {{-- Tanggal Manual --}}
                <input type="datetime-local" name="created_at" class="form-control"
                    value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Customer</label>
            {{-- Dropdown Customer --}}
            <select name="customer_id" class="form-select" required>
                <option value="">-- Pilih Customer --</option>
                @foreach ($customers as $cus)
                    <option value="{{ $cus->id }}">{{ $cus->nama_customer }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Keterangan Transaksi</label>
            <input type="text" name="keterangan_keluar" class="form-control">
        </div>

        <hr class="my-3">
        <h5>Detail Barang</h5>

        <div id="detail-barang-wrapper">
            <div class="detail-barang-item border-bottom pb-3 mb-3">
                <div class="row align-items-end barang-row">
                    <div class="col-md-4">
                        <label class="form-label">Barang</label>
                        <select name="barang_id[]" class="form-select barang-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id }}">
                                    {{ $barang->nama_barang }} ({{ $barang->jenis_barang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Jumlah</label>
                        <small class="text-muted d-block stok-info"></small>
                        <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="harga_jual[]" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-remove-detail w-100"><i class="bi bi-trash"></i>
                            Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="btn-add-detail" class="btn btn-secondary"><i class="bi bi-plus-circle"></i> Tambah
            Barang</button>
        <button type="submit" class="btn btn-success text-white rounded float-end">Simpan Transaksi</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('detail-barang-wrapper');
            const btnAdd = document.getElementById('btn-add-detail');
            const template = wrapper.querySelector('.detail-barang-item').cloneNode(true);

            function fetchStok(selectElement) {
                const barangId = selectElement.value;
                const parent = selectElement.closest('.barang-row');
                const stokInfo = parent.querySelector('.stok-info');
                const jumlahInput = parent.querySelector('.jumlah-input');

                if (!barangId) {
                    stokInfo.textContent = '';
                    jumlahInput.placeholder = '';
                    jumlahInput.max = null;
                    return;
                }

                fetch(`/get-stok-barang/${barangId}`)
                    .then(response => response.json())
                    .then(data => {
                        stokInfo.textContent = `Stok tersedia: ${data.stok_baik}`;
                        jumlahInput.placeholder = `Maks: ${data.stok_baik}`;
                        jumlahInput.max = data.stok_baik;
                    })
                    .catch(() => {
                        stokInfo.textContent = 'Gagal mengambil stok.';
                    });
            }

            btnAdd.addEventListener('click', function() {
                const newItem = template.cloneNode(true);
                newItem.querySelectorAll('input').forEach(input => input.value = '');
                newItem.querySelector('select').selectedIndex = 0;
                newItem.querySelector('.stok-info').textContent = '';
                newItem.querySelector('.jumlah-input').placeholder = '';
                newItem.querySelector('.jumlah-input').max = null;
                wrapper.appendChild(newItem);
            });

            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-detail')) {
                    const items = wrapper.querySelectorAll('.detail-barang-item');
                    if (items.length > 1) {
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
