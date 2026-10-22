@extends('layouts.app')

@section('title', 'Laporan Penjualan Toko')

@section('content')
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fa-solid fa-file-invoice-dollar text-danger me-2"></i> Laporan Rekap Penjualan (UAdmin)
            </h4>
            <small class="text-muted">Modul baca/tinjau data penjualan toko dan riwayat transaksi.</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Cetak Laporan
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary-sitoko btn-sm">
                <i class="fa-solid fa-gauge me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Laporan Form (Prepared Statement Protected) -->
    <div class="card card-sitoko border-0 shadow-sm p-3 mb-4 d-print-none">
        <form action="{{ route('admin.laporan') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="lunas" {{ ($status === 'lunas') ? 'selected' : '' }}>Lunas</option>
                    <option value="menunggu" {{ ($status === 'menunggu') ? 'selected' : '' }}>Menunggu</option>
                    <option value="batal" {{ ($status === 'batal') ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="No. Trx, Pelanggan..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary-sitoko w-100 py-2">Filter</button>
            </div>
        </form>
    </div>

    <!-- Ringkasan Filter -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white border-0 p-3 rounded-3 shadow-sm">
                <small class="text-white-50 fw-bold">TOTAL OMZET (LUNAS)</small>
                <h4 class="fw-bold mb-0">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white border-0 p-3 rounded-3 shadow-sm">
                <small class="text-white-50 fw-bold">TOTAL TRANSAKSI</small>
                <h4 class="fw-bold mb-0">{{ $stats['total_transaksi'] }} Transaksi</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white border-0 p-3 rounded-3 shadow-sm">
                <small class="text-white-50 fw-bold">TOTAL ITEM TERJUAL</small>
                <h4 class="fw-bold mb-0">{{ $stats['total_item_terjual'] }} Item</h4>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan Penjualan -->
    <div class="card card-sitoko border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>No. Transaksi</th>
                        <th>Waktu</th>
                        <th>Pelanggan</th>
                        <th>Kasir / Petugas</th>
                        <th>Metode Bayar</th>
                        <th>Total Belanja</th>
                        <th>Status</th>
                        <th class="text-end pe-4 d-print-none">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $index => $trx)
                        <tr>
                            <td class="ps-4 text-muted">{{ $laporan->firstItem() + $index }}</td>
                            <td class="fw-bold font-monospace">{{ $trx->nomor_transaksi }}</td>
                            <td><small class="text-muted">{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }}</small></td>
                            <td>{{ $trx->nama_pelanggan }}</td>
                            <td><small class="text-muted">{{ $trx->kasir->nama_lengkap ?? 'Checkout Mandiri (Web)' }}</small></td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $trx->metode_pembayaran) }}</span></td>
                            <td class="fw-bold text-danger">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->status_pembayaran === 'lunas')
                                    <span class="badge badge-status-lunas">LUNAS</span>
                                @elseif($trx->status_pembayaran === 'menunggu')
                                    <span class="badge badge-status-menunggu">MENUNGGU</span>
                                @else
                                    <span class="badge badge-status-batal">BATAL</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 d-print-none">
                                <a href="{{ route('admin.transaksi.detail', $trx->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-eye"></i> Rincian
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Tidak ada transaksi yang cocok dengan filter laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination (Requirement 8) -->
    <div class="mt-4 d-flex justify-content-center d-print-none">
        {{ $laporan->withQueryString()->links() }}
    </div>
</div>
@endsection
