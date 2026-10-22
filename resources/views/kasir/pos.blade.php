@extends('layouts.app')

@section('title', 'POS Kasir — Kasir Desktop')

@section('content')
<div class="container-fluid px-lg-4">
    <!-- Header Kasir -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-danger">
                <i class="fa-solid fa-cash-register me-2"></i> Mesin Kasir POS — SiToko 002
            </h4>
            <small class="text-muted">
                Kasir: <strong>{{ Auth::user()->nama_lengkap }}</strong> ({{ Auth::user()->nim_nik }}) &bull; Mengurangi stok barang secara otomatis saat transaksi diproses.
            </small>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <a href="{{ route('kasir.riwayat') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat Transaksi
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearPosCart()">
                <i class="fa-solid fa-trash me-1"></i> Reset Kasir
            </button>
        </div>
    </div>

    <!-- POS Split Screen (Desktop-First UX) -->
    <div class="row g-3 pos-container">
        <!-- Panel Kiri: Katalog & Pencarian Barang (Col-7 / Col-8) -->
        <div class="col-lg-7 col-xl-8 pos-catalog-pane">
            <!-- Bar Pencarian & Filter Kategori Cepat -->
            <div class="card card-sitoko border-0 shadow-sm p-3 mb-3">
                <div class="row g-2">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-barcode text-muted"></i></span>
                            <input type="text" id="posSearchInput" class="form-control border-start-0" 
                                   placeholder="Ketik nama produk atau barcode (BRG-...)" onkeyup="filterPosCatalog()">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="posCategorySelect" class="form-select" onchange="filterPosCatalog()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grid Produk Kasir -->
            <div class="row g-2" id="posProductGrid">
                @foreach($barangList as $b)
                    <div class="col-6 col-md-4 col-xl-3 pos-product-item" 
                         data-id="{{ $b->id }}"
                         data-kode="{{ strtolower($b->kode_barang) }}"
                         data-nama="{{ strtolower($b->nama_barang) }}"
                         data-kategori="{{ $b->kategori_id }}"
                         data-harga="{{ $b->harga_jual }}"
                         data-stok="{{ $b->stok }}">
                        
                        <div class="card card-sitoko h-100 p-2 text-start position-relative {{ $b->stok <= 0 ? 'opacity-50' : 'cursor-pointer' }}"
                             style="cursor: pointer;"
                             onclick="posAddItem({{ $b->id }}, '{{ addslashes($b->nama_barang) }}', {{ $b->harga_jual }}, {{ $b->stok }}, '{{ $b->kode_barang }}')">
                            
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" 
                                     class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="overflow-hidden">
                                    <span class="badge bg-light text-muted border px-1" style="font-size: 0.65rem;">{{ $b->kode_barang }}</span>
                                    <div class="fw-bold small text-truncate" title="{{ $b->nama_barang }}">{{ $b->nama_barang }}</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-1 border-top">
                                <span class="fw-bold text-danger small">Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</span>
                                @if($b->stok > 0)
                                    <span class="badge badge-stock badge-stock-in px-2 py-0" style="font-size: 0.65rem;">Stok: {{ $b->stok }}</span>
                                @else
                                    <span class="badge badge-stock badge-stock-out px-2 py-0" style="font-size: 0.65rem;">Habis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Panel Kanan: Struk / Keranjang & Kasir Form (Col-5 / Col-4) -->
        <div class="col-lg-5 col-xl-4">
            <div class="pos-cart-pane">
                <!-- Header Cart Kasir -->
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-basket-shopping me-1 text-danger"></i> Daftar Belanjaan</h6>
                        <small class="text-muted" id="posCartItemCount">0 item dipilih</small>
                    </div>
                    <div class="text-end" style="width: 150px;">
                        <input type="text" id="posPelangganName" class="form-control form-control-sm" placeholder="Nama Pelanggan" value="Pelanggan Umum">
                    </div>
                </div>

                <!-- Item List Kasir -->
                <div class="pos-cart-items" id="posCartItemsList">
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-cart-arrow-down display-4 opacity-50 mb-2"></i>
                        <div class="small">Klik produk di panel kiri untuk memasukkan ke keranjang kasir.</div>
                    </div>
                </div>

                <!-- Footer Kasir & Tombol Bayar -->
                <div class="pos-cart-footer">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Belanja:</span>
                        <span class="fs-4 fw-bold text-danger" id="posTotalDisplay">Rp 0</span>
                    </div>

                    <!-- Pilihan Pembayaran Kasir -->
                    <div class="row g-1 mb-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="posPayMethod" id="posMethodCash" value="tunai" checked onchange="updatePosPaymentUI()">
                            <label class="btn btn-outline-danger btn-sm w-100 py-2 fw-semibold" for="posMethodCash">
                                <i class="fa-solid fa-money-bill-wave d-block mb-1"></i> Tunai
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="posPayMethod" id="posMethodQris" value="qris" onchange="updatePosPaymentUI()">
                            <label class="btn btn-outline-danger btn-sm w-100 py-2 fw-semibold" for="posMethodQris">
                                <i class="fa-solid fa-qrcode d-block mb-1"></i> QRIS
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="posPayMethod" id="posMethodVa" value="va_bca" onchange="updatePosPaymentUI()">
                            <label class="btn btn-outline-danger btn-sm w-100 py-2 fw-semibold" for="posMethodVa">
                                <i class="fa-solid fa-building-columns d-block mb-1"></i> Transfer VA
                            </label>
                        </div>
                    </div>

                    <!-- Input Tunai & Kembalian (Jika Tunai) -->
                    <div id="posCashSection" class="mb-3">
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white small">Uang Diterima:</span>
                            <input type="number" id="posCashInput" class="form-control text-end fw-bold" 
                                   placeholder="0" oninput="calculateChange()">
                        </div>
                        
                        <!-- Quick Cash Buttons -->
                        <div class="d-flex gap-1 mb-2 overflow-auto">
                            <button type="button" class="btn btn-light border btn-sm py-0 px-2 small" onclick="setQuickCash('exact')">Uang Pas</button>
                            <button type="button" class="btn btn-light border btn-sm py-0 px-2 small" onclick="setQuickCash(20000)">20rb</button>
                            <button type="button" class="btn btn-light border btn-sm py-0 px-2 small" onclick="setQuickCash(50000)">50rb</button>
                            <button type="button" class="btn btn-light border btn-sm py-0 px-2 small" onclick="setQuickCash(100000)">100rb</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border">
                            <span class="small fw-semibold">Kembalian:</span>
                            <span class="fw-bold text-success fs-6" id="posChangeDisplay">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tombol Proses Transaksi -->
                    <button type="button" id="btnPosSubmit" class="btn btn-primary-sitoko w-100 py-3 fw-bold fs-6" 
                            onclick="submitPosTransaction()">
                        <i class="fa-solid fa-check-double me-1"></i> Simpan & Selesaikan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let posCart = [];
    let currentTotal = 0;

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function filterPosCatalog() {
        const query = document.getElementById('posSearchInput').value.toLowerCase();
        const cat = document.getElementById('posCategorySelect').value;
        const items = document.querySelectorAll('.pos-product-item');

        items.forEach(el => {
            const nama = el.getAttribute('data-nama');
            const kode = el.getAttribute('data-kode');
            const itemCat = el.getAttribute('data-kategori');

            const matchSearch = nama.includes(query) || kode.includes(query);
            const matchCat = !cat || itemCat === cat;

            if (matchSearch && matchCat) {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });
    }

    function posAddItem(id, nama, harga, maxStok, kode) {
        if (maxStok <= 0) {
            alert('Stok barang habis!');
            return;
        }

        let existing = posCart.find(i => i.id === id);
        if (existing) {
            if (existing.qty < maxStok) {
                existing.qty += 1;
            } else {
                alert('Jumlah maksimal sesuai stok (' + maxStok + ')');
                return;
            }
        } else {
            posCart.push({ id, nama, harga, maxStok, kode, qty: 1 });
        }

        renderPosCart();
    }

    function posUpdateQty(id, delta) {
        let item = posCart.find(i => i.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                posCart = posCart.filter(i => i.id !== id);
            } else if (item.qty > item.maxStok) {
                item.qty = item.maxStok;
                alert('Maksimal stok tercapai: ' + item.maxStok);
            }
            renderPosCart();
        }
    }

    function renderPosCart() {
        const container = document.getElementById('posCartItemsList');
        const countDisplay = document.getElementById('posCartItemCount');
        const totalDisplay = document.getElementById('posTotalDisplay');

        if (posCart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-cart-arrow-down display-4 opacity-50 mb-2"></i>
                    <div class="small">Klik produk di panel kiri untuk memasukkan ke keranjang kasir.</div>
                </div>
            `;
            countDisplay.innerText = '0 item dipilih';
            totalDisplay.innerText = 'Rp 0';
            currentTotal = 0;
            calculateChange();
            return;
        }

        let totalQty = 0;
        currentTotal = 0;
        let html = '';

        posCart.forEach(item => {
            const subtotal = item.qty * item.harga;
            totalQty += item.qty;
            currentTotal += subtotal;

            html += `
                <div class="pos-item-row">
                    <div class="pe-2 overflow-hidden" style="flex: 1;">
                        <div class="fw-bold small text-truncate">${item.nama}</div>
                        <small class="text-muted">${formatRupiah(item.harga)} x ${item.qty}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 85px;">
                            <button class="btn btn-outline-secondary py-0" type="button" onclick="posUpdateQty(${item.id}, -1)">-</button>
                            <input type="text" class="form-control text-center py-0 px-1" value="${item.qty}" readonly>
                            <button class="btn btn-outline-secondary py-0" type="button" onclick="posUpdateQty(${item.id}, 1)">+</button>
                        </div>
                        <div class="fw-bold small text-end" style="min-width: 70px;">
                            ${formatRupiah(subtotal)}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        countDisplay.innerText = totalQty + ' item';
        totalDisplay.innerText = formatRupiah(currentTotal);
        calculateChange();
    }

    function updatePosPaymentUI() {
        const method = document.querySelector('input[name="posPayMethod"]:checked').value;
        const cashSection = document.getElementById('posCashSection');
        if (method === 'tunai') {
            cashSection.style.display = 'block';
        } else {
            cashSection.style.display = 'none';
        }
    }

    function setQuickCash(val) {
        const input = document.getElementById('posCashInput');
        if (val === 'exact') {
            input.value = currentTotal;
        } else {
            input.value = val;
        }
        calculateChange();
    }

    function calculateChange() {
        const method = document.querySelector('input[name="posPayMethod"]:checked').value;
        const changeEl = document.getElementById('posChangeDisplay');
        if (method !== 'tunai') {
            changeEl.innerText = 'Rp 0';
            return;
        }

        const cashInput = parseFloat(document.getElementById('posCashInput').value) || 0;
        const diff = cashInput - currentTotal;
        if (diff >= 0) {
            changeEl.innerText = formatRupiah(diff);
            changeEl.className = 'fw-bold text-success fs-6';
        } else {
            changeEl.innerText = 'Kurang ' + formatRupiah(Math.abs(diff));
            changeEl.className = 'fw-bold text-danger fs-6';
        }
    }

    function clearPosCart() {
        posCart = [];
        document.getElementById('posCashInput').value = '';
        renderPosCart();
    }

    function submitPosTransaction() {
        if (posCart.length === 0) {
            alert('Pilih minimal satu barang untuk melakukan transaksi.');
            return;
        }

        const method = document.querySelector('input[name="posPayMethod"]:checked').value;
        const cashInput = parseFloat(document.getElementById('posCashInput').value) || 0;
        const pelangganName = document.getElementById('posPelangganName').value || 'Pelanggan Umum';

        if (method === 'tunai' && cashInput < currentTotal) {
            alert('Nominal uang tunai yang diterima kurang dari total belanja!');
            return;
        }

        const submitBtn = document.getElementById('btnPosSubmit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Menyimpan...';

        const payload = {
            items: posCart.map(i => ({ barang_id: i.id, jumlah: i.qty })),
            metode_pembayaran: method,
            total_bayar: method === 'tunai' ? cashInput : currentTotal,
            nama_pelanggan: pelangganName
        };

        fetch('{{ route("kasir.checkout") }}', {
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
                clearPosCart();
                window.location.href = '/kasir/struk/' + data.transaksi.id;
            } else {
                alert('Gagal: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-check-double me-1"></i> Simpan & Selesaikan Transaksi';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-check-double me-1"></i> Simpan & Selesaikan Transaksi';
        });
    }
</script>
@endpush
@endsection
