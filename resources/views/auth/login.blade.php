@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Medhaversum Gurukulam" class="auth-logo">
        <h1 class="auth-title">MEDHAVERSUM</h1>
        <div class="auth-subtitle">GURUKULAM MANAGEMENT & CRM SYSTEM</div>
    </div>

    <div class="auth-body">
        @if(session('error'))
            <div class="alert alert-danger p-2 small rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success p-2 small rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ old('email', 'admin@medhaversum.com') }}" required autofocus placeholder="Enter your email">
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Password</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" value="password" required placeholder="Enter password">
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">
                        Remember me
                    </label>
                </div>
                <span class="small text-muted"><i class="bi bi-shield-check text-success"></i> 256-bit Encrypted</span>
            </div>

            <button type="submit" class="btn btn-mg-gold w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Gurukulam Portal
            </button>
        </form>

        <div class="demo-credentials">
            <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                <i class="bi bi-key-fill text-warning"></i> Quick Demo Access:
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Super Admin:</span> <code>admin@medhaversum.com</code>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Counselor:</span> <code>counselor@medhaversum.com</code>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Accountant:</span> <code>accounts@medhaversum.com</code>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Default Password:</span> <code>password</code>
            </div>
        </div>
    </div>

    <div class="auth-footer">
        &copy; {{ date('Y') }} Medhaversum Gurukulam. All rights reserved.
    </div>
</div>
@endsection
