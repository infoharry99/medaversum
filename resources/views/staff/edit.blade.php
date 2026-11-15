@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Staff Directory</a>
    <h4 class="fw-bold text-dark m-0">Edit Staff Member: {{ $staffMember->name }}</h4>
    <span class="text-muted small">Update profile, salary details or employment status</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('staff.update', $staffMember->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Staff ID</label>
                    <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $staffMember->staff_code }}" disabled>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $staffMember->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department" class="form-select" required>
                        @foreach(['Administration', 'Training', 'Counselling', 'Accounts', 'Support'] as $dept)
                            <option value="{{ $dept }}" {{ old('department', $staffMember->department) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $staffMember->designation) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $staffMember->mobile) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $staffMember->email) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $staffMember->joining_date ? $staffMember->joining_date->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Monthly Gross Salary (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="salary" class="form-control" value="{{ old('salary', $staffMember->salary) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Active" {{ old('status', $staffMember->status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $staffMember->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="On Leave" {{ old('status', $staffMember->status) === 'On Leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $staffMember->address) }}</textarea>
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('staff.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Update Staff Details</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
