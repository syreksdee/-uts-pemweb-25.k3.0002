@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-sitoko border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-plus-circle text-danger me-2"></i> Tambah Barang Baru ke Inventaris
                    </h5>
                    <a href="{{ route('gudang.barang.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('gudang.barang.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kategori_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori...</option>
                                    @foreach($kategoriList as $k)
                                        <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="kode_barang" class="form-label fw-semibold">Kode Barang (Barcode / SKU)</label>
                                <input type="text" name="kode_barang" id="kode_barang" 
                                       class="form-control font-monospace @error('kode_barang') is-invalid @enderror" 
                                       value="{{ old('kode_barang') }}" placeholder="Contoh: BRG-016 (Kosongkan utk auto)">
                                @error('kode_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="nama_barang" class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_barang" id="nama_barang" 
                                       class="form-control @error('nama_barang') is-invalid @enderror" 
                                       value="{{ old('nama_barang') }}" placeholder="Contoh: Susu Kedelai Botol 300ml" required>
                                @error('nama_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="deskripsi" class="form-label fw-semibold">Deskripsi Barang</label>
                                <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control" 
                                          placeholder="Keterangan rincian produk, komposisi, atau manfaat...">{{ old('deskripsi') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="harga_beli" class="form-label fw-semibold">Harga Beli / Pokok (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga_beli" id="harga_beli" min="0" step="100" 
                                       class="form-control @error('harga_beli') is-invalid @enderror" 
                                       value="{{ old('harga_beli', 0) }}" required>
                                @error('harga_beli')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="harga_jual" class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga_jual" id="harga_jual" min="0" step="100" 
                                       class="form-control @error('harga_jual') is-invalid @enderror" 
                                       value="{{ old('harga_jual', 0) }}" required>
                                @error('harga_jual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="stok" class="form-label fw-semibold">Jumlah Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stok" id="stok" min="0" 
                                       class="form-control @error('stok') is-invalid @enderror" 
                                       value="{{ old('stok', 10) }}" required>
                                @error('stok')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="satuan" class="form-label fw-semibold">Satuan Kemasan <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" id="satuan" 
                                       class="form-control" value="{{ old('satuan', 'pcs') }}" placeholder="pcs, botol, pack" required>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label fw-semibold">Status Barang <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif (Dijual)</option>
                                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Arsip)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="gambar" class="form-label fw-semibold">Foto Produk (Opsional)</label>
                                <input type="file" name="gambar" id="gambar" class="form-control" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Format: JPG/PNG, ukuran maks. 2 MB. Disimpan di folder <code>public/uploads/barang/</code>.</small>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary-sitoko px-4 py-2">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
