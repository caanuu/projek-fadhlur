@extends('layout')
@section('title', 'Tambah Supplier')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-primary"><i class="bi bi-person-plus-fill me-2"></i>Tambah Supplier Baru</h4>
                    <p class="text-muted small">Lengkapi informasi di bawah untuk menambahkan mitra supplier.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Supplier <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-building"></i></span>
                                    <input type="text" name="nama_supplier" class="form-control"
                                        placeholder="Contoh: PT. Sumber Makmur" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Nomor Telepon / HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="no_hp" class="form-control" placeholder="0812...">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap supplier..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                <i class="bi bi-save me-1"></i> Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
