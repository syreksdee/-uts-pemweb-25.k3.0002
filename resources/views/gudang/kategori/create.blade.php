@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-sitoko border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-plus-circle text-danger me-2"></i> Tambah Kategori Baru
                    </h5>
                    <a href="{{ route('gudang.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('gudang.kategori.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="nama_kategori" 
                                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori') }}" placeholder="Contoh: Frozen Food & Makanan Dingin" required>
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label fw-semibold">Icon FontAwesome (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-icons text-muted"></i></span>
                                <input type="text" name="icon" id="icon" class="form-control" 
                                       value="{{ old('icon', 'fa-tag') }}" placeholder="fa-tag, fa-coffee, fa-soap">
                            </div>
                            <small class="text-muted">Nama class FontAwesome (misal: fa-coffee, fa-tag, fa-utensils).</small>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi Singkat</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control" 
                                      placeholder="Penjelasan produk dalam kategori ini...">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary-sitoko px-4 py-2">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
