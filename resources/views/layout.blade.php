<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Stok Barang')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            overflow-x: hidden;
            /* supaya tidak ada scroll horizontal */
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            /* tersembunyi di kiri */
            width: 250px;
            height: 100%;
            background: rgb(0, 40, 119);
            color: rgb(255, 234, 0);
            transition: left 0.3s ease;
            z-index: 1030;
            padding-top: 60px;
        }

        .sidebar.active {
            left: 0;
        }

        .main-content {
            transition: margin-left 0.3s ease;
            margin-left: 0;
        }

        .main-content.shifted {
            margin-left: 250px;
            /* bergeser ketika sidebar terbuka */
        }

        .sidebar .nav-link {
            color: black;
        }

        .sidebar .nav-link:hover {
            background: #d99841;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1040;
            background: #fff;
        }
    </style>
</head>

<body>
    <div class="d-flex justify-content-between align-items-center border-bottom topbar"
        style="background-color: rgb(0, 40, 119)">
        <div class="d-flex justify-content-start p-3">
            <button id="sidebarToggle" class="btn btn-outline-light" type="button">
                <i class="bi bi-list"></i>
            </button>
        </div>
        <div class="d-flex justify-content-center align-items-center text-white">

            <a href="{{ route('home') }}" class="text-white text-decoration-none">
                <img src="https://i.ibb.co.com/FNckrp1/image.jpg" alt="image" class="me-3"
                    style="height: 50px; width: 50px;">
                <b>PT Agung Mas Sentosa</b>
            </a>
        </div>
        <div class="d-flex justify-content-end p-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>

    <div id="sidebar" class="sidebar text-white">
        <div class="py-4">
            {{-- Tampilkan role pengguna --}}
            @auth
                <div class="px-3 mb-3">
                    <b class="d-block">{{ Auth::user()->name }}</b>
                    <small class="text-warning">{{ strtoupper(Auth::user()->role) }}</small>
                </div>
            @endauth
            <hr>

            <div class="nav flex-column ">

                {{-- MENU KHUSUS ADMIN (Dashboard) --}}
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="nav-link text-white"><i class="bi bi-grid me-1"></i>
                        Dashboard</a>
                @endif

                {{-- MENU UNTUK ADMIN, GUDANG, KASIR (Daftar Barang) --}}
                @if (Auth::user()->isAdmin() || Auth::user()->isGudang() || Auth::user()->isKasir())
                    <a href="{{ route('barang.index') }}" class="nav-link text-white"><i class="bi bi-box-seam"></i>
                        Daftar Barang</a>
                @endif

                {{-- MENU UNTUK ADMIN DAN GUDANG --}}
                @if (Auth::user()->isAdmin() || Auth::user()->isGudang())
                    <a href="{{ route('transaksi-masuk.index') }}" class="nav-link text-white"><i
                            class="bi bi-box-arrow-in-left"></i>
                        Barang Masuk</a>

                    {{-- MENU BARU: MASTER SUPPLIER --}}
                    <a href="{{ route('suppliers.index') }}" class="nav-link text-white"><i
                            class="bi bi-people me-1"></i>
                        Master Supplier</a>

                    <a href="{{ route('mutasi-kondisi.create') }}" class="nav-link text-white"><i
                            class="bi bi-arrow-repeat"></i>
                        Mutasi Stok</a>
                    <a href="{{ route('barang.list') }}" class="nav-link text-white"><i
                            class="bi bi-clipboard-data me-1"></i> Laporan
                        Barang</a>
                    <a href="{{ route('barang.rusak') }}" class="nav-link text-white"><i
                            class="bi bi-clipboard-data me-1"></i> Laporan
                        Barang Rusak</a>
                @endif

                {{-- MENU UNTUK ADMIN DAN KASIR --}}
                @if (Auth::user()->isAdmin() || Auth::user()->isKasir())
                    <a href="{{ route('transaksi-keluar.index') }}" class="nav-link text-white"><i
                            class="bi bi-box-arrow-right"></i> Barang Keluar</a>

                    {{-- MENU BARU: MASTER CUSTOMER --}}
                    <a href="{{ route('customers.index') }}" class="nav-link text-white"><i
                            class="bi bi-people-fill me-1"></i>
                        Master Customer</a>
                @endif

                {{-- MENU KHUSUS ADMIN (Laporan Transaksi) --}}
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('laporan.index') }}" class="nav-link text-white"><i
                            class="bi bi-clipboard-data me-1"></i>
                        Laporan Transaksi</a>
                @endif

            </div>
        </div>
    </div>

    <div id="mainContent" class="main-content">
        <main class="container py-4">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("mainContent");
        const toggleBtn = document.getElementById("sidebarToggle");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("active");
            mainContent.classList.toggle("shifted");
        });
    </script>
    @stack('scripts')
</body>

</html>
