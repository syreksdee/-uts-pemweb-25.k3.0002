@extends('layouts.app')

@section('title', 'Ubah Kategori')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-sitoko border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Ubah Kategori: {{ $kategori->nama_kategori }}
                    </h5>
                    <a href="{{ route('gudang.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('gudang.kategori.update', $kategori->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="nama_kategori" 
                                   class="form-control @error('nama_kategori') is-invalid @enderror" 
                                   value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                            @error('nama_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label fw-semibold">Icon FontAwesome</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid {{ $kategori->icon ?? 'fa-tag' }}"></i></span>
                                <input type="text" name="icon" id="icon" class="form-control" 
                                       value="{{ old('icon', $kategori->icon) }}" placeholder="fa-tag">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi Singkat</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary-sitoko px-4 py-2">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Perbarui Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
