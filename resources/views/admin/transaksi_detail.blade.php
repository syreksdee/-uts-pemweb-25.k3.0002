@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $transaksi->nomor_transaksi)

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            <i class="fa-solid fa-receipt text-danger me-2"></i> Rincian Transaksi: {{ $transaksi->nomor_transaksi }}
        </h4>
        <div>
            <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Laporan
            </a>
            <a href="{{ route('kasir.struk', $transaksi->id) }}" class="btn btn-primary-sitoko btn-sm">
                <i class="fa-solid fa-print me-1"></i> Lihat Format Struk
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informasi Transaksi -->
        <div class="col-md-4">
            <div class="card card-sitoko border-0 shadow-sm p-3 mb-3">
                <h6 class="fw-bold border-bottom pb-2 mb-3">Ringkasan Transaksi</h6>
                <div class="small mb-2">
                    <span class="text-muted d-block">Nomor Transaksi:</span>
                    <strong class="font-monospace">{{ $transaksi->nomor_transaksi }}</strong>
                </div>
                <div class="small mb-2">
                    <span class="text-muted d-block">Waktu Transaksi:</span>
                    <span>{{ $transaksi->tanggal_transaksi->format('d F Y, H:i:s') }}</span>
                </div>
                <div class="small mb-2">
                    <span class="text-muted d-block">Pelanggan:</span>
                    <span>{{ $transaksi->nama_pelanggan }}</span>
                </div>
                <div class="small mb-2">
                    <span class="text-muted d-block">Petugas Kasir:</span>
                    <span>{{ $transaksi->kasir->nama_lengkap ?? 'Checkout Mandiri Online' }}</span>
                </div>
                <div class="small mb-2">
                    <span class="text-muted d-block">Metode Pembayaran:</span>
                    <span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $transaksi->metode_pembayaran) }}</span>
                </div>
                @if($transaksi->nomor_va)
                    <div class="small mb-2">
                        <span class="text-muted d-block">Nomor Virtual Account:</span>
                        <code class="fw-bold">{{ $transaksi->nomor_va }}</code>
                    </div>
                @endif
                <div class="small mb-3">
                    <span class="text-muted d-block">Status Pembayaran:</span>
                    @if($transaksi->status_pembayaran === 'lunas')
                        <span class="badge badge-status-lunas">LUNAS</span>
                    @elseif($transaksi->status_pembayaran === 'menunggu')
                        <span class="badge badge-status-menunggu">MENUNGGU PEMBAYARAN</span>
                    @else
                        <span class="badge badge-status-batal">BATAL</span>
                    @endif
                </div>
                <div class="p-2 bg-light rounded">
                    <small class="text-muted d-block">Catatan:</small>
                    <small>{{ $transaksi->catatan ?: '-' }}</small>
                </div>
            </div>
        </div>

        <!-- Tabel Item Pembelian -->
        <div class="col-md-8">
            <div class="card card-sitoko border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Daftar Produk yang Dibeli</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No.</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi->detail as $idx => $d)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $d->nama_barang }}</div>
                                        <small class="text-muted font-monospace">{{ $d->barang->kode_barang ?? '-' }}</small>
                                    </td>
                                    <td class="text-center fw-bold">{{ $d->jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 fw-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end fw-bold">TOTAL TAGIHAN:</th>
                                <th class="text-end pe-3 fs-5 text-danger fw-bold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end small text-muted">Nominal Dibayar:</th>
                                <th class="text-end pe-3 small text-muted">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end small text-muted">Kembalian:</th>
                                <th class="text-end pe-3 small text-success fw-bold">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
