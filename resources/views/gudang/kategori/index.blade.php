@extends('layouts.app')

@section('title', 'Manajemen Kategori Barang')

@section('content')
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fa-solid fa-tags text-danger me-2"></i> Manajemen Kategori Barang (Entitas 2)
            </h4>
            <small class="text-muted">Kelola kelompok kategori untuk pengorganisasian produk toko.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gudang.barang.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-boxes-stacked me-1"></i> Data Barang
            </a>
            <a href="{{ route('gudang.kategori.create') }}" class="btn btn-primary-sitoko btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian Kategori (Parameter Binding) -->
    <div class="card card-sitoko border-0 shadow-sm p-3 mb-4">
        <form action="{{ route('gudang.kategori.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Cari nama kategori atau deskripsi..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-primary-sitoko px-3">Cari</button>
                    @if($search)
                        <a href="{{ route('gudang.kategori.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Kategori -->
    <div class="card card-sitoko border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Icon</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th>Total Barang</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoriList as $index => $k)
                        <tr>
                            <td class="ps-4 text-muted">{{ $kategoriList->firstItem() + $index }}</td>
                            <td>
                                <div class="p-2 bg-light rounded text-center d-inline-block text-danger" style="width: 36px; height: 36px;">
                                    <i class="fa-solid {{ $k->icon ?? 'fa-tag' }}"></i>
                                </div>
                            </td>
                            <td><strong>{{ $k->nama_kategori }}</strong></td>
                            <td><code class="text-muted">{{ $k->slug }}</code></td>
                            <td><small class="text-muted">{{ Str::limit($k->deskripsi, 50) ?: '-' }}</small></td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    {{ $k->barang_count }} barang
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('gudang.kategori.edit', $k->id) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('gudang.kategori.destroy', $k->id) }}" method="POST" 
                                          onsubmit="return confirm('Hapus kategori ini?')" class="d-inline">
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
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination (Requirement 8) -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $kategoriList->withQueryString()->links() }}
    </div>
</div>
@endsection
