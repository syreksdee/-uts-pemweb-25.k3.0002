@extends('layouts.app')

@section('title', $barang->nama_barang)

@section('content')
<div class="container py-3">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('toko.index') }}" class="text-danger text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $barang->nama_barang }}</li>
        </ol>
    </nav>

    <div class="card card-sitoko shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="row g-4 align-items-center">
                <div class="col-md-5 text-center">
                    <img src="{{ $barang->gambar_url }}" alt="{{ $barang->nama_barang }}" 
                         class="img-fluid rounded-3 shadow-sm border" style="max-height: 380px; width: 100%; object-fit: cover;">
                </div>
                <div class="col-md-7">
                    <span class="badge bg-light text-muted border px-2 py-1 mb-2">
                        <i class="fa-solid fa-barcode me-1"></i> {{ $barang->kode_barang }}
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 mb-2">
                        {{ $barang->kategori->nama_kategori ?? 'Umum' }}
                    </span>

                    <h3 class="fw-bold text-dark mb-2">{{ $barang->nama_barang }}</h3>

                    <div class="display-6 fw-bold text-danger mb-3">
                        Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                    </div>

                    <div class="mb-3">
                        <span class="fw-semibold">Status Stok:</span>
                        @if($barang->stok > 0)
                            <span class="badge badge-stock badge-stock-in fs-6 ms-1">{{ $barang->stok }} {{ $barang->satuan }} tersedia</span>
                        @else
                            <span class="badge badge-stock badge-stock-out fs-6 ms-1">Stok Habis</span>
                        @endif
                    </div>

                    <p class="text-muted mb-4 leading-relaxed">
                        {{ $barang->deskripsi ?: 'Tidak ada deskripsi rinci untuk produk ini.' }}
                    </p>

                    @if($barang->stok > 0)
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <label class="fw-semibold">Jumlah:</label>
                            <div class="input-group" style="width: 140px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                                <input type="number" id="detailQty" class="form-control text-center" value="1" min="1" max="{{ $barang->stok }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary-sitoko px-4 py-2" onclick="addCurrentToCart()">
                                <i class="fa-solid fa-cart-plus me-2"></i> Tambah ke Keranjang
                            </button>
                            <a href="{{ route('toko.keranjang') }}" class="btn btn-outline-secondary px-3 py-2">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Lihat Keranjang
                            </a>
                        </div>
                    @else
                        <button class="btn btn-secondary px-4 py-2" disabled>Stok Habis Sementara</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const maxStock = {{ $barang->stok }};
    function adjustQty(amount) {
        const input = document.getElementById('detailQty');
        let current = parseInt(input.value) || 1;
        current += amount;
        if (current < 1) current = 1;
        if (current > maxStock) current = maxStock;
        input.value = current;
    }

    function addCurrentToCart() {
        const qty = parseInt(document.getElementById('detailQty').value) || 1;
        let cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
        let existing = cart.find(item => item.barang_id === {{ $barang->id }});

        if (existing) {
            existing.jumlah = Math.min(existing.jumlah + qty, maxStock);
        } else {
            cart.push({
                barang_id: {{ $barang->id }},
                nama_barang: '{{ addslashes($barang->nama_barang) }}',
                harga_satuan: {{ $barang->harga_jual }},
                gambar_url: '{{ $barang->gambar_url }}',
                max_stok: maxStock,
                jumlah: qty
            });
        }

        localStorage.setItem('sitoko_cart', JSON.stringify(cart));
        updateCartBadge();
        alert('Produk berhasil ditambahkan ke keranjang belanja!');
    }
</script>
@endpush
@endsection
