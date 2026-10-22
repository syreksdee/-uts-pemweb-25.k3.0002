@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-3">
    <!-- Header Admin Dashboard -->
    <div class="card border-0 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #1E232A 0%, #2c333f 100%); border-radius: var(--radius-md);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-danger text-white px-3 py-1 mb-2">
                        <i class="fa-solid fa-user-shield me-1"></i> Modul Administrator (UAdmin)
                    </span>
                    <h3 class="fw-bold mb-1">Halo, {{ Auth::user()->nama_lengkap }}</h3>
                    <p class="text-white-50 small mb-0">NIM: <strong>{{ Auth::user()->nim_nik }}</strong> &bull; Panel kendali laporan transaksi dan manajemen pengguna toko.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.laporan') }}" class="btn btn-primary-sitoko btn-sm">
                        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Buka Laporan Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Pendapatan Lunas -->
        <div class="col-6 col-lg-3">
            <div class="card card-sitoko border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">TOTAL OMZET</small>
                    <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-circle">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-danger mb-0">
                    Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
                </h4>
                <small class="text-muted" style="font-size: 0.72rem;">Status pembayaran lunas</small>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-6 col-lg-3">
            <div class="card card-sitoko border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">TRANSAKSI</small>
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-0">
                    {{ $stats['total_transaksi'] }} <span class="fs-6 fw-normal text-muted">Trx</span>
                </h4>
                <small class="text-success fw-semibold" style="font-size: 0.72rem;">{{ $stats['transaksi_lunas'] }} lunas &bull; {{ $stats['transaksi_menunggu'] }} pending</small>
            </div>
        </div>

        <!-- Item Terjual -->
        <div class="col-6 col-lg-3">
            <div class="card card-sitoko border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">PRODUK TERJUAL</small>
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-0">
                    {{ $stats['total_item_terjual'] }} <span class="fs-6 fw-normal text-muted">Pcs</span>
                </h4>
                <small class="text-muted" style="font-size: 0.72rem;">Dari seluruh penjualan</small>
            </div>
        </div>

        <!-- Total Katalog -->
        <div class="col-6 col-lg-3">
            <div class="card card-sitoko border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-bold">KATALOG BARANG</small>
                    <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-0">
                    {{ $totalBarang }} <span class="fs-6 fw-normal text-muted">Item</span>
                </h4>
                <small class="text-muted" style="font-size: 0.72rem;">Tersedia di etalase</small>
            </div>
        </div>
    </div>

    <!-- Visual Tren Penjualan Harian (Chart / Bar Representation) -->
    <div class="card card-sitoko border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">
                <i class="fa-solid fa-chart-column text-danger me-2"></i> Tren Penjualan Harian SiToko 002
            </h6>
            <small class="text-muted">14 Hari Terakhir</small>
        </div>
        <div class="card-body p-4">
            <div class="row align-items-end g-2" style="height: 180px;">
                @php
                    $maxVal = max(!empty($stats['chart_values']) ? max($stats['chart_values']) : 1, 1);
                @endphp
                @foreach($stats['chart_labels'] as $idx => $label)
                    @php
                        $val = $stats['chart_values'][$idx] ?? 0;
                        $percent = ($val / $maxVal) * 100;
                    @endphp
                    <div class="col text-center d-flex flex-column justify-content-end h-100">
                        <div class="small fw-bold text-danger" style="font-size: 0.65rem;">
                            {{ $val > 0 ? number_format($val / 1000, 0) . 'k' : '' }}
                        </div>
                        <div class="w-100 bg-danger rounded-top opacity-75 my-1" 
                             style="height: {{ max($percent, 8) }}%; transition: height 0.5s ease;" 
                             title="{{ $label }}: Rp {{ number_format($val, 0, ',', '.') }}"></div>
                        <span class="text-muted text-truncate" style="font-size: 0.65rem;">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Shortcut Navigasi Cepat Admin -->
        <div class="col-lg-4">
            <div class="card card-sitoko border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Navigasi Manajemen Admin</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.laporan') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-bold"><i class="fa-solid fa-file-invoice text-danger me-2"></i> Baca Laporan Penjualan</div>
                            <small class="text-muted">Filter harian, mingguan, status, dan metode bayar</small>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('admin.kasir.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-bold"><i class="fa-solid fa-cash-register text-success me-2"></i> Mengatur Akun Kasir</div>
                            <small class="text-muted">Tambah & kontrol akses staf kasir POS</small>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('admin.pelanggan.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-bold"><i class="fa-solid fa-users text-primary me-2"></i> Mengatur Data Pelanggan</div>
                            <small class="text-muted">Data pembeli, kontak & histori pesanan</small>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted small"></i>
                    </a>
                    <a href="{{ route('gudang.barang.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-bold"><i class="fa-solid fa-boxes-stacked text-warning me-2"></i> Inventaris Barang</div>
                            <small class="text-muted">Lihat dan atur stok barang & kategori</small>
                        </div>
                        <i class="fa-solid fa-chevron-right text-muted small"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Transaksi Terbaru (Read/View) -->
        <div class="col-lg-8">
            <div class="card card-sitoko border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Transaksi Terbaru</h6>
                    <a href="{{ route('admin.laporan') }}" class="btn btn-outline-danger btn-sm">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Trx</th>
                                <th>Pelanggan</th>
                                <th>Metode</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $r)
                                <tr>
                                    <td class="ps-3 fw-bold font-monospace small">{{ $r->nomor_transaksi }}</td>
                                    <td>{{ $r->nama_pelanggan }}</td>
                                    <td><span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $r->metode_pembayaran) }}</span></td>
                                    <td class="fw-bold text-danger">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($r->status_pembayaran === 'lunas')
                                            <span class="badge badge-status-lunas">Lunas</span>
                                        @elseif($r->status_pembayaran === 'menunggu')
                                            <span class="badge badge-status-menunggu">Menunggu</span>
                                        @else
                                            <span class="badge badge-status-batal">Batal</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.transaksi.detail', $r->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
