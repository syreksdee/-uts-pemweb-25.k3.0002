@extends('layouts.app')

@section('title', 'Ubah Data Barang')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-sitoko border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Ubah Data Barang: {{ $barang->nama_barang }}
                    </h5>
                    <a href="{{ route('gudang.barang.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('gudang.barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                    @foreach($kategoriList as $k)
                                        <option value="{{ $k->id }}" {{ old('kategori_id', $barang->kategori_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="kode_barang" class="form-label fw-semibold">Kode Barang (Barcode / SKU)</label>
                                <input type="text" name="kode_barang" id="kode_barang" 
                                       class="form-control font-monospace" value="{{ old('kode_barang', $barang->kode_barang) }}" required>
                            </div>

                            <div class="col-12">
                                <label for="nama_barang" class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_barang" id="nama_barang" 
                                       class="form-control" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                            </div>

                            <div class="col-12">
                                <label for="deskripsi" class="form-label fw-semibold">Deskripsi Barang</label>
                                <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="harga_beli" class="form-label fw-semibold">Harga Beli (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga_beli" id="harga_beli" min="0" step="100" 
                                       class="form-control" value="{{ old('harga_beli', $barang->harga_beli) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="harga_jual" class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga_jual" id="harga_jual" min="0" step="100" 
                                       class="form-control" value="{{ old('harga_jual', $barang->harga_jual) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="stok" class="form-label fw-semibold">Stok Saat Ini <span class="text-danger">*</span></label>
                                <input type="number" name="stok" id="stok" min="0" 
                                       class="form-control" value="{{ old('stok', $barang->stok) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="satuan" class="form-label fw-semibold">Satuan Kemasan <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" id="satuan" 
                                       class="form-control" value="{{ old('satuan', $barang->satuan) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label fw-semibold">Status Barang <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="aktif" {{ old('status', $barang->status) == 'aktif' ? 'selected' : '' }}>Aktif (Dijual)</option>
                                    <option value="nonaktif" {{ old('status', $barang->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Arsip)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="{{ $barang->gambar_url }}" alt="Thumbnail" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                                    </div>
                                    <div class="col">
                                        <label for="gambar" class="form-label fw-semibold">Ganti Foto Produk (Opsional)</label>
                                        <input type="file" name="gambar" id="gambar" class="form-control" accept=".jpg,.jpeg,.png">
                                        <small class="text-muted">Biarkan kosong jika tidak ingin merubah foto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary-sitoko px-4 py-2">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Data Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
