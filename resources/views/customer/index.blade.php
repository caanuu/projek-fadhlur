@extends('layout')
@section('title', 'Data Customer')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-success mb-0"><i class="bi bi-people-fill me-2"></i>Data Customer</h2>
                <p class="text-muted mb-0">Kelola daftar pelanggan toko anda di sini.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="btn btn-success btn-lg shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i>Tambah Customer
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-3 rounded-start">No</th>
                                <th class="py-3">Nama Customer</th>
                                <th class="py-3">Kontak</th>
                                <th class="py-3">Alamat</th>
                                <th class="py-3 text-center rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $index => $c)
                                <tr>
                                    <td class="ps-3 fw-bold text-secondary">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $c->nama_customer }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($c->no_hp)
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-whatsapp me-1 text-success"></i> {{ $c->no_hp }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small" style="max-width: 250px;">
                                        {{ $c->alamat ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('customers.edit', $c->id) }}"
                                                class="btn btn-outline-warning btn-sm" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('customers.destroy', $c->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Hapus customer {{ $c->nama_customer }}?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-emoji-frown fs-1 d-block mb-2"></i>
                                        Belum ada data customer.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
