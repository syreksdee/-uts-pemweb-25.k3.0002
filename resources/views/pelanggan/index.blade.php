@extends('layouts.app')

@section('title', 'Katalog Belanja')

@section('content')
<div class="container">
    <!-- Hero Banner (Mobile-First Style) -->
    <div class="card border-0 mb-4 text-white overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #E53935 0%, #B71C1C 100%); border-radius: var(--radius-md);">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-white text-danger fw-bold px-3 py-1 mb-2">
                        <i class="fa-solid fa-sparkles me-1"></i> Toko Online Sederhana 002
                    </span>
                    <h2 class="display-6 fw-bold mb-2">Selamat Datang di SiToko 002</h2>
                    <p class="lead mb-3 text-white-50 fs-6">Belanja kebutuhan harian praktis, mudah, cepat, dan aman dengan dukungan QRIS serta Virtual Account Bank.</p>
                    <a href="#katalog" class="btn btn-light text-danger fw-bold px-4 py-2">
                        <i class="fa-solid fa-bag-shopping me-1"></i> Mulai Belanja
                    </a>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class="fa-solid fa-cart-flatbed-suitcase display-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Form (Prepared Parameter Binding) -->
    <div id="katalog" class="mb-4">
        <form action="{{ route('toko.index') }}" method="GET" class="row g-2">
            <div class="col-md-8 col-12">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 py-2" 
                           placeholder="Cari nama barang atau kode barang..." value="{{ $search ?? '' }}">
                    @if($kategoriId)
                        <input type="hidden" name="kategori_id" value="{{ $kategoriId }}">
                    @endif
                    <button type="submit" class="btn btn-primary-sitoko px-4">Cari</button>
                    @if($search || $kategoriId)
                        <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Kategori Horizontal Pills Filter -->
    <div class="category-pills mb-4">
        <a href="{{ route('toko.index', ['search' => $search]) }}" 
           class="cat-pill {{ empty($kategoriId) ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i> Semua Kategori
        </a>
        @foreach($kategoriList as $kat)
            <a href="{{ route('toko.index', ['kategori_id' => $kat->id, 'search' => $search]) }}" 
               class="cat-pill {{ ($kategoriId == $kat->id) ? 'active' : '' }}">
                <i class="fa-solid {{ $kat->icon ?? 'fa-tag' }}"></i> {{ $kat->nama_kategori }}
            </a>
        @endforeach
    </div>

    <!-- Product Grid -->
    <div class="row g-3">
        @forelse($barangList as $barang)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-sitoko card-product">
                    <a href="{{ route('toko.produk.show', $barang->id) }}" class="product-img-wrapper text-decoration-none">
                        <img src="{{ $barang->gambar_url }}" alt="{{ $barang->nama_barang }}" loading="lazy">
                        <div class="position-absolute top-0 end-0 m-2">
                            @if($barang->stok > 0)
                                <span class="badge badge-stock badge-stock-in">Stok: {{ $barang->stok }}</span>
                            @else
                                <span class="badge badge-stock badge-stock-out">Habis</span>
                            @endif
                        </div>
                    </a>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem; font-weight: 700;">
                                {{ $barang->kategori->nama_kategori ?? 'Umum' }}
                            </small>
                            <h6 class="fw-bold mb-1 mt-1 text-truncate" title="{{ $barang->nama_barang }}">
                                <a href="{{ route('toko.produk.show', $barang->id) }}" class="text-dark text-decoration-none">
                                    {{ $barang->nama_barang }}
                                </a>
                            </h6>
                            <div class="fw-bold text-danger fs-5 mb-2">
                                Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            @if($barang->stok > 0)
                                <button type="button" class="btn btn-outline-sitoko btn-sm w-100 py-1"
                                        onclick="addToCart({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}', {{ $barang->harga_jual }}, '{{ $barang->gambar_url }}', {{ $barang->stok }})">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Beli
                                </button>
                            @else
                                <button class="btn btn-secondary btn-sm w-100 py-1" disabled>Stok Habis</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center">
                <div class="text-muted mb-3"><i class="fa-solid fa-box-open display-1"></i></div>
                <h5 class="fw-bold">Tidak ada barang yang ditemukan</h5>
                <p class="text-muted">Coba ganti kata kunci pencarian atau pilih kategori lain.</p>
                <a href="{{ route('toko.index') }}" class="btn btn-primary-sitoko">Lihat Semua Barang</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination (Requirement 8) -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $barangList->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    function addToCart(id, nama, harga, gambar, maxStok) {
        let cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
        let existing = cart.find(item => item.barang_id === id);

        if (existing) {
            if (existing.jumlah < maxStok) {
                existing.jumlah += 1;
            } else {
                alert('Jumlah maksimal sesuai stok tersedia (' + maxStok + ') telah tercapai.');
                return;
            }
        } else {
            cart.push({
                barang_id: id,
                nama_barang: nama,
                harga_satuan: harga,
                gambar_url: gambar,
                max_stok: maxStok,
                jumlah: 1
            });
        }

        localStorage.setItem('sitoko_cart', JSON.stringify(cart));
        updateCartBadge();

        // Toast feedback
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 m-3 p-3 bg-dark text-white rounded-3 shadow-lg';
        toast.style.zIndex = '9999';
        toast.innerHTML = `<i class="fa-solid fa-circle-check text-success me-2"></i> ${nama} ditambahkan ke keranjang!`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
</script>
@endpush
@endsection
