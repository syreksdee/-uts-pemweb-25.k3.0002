@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-sitoko shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-user-circle text-danger me-2"></i> Pengaturan Profil Pengguna
                    </h5>
                    <span class="badge badge-role badge-role-{{ $user->role->kode_role ?? 'user' }}">
                        {{ $user->role->nama_role ?? 'User' }}
                    </span>
                </div>
                <div class="card-body p-4">
                    {{-- Alert Ketentuan Upload (Requirement 3a) --}}
                    <div class="alert alert-info d-flex align-items-start gap-2 py-2 px-3 mb-4">
                        <i class="fa-solid fa-circle-info fs-5 mt-1"></i>
                        <small>
                            <strong>Ketentuan Foto Profil:</strong> Foto diunggah lewat formulir ini, disimpan di folder <code>public/uploads/profil/</code>, dan path-nya disimpan di database (bukan BLOB). Wajib berformat <strong>JPG / PNG</strong> dengan ukuran maksimal <strong>2 MB</strong>.
                        </small>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-3 text-center mb-3 mb-sm-0">
                                <img src="{{ $user->foto_url }}" alt="Foto Profil" id="profilePreview"
                                     class="rounded-circle shadow-sm border border-3 border-danger-subtle" 
                                     style="width: 120px; height: 120px; object-fit: cover;">
                                <div class="mt-2 text-muted small" style="font-size: 0.75rem;">
                                    Path DB: <br><code>{{ $user->foto ?? 'default' }}</code>
                                </div>
                            </div>
                            <div class="col-sm-9">
                                <label for="foto" class="form-label fw-semibold">Ubah Foto Profil</label>
                                <input type="file" name="foto" id="foto" class="form-control" 
                                       accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                                <div class="form-text text-muted">
                                    Format: <code>.jpg</code>, <code>.jpeg</code>, <code>.png</code>. Maksimal 2 MB.
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIM / NIK / ID</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->nim_nik }}" readonly>
                                <small class="text-muted">ID pengguna permanen</small>
                            </div>

                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label fw-semibold">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" 
                                       class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                       value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="no_hp" class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                                <input type="text" name="no_hp" id="no_hp" 
                                       class="form-control" value="{{ old('no_hp', $user->no_hp) }}" 
                                       placeholder="0812xxxxxxx">
                            </div>

                            <div class="col-12">
                                <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
                                <textarea name="alamat" id="alamat" rows="2" class="form-control" 
                                          placeholder="Alamat pengiriman / domisili">{{ old('alamat', $user->alamat) }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="card bg-light border-0 p-3 mt-2">
                                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-key me-1"></i> Ganti Password (Opsional)</h6>
                                    <p class="text-muted small mb-3">Biarkan kosong jika tidak ingin merubah password.</p>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <input type="password" name="password" class="form-control" placeholder="Password Baru">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Password Baru">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($user->api_token)
                                <div class="col-12">
                                    <label class="form-label fw-semibold">REST API Token (Bearer Token)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light font-monospace small" value="{{ $user->api_token }}" id="apiTokenInput" readonly>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyToken()">
                                            <i class="fa-solid fa-copy"></i> Salin Token
                                        </button>
                                    </div>
                                    <small class="text-muted">Gunakan token ini pada header <code>Authorization: Bearer &lt;token&gt;</code> untuk mengakses REST API SiToko 002.</small>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary-sitoko px-4 py-2">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            // Client side validation check
            if (file.size > 2097152) {
                alert('Peringatan: Ukuran file melebihi 2 MB!');
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    function copyToken() {
        const copyText = document.getElementById("apiTokenInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("API Token berhasil disalin ke clipboard!");
    }
</script>
@endpush
@endsection
