@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">System Settings & Audit Log</h4>
        <span class="text-muted small">Configure Gurukulam institutional parameters, currency prefixes, and review audit trail</span>
    </div>
</div>

<div class="row g-4">
    <!-- SETTINGS FORMS -->
    <div class="col-lg-7">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-buildings text-primary"></i> Gurukulam Identity & Branding</h5>
            </div>
            <div class="card-custom-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 text-center pb-3 border-bottom">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="rounded-3 shadow-sm border p-1" style="width: 100px; height: 100px; object-fit: contain;">
                            <div class="text-xs text-muted mt-2">Official Emblem (stored in <code>public/assets/images/logo.png</code>)</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Institute Name</label>
                            <input type="text" name="institute_name" class="form-control" value="{{ $settings['institute_name'] ?? 'Medhaversum Gurukulam' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tagline / Motto</label>
                            <input type="text" name="institute_tagline" class="form-control" value="{{ $settings['institute_tagline'] ?? 'A Universe of Infinite Potential' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'contact@medhaversum.com' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '+91 98765 43210' }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Campus Physical Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ $settings['address'] ?? 'Mind Science & Neuro-Cognitive Gurukulam Campus, New Delhi, India' }}</textarea>
                        </div>

                        <div class="col-12 border-top pt-3">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 text-primary me-2"></i> Financial & Registration Settings</h6>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? '₹' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Currency Code</label>
                            <input type="text" name="currency_code" class="form-control" value="{{ $settings['currency_code'] ?? 'INR' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Student ID Prefix</label>
                            <input type="text" name="student_id_prefix" class="form-control" value="{{ $settings['student_id_prefix'] ?? 'MG-2026-' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Receipt Prefix</label>
                            <input type="text" name="receipt_prefix" class="form-control" value="{{ $settings['receipt_prefix'] ?? 'REC-2026-' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST / Tax Identification</label>
                            <input type="text" name="gst_number" class="form-control" value="{{ $settings['gst_number'] ?? '07AAAAA0000A1Z5' }}">
                        </div>

                        <div class="col-12 text-end pt-3 border-top mt-3">
                            <button type="submit" class="btn btn-mg-gold px-4">Save Configuration</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SYSTEM AUDIT TRAIL LOG -->
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-shield-check text-primary"></i> System Activity Audit Trail</h5>
                <span class="badge bg-light text-secondary border">Real-time</span>
            </div>
            <div class="card-custom-body p-3" style="max-height: 600px; overflow-y: auto;">
                @forelse($activityLogs as $log)
                    <div class="p-2 border-bottom mb-2 pb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary text-xs">{{ $log->action }} &bull; {{ $log->module }}</span>
                            <span class="text-xs text-muted">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="small text-dark">{{ $log->description }}</div>
                        <div class="text-xs text-muted mt-1">
                            By: <strong>{{ $log->user->name ?? 'System' }}</strong> &bull; IP: <code>{{ $log->ip_address }}</code>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">No activity logs recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
