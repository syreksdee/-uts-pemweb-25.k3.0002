@extends('layouts.app')

@section('title', 'Pembayaran ' . $transaksi->nomor_transaksi)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-sitoko shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom text-center">
                    <span class="badge bg-light text-muted border px-2 py-1 mb-1">
                        {{ $transaksi->nomor_transaksi }}
                    </span>
                    <h5 class="fw-bold mb-0">Pembayaran Pesanan</h5>
                </div>

                <div class="card-body p-4 text-center">
                    @if($transaksi->status_pembayaran === 'lunas')
                        <div class="alert alert-success d-flex align-items-center justify-content-center py-3 mb-4">
                            <i class="fa-solid fa-circle-check fs-2 text-success me-3"></i>
                            <div class="text-start">
                                <h6 class="fw-bold mb-0">Pembayaran Berhasil / LUNAS</h6>
                                <small>Terima kasih! Pesanan Anda telah lunas dan stok barang otomatis terpotong.</small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 small mb-4">
                            <i class="fa-solid fa-clock me-1"></i> Selesaikan pembayaran dalam <strong>23:59:59</strong>
                        </div>
                    @endif

                    <div class="bg-light p-3 rounded mb-4">
                        <small class="text-muted d-block">Total yang Harus Dibayar:</small>
                        <div class="display-6 fw-bold text-danger">
                            Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Mode Bayar QRIS --}}
                    @if($transaksi->metode_pembayaran === 'qris')
                        <div class="card border border-danger-subtle p-3 mb-4 bg-white">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <span class="fw-bold text-danger"><i class="fa-solid fa-qrcode me-1"></i> QRIS Standar Pembayaran Nasional</span>
                                <span class="badge bg-danger">NMID: ID102002</span>
                            </div>

                            <div class="py-2">
                                {{-- QR Code SVG Graphic --}}
                                <div class="p-3 bg-white border d-inline-block rounded shadow-sm mb-2">
                                    <svg width="200" height="200" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <!-- QR Corner 1 -->
                                        <rect width="30" height="30" fill="#111" />
                                        <rect x="5" y="5" width="20" height="20" fill="#fff" />
                                        <rect x="9" y="9" width="12" height="12" fill="#111" />
                                        <!-- QR Corner 2 -->
                                        <rect x="70" width="30" height="30" fill="#111" />
                                        <rect x="75" y="5" width="20" height="20" fill="#fff" />
                                        <rect x="79" y="9" width="12" height="12" fill="#111" />
                                        <!-- QR Corner 3 -->
                                        <rect y="70" width="30" height="30" fill="#111" />
                                        <rect x="5" y="75" width="20" height="20" fill="#fff" />
                                        <rect x="9" y="79" width="12" height="12" fill="#111" />
                                        <!-- Data Matrix Dots -->
                                        <rect x="35" y="10" width="8" height="8" fill="#111" />
                                        <rect x="50" y="10" width="8" height="8" fill="#E53935" />
                                        <rect x="35" y="25" width="8" height="8" fill="#111" />
                                        <rect x="45" y="35" width="10" height="10" fill="#111" />
                                        <rect x="60" y="35" width="8" height="8" fill="#111" />
                                        <rect x="10" y="40" width="8" height="8" fill="#111" />
                                        <rect x="25" y="40" width="8" height="8" fill="#E53935" />
                                        <rect x="75" y="45" width="12" height="8" fill="#111" />
                                        <rect x="35" y="60" width="10" height="10" fill="#E53935" />
                                        <rect x="55" y="60" width="8" height="8" fill="#111" />
                                        <rect x="70" y="60" width="15" height="8" fill="#111" />
                                        <rect x="40" y="75" width="8" height="15" fill="#111" />
                                        <rect x="60" y="80" width="15" height="10" fill="#111" />
                                        <rect x="85" y="75" width="10" height="15" fill="#E53935" />
                                    </svg>
                                </div>
                                <div class="fw-bold fs-6">SiToko 002 &bull; Aloysius Jaison</div>
                                <small class="text-muted">Bisa dibayar dengan BCA Mobile, GoPay, OVO, Dana, ShopeePay, LinkAja</small>
                            </div>
                        </div>

                    {{-- Mode Bayar Virtual Account --}}
                    @else
                        <div class="card border p-3 mb-4 bg-white text-start">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold"><i class="fa-solid fa-building-columns me-1 text-primary"></i> Transfer Virtual Account</span>
                                <span class="badge bg-primary text-uppercase">{{ str_replace('_', ' ', $transaksi->metode_pembayaran) }}</span>
                            </div>

                            <label class="small text-muted mb-1">Nomor Virtual Account:</label>
                            <div class="input-group mb-2">
                                <input type="text" id="vaNumberInput" class="form-control fw-bold fs-5 font-monospace text-primary bg-light" 
                                       value="{{ $transaksi->nomor_va }}" readonly>
                                <button type="button" class="btn btn-outline-primary" onclick="copyVA()">
                                    <i class="fa-solid fa-copy me-1"></i> Salin
                                </button>
                            </div>
                            <small class="text-muted">Nama Rekening: <strong>SiToko 002 - {{ $transaksi->nama_pelanggan }}</strong></small>
                        </div>
                    @endif

                    {{-- Rincian Item Belanja --}}
                    <div class="text-start mb-4">
                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-list-check me-1"></i> Rincian Produk:</h6>
                        <ul class="list-group list-group-flush border-top border-bottom small">
                            @foreach($transaksi->detail as $item)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <div>
                                        <div class="fw-semibold">{{ $item->nama_barang }}</div>
                                        <small class="text-muted">{{ $item->jumlah }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                                    </div>
                                    <span class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Tombol Tindakan / Simulasi Bayar --}}
                    @if($transaksi->status_pembayaran === 'menunggu')
                        <form action="{{ route('toko.bayar.simulasi', $transaksi->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary-sitoko w-100 py-3 fw-bold fs-6 shadow">
                                <i class="fa-solid fa-circle-check me-2"></i> Simulasi Bayar Sekarang (Konfirmasi)
                            </button>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Klik tombol di atas untuk menyimulasikan pembayaran lunas & pemotongan stok otomatis.
                        </small>
                    @else
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('toko.index') }}" class="btn btn-primary-sitoko px-4">
                                <i class="fa-solid fa-bag-shopping me-1"></i> Belanja Lagi
                            </a>
                            <a href="{{ route('toko.pesanan') }}" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-receipt me-1"></i> Lihat Semua Pesanan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyVA() {
        const copyText = document.getElementById("vaNumberInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Nomor Virtual Account berhasil disalin!");
    }
</script>
@endpush
@endsection
