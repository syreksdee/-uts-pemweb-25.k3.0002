@extends('layouts.app')

@section('title', 'Manajemen Barang (Gudang)')

@section('content')
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fa-solid fa-boxes-stacked text-warning me-2"></i> Inventaris Data Barang (UGudang)
            </h4>
            <small class="text-muted">Kelola data produk, harga jual/beli, dan stok inventaris toko.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gudang.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-tags me-1"></i> Kelola Kategori
            </a>
            <a href="{{ route('gudang.barang.create') }}" class="btn btn-primary-sitoko btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Barang Baru
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian (Parameter Binding) -->
    <div class="card card-sitoko border-0 shadow-sm p-3 mb-4">
        <form action="{{ route('gudang.barang.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Cari kode atau nama barang..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="kategori_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ ($kategoriId == $k->id) ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-sitoko flex-grow-1">Filter</button>
                @if($search || $kategoriId)
                    <a href="{{ route('gudang.barang.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Barang -->
    <div class="card card-sitoko border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangList as $index => $b)
                        <tr>
                            <td class="ps-4 text-muted">{{ $barangList->firstItem() + $index }}</td>
                            <td>
                                <img src="{{ $b->gambar_url }}" alt="{{ $b->nama_barang }}" 
                                     class="rounded border" style="width: 44px; height: 44px; object-fit: cover;">
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $b->kode_barang }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $b->nama_barang }}</div>
                                <small class="text-muted">{{ Str::limit($b->deskripsi, 40) }}</small>
                            </td>
                            <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $b->kategori->nama_kategori ?? '-' }}</span></td>
                            <td class="text-muted small">Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                            <td class="fw-bold text-danger">Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                            <td>
                                @if($b->stok > 10)
                                    <span class="badge badge-stock badge-stock-in">{{ $b->stok }} {{ $b->satuan }}</span>
                                @elseif($b->stok > 0)
                                    <span class="badge bg-warning text-dark">{{ $b->stok }} {{ $b->satuan }} (Menipis)</span>
                                @else
                                    <span class="badge badge-stock badge-stock-out">Habis</span>
                                @endif
                            </td>
                            <td>
                                @if($b->status === 'aktif')
                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('gudang.barang.edit', $b->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('gudang.barang.destroy', $b->id) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus/menonaktifkan barang ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Tidak ada data barang yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination (Requirement 8) -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $barangList->withQueryString()->links() }}
    </div>
</div>
@endsection
