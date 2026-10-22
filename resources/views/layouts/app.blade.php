<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Requirement 7: Nama toko harus ada di judul tab browser --}}
    <title>@yield('title', 'Beranda') — SiToko — Toko Online Sederhana 002</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom Theme Stylesheet (#E53935) -->
    <link rel="stylesheet" href="{{ asset('css/sitoko.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Navbar (Requirement 7: Nama toko harus ada di navbar) -->
    <nav class="navbar navbar-expand-lg navbar-sitoko">
        <div class="container">
            <a class="navbar-brand brand-sitoko" href="{{ route('toko.index') }}">
                <i class="fa-solid fa-store text-danger"></i>
                <span>SiToko — Toko Online Sederhana 002</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link nav-link-sitoko {{ request()->routeIs('toko.index') ? 'active' : '' }}" href="{{ route('toko.index') }}">
                            <i class="fa-solid fa-bag-shopping me-1"></i> Belanja
                        </a>
                    </li>

                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link nav-link-sitoko {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fa-solid fa-chart-line me-1 text-primary"></i> Modul Admin
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isKasir() || Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link nav-link-sitoko {{ request()->routeIs('kasir.*') ? 'active' : '' }}" href="{{ route('kasir.pos') }}">
                                    <i class="fa-solid fa-cash-register me-1 text-success"></i> Modul Kasir (POS)
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isGudang() || Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link nav-link-sitoko {{ request()->routeIs('gudang.*') ? 'active' : '' }}" href="{{ route('gudang.barang.index') }}">
                                    <i class="fa-solid fa-boxes-stacked me-1 text-warning"></i> Modul Gudang
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->isPelanggan())
                            <li class="nav-item">
                                <a class="nav-link nav-link-sitoko {{ request()->routeIs('toko.pesanan') ? 'active' : '' }}" href="{{ route('toko.pesanan') }}">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Pesanan Saya
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <!-- Tombol Keranjang -->
                    <a href="{{ route('toko.keranjang') }}" class="btn btn-outline-sitoko position-relative px-3 py-2">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="navCartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary-sitoko px-3 py-2">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
                        </a>
                    @else
                        <!-- Profil User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border px-2 py-1" type="button" data-bs-toggle="dropdown">
                                <img src="{{ Auth::user()->foto_url }}" alt="Avatar" class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                                <div class="text-start d-none d-sm-block">
                                    <div class="fw-bold small lh-1">{{ Str::limit(Auth::user()->nama_lengkap, 16) }}</div>
                                    <span class="badge badge-role badge-role-{{ Auth::user()->role->kode_role ?? 'user' }} px-1 py-0" style="font-size: 0.65rem;">
                                        {{ Auth::user()->role->nama_role ?? 'User' }}
                                    </span>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2">
                                <li class="px-3 py-1">
                                    <small class="text-muted d-block">Login sebagai:</small>
                                    <strong>{{ Auth::user()->nim_nik }}</strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.show') }}">
                                        <i class="fa-solid fa-user-pen me-2 text-primary"></i> Edit Profil & Foto
                                    </a>
                                </li>
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.laporan') }}"><i class="fa-solid fa-file-invoice-dollar me-2 text-info"></i> Laporan Penjualan</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar (Logout)
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Notifikasi Flash Message -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong><i class="fa-solid fa-circle-xmark me-2"></i> Terjadi kesalahan validasi:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Konten Utama -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation (Requirement 6: Mobile-first UX untuk Pelanggan & Admin) -->
    <div class="mobile-bottom-bar d-md-none">
        <a href="{{ route('toko.index') }}" class="mobile-bar-item {{ request()->routeIs('toko.index') ? 'active' : '' }}">
            <i class="fa-solid fa-store"></i>
            <span>Toko</span>
        </a>
        <a href="{{ route('toko.keranjang') }}" class="mobile-bar-item position-relative {{ request()->routeIs('toko.keranjang') ? 'active' : '' }}">
            <i class="fa-solid fa-cart-shopping"></i>
            <span id="mobileCartCount" class="badge rounded-pill bg-danger" style="position: absolute; top: -4px; right: 8px; font-size: 0.6rem;">0</span>
            <span>Keranjang</span>
        </a>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="mobile-bar-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Admin</span>
                </a>
            @elseif(Auth::user()->isKasir())
                <a href="{{ route('kasir.pos') }}" class="mobile-bar-item {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Kasir</span>
                </a>
            @elseif(Auth::user()->isGudang())
                <a href="{{ route('gudang.barang.index') }}" class="mobile-bar-item {{ request()->routeIs('gudang.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Gudang</span>
                </a>
            @else
                <a href="{{ route('toko.pesanan') }}" class="mobile-bar-item {{ request()->routeIs('toko.pesanan') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Pesanan</span>
                </a>
            @endif
            <a href="{{ route('profile.show') }}" class="mobile-bar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>Profil</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="mobile-bar-item {{ request()->routeIs('login') ? 'active' : '' }}">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk</span>
            </a>
        @endauth
    </div>

    <!-- Footer Wajib (Requirement 6: © 2026 Aloysius Jaison Gondopurwanto– 25.K3.0002 beserta thumbnail foto) -->
    <footer class="footer-sitoko">
        <div class="container">
            <div class="row align-items-center text-center text-md-start gy-3">
                <div class="col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                    {{-- Thumbnail Foto Pengembang --}}
                    <img src="{{ asset('uploads/profil/0002.jpg') }}" 
                         alt="Aloysius Jaison Gondopurwanto" 
                         class="footer-thumbnail" 
                         onerror="this.src='{{ asset('uploads/profil/default.png') }}'">
                    <div>
                        <div class="fw-bold text-white fs-6">
                            &copy; 2026 Aloysius Jaison Gondopurwanto&ndash; 25.K3.0002
                        </div>
                        <small class="text-white-50">SiToko — Toko Online Sederhana 002 &bull; Midterm Project Internet Programming</small>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="badge bg-danger px-3 py-2 me-2">
                        <i class="fa-solid fa-shield-halved me-1"></i> Prepared Statements Protected
                    </span>
                    <a href="{{ url('/api/identitas') }}" target="_blank" class="badge bg-dark border border-secondary text-decoration-none px-3 py-2">
                        <i class="fa-solid fa-code me-1"></i> API Identitas
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart Counter Script -->
    <script>
        function updateCartBadge() {
            try {
                const cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
                const count = cart.reduce((total, item) => total + (item.jumlah || 1), 0);
                const badgeNav = document.getElementById('navCartCount');
                const badgeMob = document.getElementById('mobileCartCount');
                if (badgeNav) badgeNav.innerText = count;
                if (badgeMob) badgeMob.innerText = count;
            } catch (e) {
                console.error(e);
            }
        }
        document.addEventListener('DOMContentLoaded', updateCartBadge);
    </script>

    @stack('scripts')
</body>
</html>
