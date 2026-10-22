@extends('layouts.app')

@section('title', 'Riwayat Transaksi Kasir')

@section('content')
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> Riwayat Transaksi Penjualan
            </h4>
            <small class="text-muted">Daftar rekaman transaksi penjualan kasir POS</small>
        </div>
        <a href="{{ route('kasir.pos') }}" class="btn btn-primary-sitoko btn-sm">
            <i class="fa-solid fa-cash-register me-1"></i> Buka Mesin Kasir
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card card-sitoko border-0 shadow-sm p-3 mb-4">
        <form action="{{ route('kasir.riwayat') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Cari nomor transaksi, nama pelanggan..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-primary-sitoko px-3">Cari</button>
                    @if($search)
                        <a href="{{ route('kasir.riwayat') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Transaksi -->
    <div class="card card-sitoko border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Transaksi</th>
                        <th>Waktu</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiList as $trx)
                        <tr>
                            <td class="ps-4 fw-bold font-monospace">{{ $trx->nomor_transaksi }}</td>
                            <td><small class="text-muted">{{ $trx->tanggal_transaksi->format('d/m/Y H:i') }}</small></td>
                            <td>{{ $trx->nama_pelanggan }}</td>
                            <td><small class="text-muted">{{ $trx->kasir->nama_lengkap ?? 'Online' }}</small></td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ str_replace('_', ' ', $trx->metode_pembayaran) }}</span></td>
                            <td class="fw-bold text-danger">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->status_pembayaran === 'lunas')
                                    <span class="badge badge-status-lunas">Lunas</span>
                                @elseif($trx->status_pembayaran === 'menunggu')
                                    <span class="badge badge-status-menunggu">Menunggu</span>
                                @else
                                    <span class="badge badge-status-batal">Batal</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('kasir.struk', $trx->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-receipt me-1"></i> Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $transaksiList->withQueryString()->links() }}
    </div>
</div>
@endsection
