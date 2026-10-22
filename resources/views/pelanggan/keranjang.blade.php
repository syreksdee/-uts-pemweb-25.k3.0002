@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container py-3">
    <h4 class="fw-bold mb-4">
        <i class="fa-solid fa-cart-shopping text-danger me-2"></i> Keranjang Belanja Anda
    </h4>

    <div class="row g-4" id="cartContainer">
        <!-- List Item Keranjang -->
        <div class="col-lg-8">
            <div class="card card-sitoko shadow-sm border-0 mb-3">
                <div class="card-body p-3 p-md-4">
                    <div id="cartItemsList">
                        <!-- Populated by JavaScript -->
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-spinner fa-spin fs-2"></i>
                            <div class="mt-2">Memuat keranjang...</div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Lanjut Belanja
            </a>
        </div>

        <!-- Ringkasan & Form Checkout -->
        <div class="col-lg-4">
            <div class="card card-sitoko shadow-sm border-0 position-sticky" style="top: 85px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Barang</span>
                        <span id="summaryTotalItems" class="fw-semibold">0 item</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Biaya Layanan</span>
                        <span class="text-success fw-bold">Gratis</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total Tagihan</span>
                        <span id="summaryTotalPrice" class="fw-bold fs-5 text-danger">Rp 0</span>
                    </div>

                    @auth
                        <form id="checkoutForm" onsubmit="submitCheckout(event)">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Pilih Metode Pembayaran <span class="text-danger">*</span></label>
                                
                                <div class="form-check p-3 border rounded mb-2 bg-light">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payQris" value="qris" checked>
                                    <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="payQris">
                                        <span><strong>QRIS</strong> (Gopay, OVO, Dana, ShopeePay)</span>
                                        <i class="fa-solid fa-qrcode fs-5 text-danger"></i>
                                    </label>
                                </div>

                                <div class="form-check p-3 border rounded mb-2 bg-light">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payVaBca" value="va_bca">
                                    <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="payVaBca">
                                        <span><strong>Virtual Account BCA</strong></span>
                                        <span class="badge bg-primary">BCA VA</span>
                                    </label>
                                </div>

                                <div class="form-check p-3 border rounded mb-2 bg-light">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payVaMandiri" value="va_mandiri">
                                    <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="payVaMandiri">
                                        <span><strong>Virtual Account Mandiri</strong></span>
                                        <span class="badge bg-warning text-dark">Mandiri VA</span>
                                    </label>
                                </div>

                                <div class="form-check p-3 border rounded mb-2 bg-light">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payVaBri" value="va_bri">
                                    <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="payVaBri">
                                        <span><strong>Virtual Account BRI</strong></span>
                                        <span class="badge bg-info text-dark">BRIVA</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="catatan" class="form-label fw-semibold small">Catatan Tambahan (Opsional)</label>
                                <input type="text" id="catatan" name="catatan" class="form-control form-control-sm" placeholder="Contoh: Titip di pos satpam">
                            </div>

                            <button type="submit" id="btnCheckoutSubmit" class="btn btn-primary-sitoko w-100 py-2 fw-bold">
                                <i class="fa-solid fa-lock me-1"></i> Lanjut ke Pembayaran
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning small py-2 mb-3">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> Silakan masuk akun terlebih dahulu untuk melakukan checkout.
                        </div>
                        <a href="{{ route('demo.login', 'pelanggan') }}" class="btn btn-primary-sitoko w-100 mb-2 py-2">
                            <i class="fa-solid fa-bolt me-1"></i> Masuk Cepat sebagai Pelanggan
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 py-2">
                            Masuk Akun Lain
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function renderCart() {
        const cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
        const listEl = document.getElementById('cartItemsList');
        const submitBtn = document.getElementById('btnCheckoutSubmit');

        if (cart.length === 0) {
            listEl.innerHTML = `
                <div class="text-center py-5">
                    <i class="fa-solid fa-cart-shopping display-1 text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold">Keranjang Belanja Kosong</h5>
                    <p class="text-muted small">Anda belum menambahkan produk apa pun ke dalam keranjang.</p>
                    <a href="{{ route('toko.index') }}" class="btn btn-primary-sitoko btn-sm mt-2">Mulai Belanja</a>
                </div>
            `;
            document.getElementById('summaryTotalItems').innerText = '0 item';
            document.getElementById('summaryTotalPrice').innerText = 'Rp 0';
            if (submitBtn) submitBtn.disabled = true;
            return;
        }

        if (submitBtn) submitBtn.disabled = false;

        let totalQty = 0;
        let totalPrice = 0;
        let html = '';

        cart.forEach((item, index) => {
            const subtotal = item.jumlah * item.harga_satuan;
            totalQty += item.jumlah;
            totalPrice += subtotal;

            html += `
                <div class="row align-items-center py-3 ${index > 0 ? 'border-top' : ''}">
                    <div class="col-3 col-md-2 text-center">
                        <img src="${item.gambar_url}" alt="${item.nama_barang}" class="img-fluid rounded border" style="max-height: 70px; object-fit: cover;">
                    </div>
                    <div class="col-9 col-md-5">
                        <h6 class="fw-bold mb-1">${item.nama_barang}</h6>
                        <div class="text-danger fw-semibold">${formatRupiah(item.harga_satuan)}</div>
                        <small class="text-muted">Maks. stok: ${item.max_stok}</small>
                    </div>
                    <div class="col-7 col-md-3 mt-2 mt-md-0">
                        <div class="input-group input-group-sm" style="width: 110px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(${item.barang_id}, -1)">-</button>
                            <input type="text" class="form-control text-center" value="${item.jumlah}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(${item.barang_id}, 1)">+</button>
                        </div>
                    </div>
                    <div class="col-5 col-md-2 text-end mt-2 mt-md-0">
                        <div class="fw-bold text-dark mb-1">${formatRupiah(subtotal)}</div>
                        <button type="button" class="btn btn-link text-danger p-0 small text-decoration-none" onclick="removeItem(${item.barang_id})">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </div>
                </div>
            `;
        });

        listEl.innerHTML = html;
        document.getElementById('summaryTotalItems').innerText = totalQty + ' item';
        document.getElementById('summaryTotalPrice').innerText = formatRupiah(totalPrice);
    }

    function updateItemQty(id, change) {
        let cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
        let item = cart.find(i => i.barang_id === id);
        if (item) {
            item.jumlah += change;
            if (item.jumlah <= 0) {
                cart = cart.filter(i => i.barang_id !== id);
            } else if (item.jumlah > item.max_stok) {
                item.jumlah = item.max_stok;
                alert('Jumlah maksimal sesuai stok (' + item.max_stok + ')');
            }
            localStorage.setItem('sitoko_cart', JSON.stringify(cart));
            renderCart();
            updateCartBadge();
        }
    }

    function removeItem(id) {
        let cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
        cart = cart.filter(i => i.barang_id !== id);
        localStorage.setItem('sitoko_cart', JSON.stringify(cart));
        renderCart();
        updateCartBadge();
    }

    function submitCheckout(e) {
        e.preventDefault();
        const cart = JSON.parse(localStorage.getItem('sitoko_cart') || '[]');
        if (cart.length === 0) {
            alert('Keranjang belanja kosong!');
            return;
        }

        const metode = document.querySelector('input[name="metode_pembayaran"]:checked').value;
        const catatan = document.getElementById('catatan').value;
        const submitBtn = document.getElementById('btnCheckoutSubmit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Memproses...';

        const payload = {
            items: cart.map(i => ({ barang_id: i.barang_id, jumlah: i.jumlah })),
            metode_pembayaran: metode,
            catatan: catatan
        };

        fetch('{{ route("toko.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Clear cart on successful checkout creation
                localStorage.removeItem('sitoko_cart');
                updateCartBadge();
                window.location.href = data.redirect;
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-lock me-1"></i> Lanjut ke Pembayaran';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-lock me-1"></i> Lanjut ke Pembayaran';
        });
    }

    document.addEventListener('DOMContentLoaded', renderCart);
</script>
@endpush
@endsection
