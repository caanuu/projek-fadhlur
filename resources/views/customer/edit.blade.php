@extends('layout')
@section('title', 'Edit Customer')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-warning"><i class="bi bi-pencil me-2"></i>Edit Data Customer</h4>
                    <p class="text-muted small">Perbarui data pelanggan yang sudah ada.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nama_customer" class="form-control"
                                        value="{{ old('nama_customer', $customer->nama_customer) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" name="no_hp" class="form-control"
                                        value="{{ old('no_hp', $customer->no_hp) }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Domisili</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $customer->alamat) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('customers.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-warning text-white px-4 rounded-pill shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Perbarui Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
