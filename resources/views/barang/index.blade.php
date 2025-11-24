@extends('layout')

@section('title', 'Daftar Barang')

@section('content')

    {{-- STYLE KHUSUS PRINT --}}
    <style>
        /* Sembunyikan Header Print di tampilan layar normal */
        #print-header {
            display: none;
        }

        @media print {

            /* Atur Ukuran Kertas Landscape agar tabel muat */
            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                background: white;
                font-family: 'Times New Roman', Times, serif;
                color: black;
            }

            /* Sembunyikan elemen UI website */
            .no-print,
            .sidebar,
            .topbar,
            .btn,
            .alert,
            footer {
                display: none !important;
            }

            /* Reset margin agar full page */
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            /* Tampilkan Header Print (Kop Surat) */
            #print-header {
                display: block;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid black;
                padding-bottom: 10px;
            }

            /* Styling Tabel Print */
            .table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid black !important;
            }

            .table th,
            .table td {
                border: 1px solid black !important;
                padding: 8px;
                font-size: 12pt;
                color: black !important;
            }

            .table thead {
                background-color: #ddd !important;
                /* Warna abu-abu untuk header saat print */
                -webkit-print-color-adjust: exact;
                /* Paksa cetak warna background */
            }

            /* Sembunyikan kolom aksi saat print */
            th:last-child,
            td:last-child {
                display: none;
            }
        }
    </style>

    {{-- KOP SURAT (Hanya Muncul Saat Print) --}}
    <div id="print-header">
        <h2 style="margin: 0;">PT AGUNG MAS SENTOSA</h2>
        <p style="margin: 5px 0;">Jl. Contoh Alamat No. 123, Kota Medan, Indonesia</p>
        <h3 style="margin-top: 15px;">LAPORAN DAFTAR STOK BARANG</h3>
        <small>Dicetak pada: {{ now()->format('d F Y H:i') }}</small>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h2>Daftar Barang</h2>

        <div class="d-flex gap-2">
            {{-- Tombol Export Excel --}}
            <a href="{{ route('barang.export') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            {{-- Tombol Print --}}
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="bi bi-printer"></i> Cetak PDF/Print
            </button>

            @if (Auth::user()->isAdmin() || Auth::user()->isGudang() || Auth::user()->isKasir())
                <a href="{{ route('barang.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Barang
                </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th class="text-center" width="10%">Stok Baik</th>
                            <th class="text-center" width="10%">Stok Rusak</th>
                            <th class="text-center no-print" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $index => $barang)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $barang->kode_barang }}</td>
                                <td>{{ $barang->nama_barang }}</td>
                                <td><span class="badge bg-info text-dark">{{ $barang->jenis_barang }}</span></td>
                                <td>{{ $barang->keterangan ?? '-' }}</td>
                                <td class="text-center fw-bold text-success">{{ $barang->stok_baik }}</td>
                                <td class="text-center fw-bold text-danger">{{ $barang->stok_rusak }}</td>

                                {{-- Kolom Aksi (Hilang saat Print) --}}
                                <td class="text-center no-print">
                                    @if (Auth::user()->isAdmin() || Auth::user()->isGudang() || Auth::user()->isKasir())
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('barang.edit', $barang->id) }}"
                                                class="btn btn-sm btn-warning text-white" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('barang.destroy', $barang->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin hapus barang ini selamanya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
