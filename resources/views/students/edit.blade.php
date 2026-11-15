@extends('layouts.app')

@section('title', 'Edit Student: ' . $student->full_name)

@section('content')
<div class="mb-4">
    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Profile</a>
    <h4 class="fw-bold text-dark m-0">Edit Student Record: {{ $student->full_name }}</h4>
    <span class="text-muted small">Update student information, parent contacts and enrollment status</span>
</div>

<form action="{{ route('students.update', $student->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-custom mb-4">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-person text-primary"></i> Personal Details</h5>
                </div>
                <div class="card-custom-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control bg-light" value="{{ $student->student_id }}" disabled>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $student->full_name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $student->dob ? $student->dob->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" {{ old('gender', $student->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $student->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $student->mobile) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alternate Contact</label>
                            <input type="text" name="alternate_contact" class="form-control" value="{{ old('alternate_contact', $student->alternate_contact) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Residential Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $student->address) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $student->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $student->state) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PIN Code</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $student->pincode) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-people text-primary"></i> Parent / Guardian Details</h5>
                </div>
                <div class="card-custom-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Parent / Guardian Name <span class="text-danger">*</span></label>
                            <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select name="parent_relation" class="form-select" required>
                                <option value="Father" {{ old('parent_relation', $student->parent_relation) === 'Father' ? 'selected' : '' }}>Father</option>
                                <option value="Mother" {{ old('parent_relation', $student->parent_relation) === 'Mother' ? 'selected' : '' }}>Mother</option>
                                <option value="Guardian" {{ old('parent_relation', $student->parent_relation) === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Parent Contact <span class="text-danger">*</span></label>
                            <input type="text" name="parent_contact" class="form-control" value="{{ old('parent_contact', $student->parent_contact) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Email</label>
                            <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email', $student->parent_email) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom mb-4">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-sliders text-primary"></i> Status & Counselor</h5>
                </div>
                <div class="card-custom-body">
                    <div class="mb-3">
                        <label class="form-label">Student Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="Active" {{ old('status', $student->status) === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Completed" {{ old('status', $student->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="On Leave" {{ old('status', $student->status) === 'On Leave' ? 'selected' : '' }}>On Leave</option>
                            <option value="Dropped" {{ old('status', $student->status) === 'Dropped' ? 'selected' : '' }}>Dropped</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assigned Counselor</label>
                        <select name="counselor_id" class="form-select">
                            <option value="">Select Counselor</option>
                            @foreach($counselors as $cn)
                                <option value="{{ $cn->id }}" {{ old('counselor_id', $student->counselor_id) == $cn->id ? 'selected' : '' }}>
                                    {{ $cn->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $student->notes) }}</textarea>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-mg-gold py-2">Save Updates</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
