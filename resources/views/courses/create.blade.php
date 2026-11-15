@extends('layouts.app')

@section('title', 'Add Course')

@section('content')
<div class="mb-4">
    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Courses</a>
    <h4 class="fw-bold text-dark m-0">Add New Gurukulam Course</h4>
    <span class="text-muted small">Define curriculum, fee structure and installment guidelines</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('courses.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Course Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Subconscious Mind Activation">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course Code <span class="text-danger">*</span></label>
                    <input type="text" name="course_code" class="form-control text-uppercase" value="{{ old('course_code') }}" required placeholder="e.g. CRS-SMA">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration (Weeks) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_weeks" class="form-control" value="{{ old('duration_weeks', 12) }}" required min="1">
                </div>

                <div class="col-12">
                    <label class="form-label">Course Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Outline cognitive objectives, meditation protocols, mental frameworks...">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Course Fee (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="total_fees" class="form-control" value="{{ old('total_fees', 25000.00) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Admission Fee Component (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="admission_fees" class="form-control" value="{{ old('admission_fees', 5000.00) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Default Installment Count <span class="text-danger">*</span></label>
                    <input type="number" name="installment_count" class="form-control" value="{{ old('installment_count', 3) }}" required min="1" max="12">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Standard Batch Timing</label>
                    <input type="text" name="batch_timing" class="form-control" value="{{ old('batch_timing') }}" placeholder="e.g. 09:00 AM - 11:00 AM">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assigned Master Trainer</label>
                    <select name="trainer_id" class="form-select">
                        <option value="">Select Trainer (Optional)</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('courses.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Create Course</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
