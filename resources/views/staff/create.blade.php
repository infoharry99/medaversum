@extends('layouts.app')

@section('title', 'Add Staff')

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Staff Directory</a>
    <h4 class="fw-bold text-dark m-0">Add New Staff Member</h4>
    <span class="text-muted small">Register faculty, trainers, counselors or support staff</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('staff.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Staff ID <span class="text-danger">*</span></label>
                    <input type="text" name="staff_code" class="form-control fw-bold text-primary bg-light" value="{{ old('staff_code', $suggestedCode) }}" required readonly>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Staff member's full name">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department" class="form-select" required>
                        <option value="Administration">Administration</option>
                        <option value="Training" selected>Training & Neuro-Coaching</option>
                        <option value="Counselling">Counselling & Admissions</option>
                        <option value="Accounts">Accounts & Finance</option>
                        <option value="Support">Support & Operations</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation') }}" required placeholder="e.g. Master Neuro-Trainer">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required placeholder="+91 XXXXX XXXXX">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="staff@medhaversum.com">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monthly Gross Salary (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="salary" class="form-control" value="{{ old('salary', 50000.00) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="On Leave">On Leave</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" rows="2" class="form-control" placeholder="Address details">{{ old('address') }}</textarea>
                </div>

                <!-- Portal User Account -->
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="create_user_account" id="createUserSwitch" value="1" checked>
                            <label class="form-check-label fw-bold text-dark" for="createUserSwitch">
                                Create System Login Account for this Staff Member
                            </label>
                        </div>
                        <div class="row g-3" id="roleSelectorDiv">
                            <div class="col-md-6">
                                <label class="form-label small">Assign Portal Role</label>
                                <select name="role_id" class="form-select">
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ $r->slug === 'trainer' ? 'selected' : '' }}>
                                            {{ $r->name }} ({{ $r->description }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Default Initial Password</label>
                                <input type="text" class="form-control bg-white" value="password" readonly>
                                <span class="text-xs text-muted">User can update password upon login.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('staff.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Register Staff Member</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
