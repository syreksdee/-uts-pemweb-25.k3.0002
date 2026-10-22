@extends('layouts.app')

@section('title', 'Masuk Akun')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-sitoko shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-danger bg-opacity-10 text-danger mb-2">
                            <i class="fa-solid fa-store fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Masuk ke SiToko</h4>
                        <p class="text-muted small">Toko Online Sederhana 002</p>
                    </div>

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label fw-semibold">NIM / NIK / Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-id-card text-muted"></i></span>
                                <input type="text" name="login" id="login" class="form-control border-start-0" 
                                       placeholder="Contoh: 25.K3.0002 atau email" value="{{ old('login', '25.K3.0002') }}" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0" 
                                       placeholder="Masukkan password" value="password" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                                <label class="form-check-label small" for="remember">Ingat Saya</label>
                            </div>
                            <span class="small text-muted">Default: <code>password</code></span>
                        </div>

                        <button type="submit" class="btn btn-primary-sitoko w-100 py-2 fs-6">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Sekarang
                        </button>
                    </form>

                    <!-- Quick Demo Login Buttons -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="text-center mb-3">
                            <span class="badge bg-light text-dark border px-3 py-1 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-bolt text-warning me-1"></i> Quick Demo Login 1-Klik
                            </span>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('demo.login', 'admin') }}" class="btn btn-outline-danger w-100 text-start py-2 px-2 small">
                                    <div class="fw-bold"><i class="fa-solid fa-user-shield me-1"></i> Admin</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">25.K3.0002</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('demo.login', 'kasir') }}" class="btn btn-outline-success w-100 text-start py-2 px-2 small">
                                    <div class="fw-bold"><i class="fa-solid fa-cash-register me-1"></i> Kasir</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">KASIR-001</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('demo.login', 'gudang') }}" class="btn btn-outline-warning w-100 text-start py-2 px-2 small">
                                    <div class="fw-bold"><i class="fa-solid fa-boxes-stacked me-1"></i> Gudang</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">GDG-001</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('demo.login', 'pelanggan') }}" class="btn btn-outline-primary w-100 text-start py-2 px-2 small">
                                    <div class="fw-bold"><i class="fa-solid fa-user me-1"></i> Pelanggan</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">CUST-001</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
