@extends('layouts.app')

@section('title', 'Struk Pembayaran ' . $transaksi->nomor_transaksi)

@section('content')
<div class="container py-4">
    <div class="text-center mb-3 d-print-none">
        <a href="{{ route('kasir.pos') }}" class="btn btn-outline-secondary me-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Transaksi Baru (Kasir)
        </a>
        <button type="button" class="btn btn-primary-sitoko" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Cetak Struk (Print)
        </button>
    </div>

    <!-- Thermal Paper Receipt Design -->
    <div class="receipt-paper shadow-sm">
        <div class="text-center">
            <h5 class="fw-bold mb-0">SiToko 002</h5>
            <small>Toko Online Sederhana 002</small>
            <div style="font-size: 0.72rem;">Jl. Pemuda No. 02, Semarang &bull; 081234567890</div>
        </div>

        <div class="receipt-divider"></div>

        <div style="font-size: 0.8rem;">
            <div class="d-flex justify-content-between">
                <span>No. Trx:</span>
                <span class="fw-bold">{{ $transaksi->nomor_transaksi }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Waktu:</span>
                <span>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Kasir:</span>
                <span>{{ $transaksi->kasir->nama_lengkap ?? 'Kasir SiToko' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Pelanggan:</span>
                <span>{{ $transaksi->nama_pelanggan }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Item List -->
        <table class="w-100" style="font-size: 0.8rem;">
            <tbody>
                @foreach($transaksi->detail as $item)
                    <tr>
                        <td colspan="2" class="fw-bold pt-1">{{ $item->nama_barang }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted pb-1">
                            {{ $item->jumlah }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end pb-1 fw-bold">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-divider"></div>

        <div style="font-size: 0.85rem;">
            <div class="d-flex justify-content-between fw-bold">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-1">
                <span>Metode:</span>
                <span class="text-uppercase">{{ str_replace('_', ' ', $transaksi->metode_pembayaran) }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Bayar:</span>
                <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between fw-bold text-success">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <div class="text-center mt-3" style="font-size: 0.75rem;">
            <div class="fw-bold">*** LUNAS ***</div>
            <div>Terima kasih atas kunjungan Anda!</div>
            <div>Barang yang dibeli tidak dapat ditukar/dikembalikan</div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .receipt-paper, .receipt-paper * {
        visibility: visible;
    }
    .receipt-paper {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
    }
}
</style>
@endsection
