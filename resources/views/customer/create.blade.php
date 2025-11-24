@extends('layout')
@section('title', 'Tambah Customer')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-success"><i class="bi bi-person-plus me-2"></i>Tambah Customer Baru</h4>
                    <p class="text-muted small">Masukkan data pelanggan baru untuk memudahkan transaksi.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nama_customer" class="form-control"
                                        placeholder="Nama Pelanggan" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" name="no_hp" class="form-control" placeholder="08...">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Domisili</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat pelanggan..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('customers.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-success px-4 rounded-pill shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Simpan Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
