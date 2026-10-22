@extends('layouts.app')

@section('title', 'Manajemen Kasir')

@section('content')
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="fa-solid fa-cash-register text-success me-2"></i> Manajemen Akun Kasir (Admin)
            </h4>
            <small class="text-muted">Kelola akun staf kasir yang bertugas di modul kasir POS.</small>
        </div>
        <button type="button" class="btn btn-primary-sitoko btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddKasir">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Kasir Baru
        </button>
    </div>

    <!-- Filter Form -->
    <div class="card card-sitoko border-0 shadow-sm p-3 mb-4">
        <form action="{{ route('admin.kasir.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Cari NIK/NIM atau nama kasir..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-primary-sitoko px-3">Cari</button>
                    @if($search)
                        <a href="{{ route('admin.kasir.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Kasir -->
    <div class="card card-sitoko border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Foto</th>
                        <th>NIM / NIK Kasir</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kasirList as $idx => $kasir)
                        <tr>
                            <td class="ps-4 text-muted">{{ $kasirList->firstItem() + $idx }}</td>
                            <td>
                                <img src="{{ $kasir->foto_url }}" alt="Foto" 
                                     class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $kasir->nim_nik }}</span></td>
                            <td><div class="fw-bold">{{ $kasir->nama_lengkap }}</div></td>
                            <td><small class="text-muted">{{ $kasir->email }}</small></td>
                            <td><small>{{ $kasir->no_hp ?: '-' }}</small></td>
                            <td><small class="text-muted">{{ Str::limit($kasir->alamat, 30) ?: '-' }}</small></td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.kasir.destroy', $kasir->id) }}" method="POST" 
                                      onsubmit="return confirm('Hapus akun kasir {{ $kasir->nama_lengkap }}?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada akun kasir terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $kasirList->withQueryString()->links() }}
    </div>
</div>

<!-- Modal Tambah Kasir -->
<div class="modal fade" id="modalAddKasir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kasir.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Akun Kasir Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ID / NIK Kasir <span class="text-danger">*</span></label>
                        <input type="text" name="nim_nik" class="form-control" placeholder="Contoh: KASIR-003" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Kasir <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap petugas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="kasir@sitoko.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Profil (Opsional, JPG/PNG &lt;= 2MB)</label>
                        <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-sitoko">Simpan Kasir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
