@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h4 class="fw-bold text-dark m-0">My Account Profile</h4>
        <span class="text-muted small">Manage your account information and login security</span>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card-custom text-center p-4">
            <div class="mx-auto rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm mb-3" style="width: 80px; height: 80px;">
                {{ substr($user->name, 0, 1) }}
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
            <div class="badge bg-primary bg-opacity-10 text-primary mb-3">{{ $user->role->name ?? 'User' }}</div>
            <p class="small text-muted mb-3">{{ $user->email }}</p>

            <div class="border-top pt-3 text-start small">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Account Status:</span>
                    <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Phone:</span>
                    <span class="fw-semibold">{{ $user->phone ?? 'Not set' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Last Login:</span>
                    <span class="fw-semibold">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-person-gear text-primary"></i> Personal Details</h5>
            </div>
            <div class="card-custom-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->role->name ?? 'No Role' }}" disabled>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-mg-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-shield-lock text-primary"></i> Change Password</h5>
            </div>
            <div class="card-custom-body">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat new password">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-mg-gold">Update Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
