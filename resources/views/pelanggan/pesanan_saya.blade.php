@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> Riwayat Pesanan Saya
        </h4>
        <a href="{{ route('toko.index') }}" class="btn btn-outline-sitoko btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Belanja Baru
        </a>
    </div>

    @if($pesananList->isEmpty())
        <div class="card card-sitoko text-center py-5 border-0">
            <i class="fa-solid fa-receipt display-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Belum Ada Pesanan</h5>
            <p class="text-muted">Anda belum memiliki riwayat transaksi di SiToko 002.</p>
            <div>
                <a href="{{ route('toko.index') }}" class="btn btn-primary-sitoko btn-sm">Belanja Sekarang</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($pesananList as $trx)
                <div class="col-12">
                    <div class="card card-sitoko border-0 shadow-sm">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3 gap-2">
                                <div>
                                    <span class="fw-bold text-dark me-2">{{ $trx->nomor_transaksi }}</span>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $trx->tanggal_transaksi->format('d M Y, H:i') }}</small>
                                </div>
                                <div>
                                    @if($trx->status_pembayaran === 'lunas')
                                        <span class="badge badge-status-lunas px-3 py-1">LUNAS</span>
                                    @elseif($trx->status_pembayaran === 'menunggu')
                                        <span class="badge badge-status-menunggu px-3 py-1">MENUNGGU PEMBAYARAN</span>
                                    @else
                                        <span class="badge badge-status-batal px-3 py-1">BATAL</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-7 mb-2 mb-md-0">
                                    <div class="small">
                                        <strong>Metode Bayar:</strong> <span class="text-uppercase">{{ str_replace('_', ' ', $trx->metode_pembayaran) }}</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        @foreach($trx->detail as $item)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $item->nama_barang }} (x{{ $item->jumlah }})
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-5 text-md-end">
                                    <small class="text-muted d-block">Total Pembayaran</small>
                                    <h5 class="fw-bold text-danger mb-2">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</h5>
                                    
                                    <a href="{{ route('toko.bayar', $trx->id) }}" class="btn {{ $trx->status_pembayaran === 'lunas' ? 'btn-outline-secondary' : 'btn-primary-sitoko' }} btn-sm px-3">
                                        <i class="fa-solid fa-eye me-1"></i> {{ $trx->status_pembayaran === 'lunas' ? 'Lihat Bukti Bayar' : 'Bayar Sekarang' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $pesananList->links() }}
        </div>
    @endif
</div>
@endsection
