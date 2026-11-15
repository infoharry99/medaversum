@extends('layouts.app')

@section('title', 'Add Enquiry')

@section('content')
<div class="mb-4">
    <a href="{{ route('enquiries.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Enquiries</a>
    <h4 class="fw-bold text-dark m-0">Record New Student Enquiry</h4>
    <span class="text-muted small">Capture lead details, course interest, source and counselor assignment</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('enquiries.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Enquiry ID <span class="text-danger">*</span></label>
                    <input type="text" name="enquiry_code" class="form-control fw-bold text-primary bg-light" value="{{ old('enquiry_code', $suggestedCode) }}" required readonly>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Student Name <span class="text-danger">*</span></label>
                    <input type="text" name="student_name" class="form-control" value="{{ old('student_name') }}" required placeholder="Candidate's full name">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Parent / Guardian Name</label>
                    <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}" placeholder="Guardian's name">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required placeholder="+91 XXXXX XXXXX">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@address.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Interested Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">General Counselling / Not Decided</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Enquiry Date <span class="text-danger">*</span></label>
                    <input type="date" name="enquiry_date" class="form-control" value="{{ old('enquiry_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lead Source <span class="text-danger">*</span></label>
                    <select name="source" class="form-select" required>
                        <option value="Walk-in" {{ old('source') === 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                        <option value="Website" {{ old('source') === 'Website' ? 'selected' : '' }}>Website</option>
                        <option value="Referral" {{ old('source') === 'Referral' ? 'selected' : '' }}>Referral</option>
                        <option value="Social Media" {{ old('source') === 'Social Media' ? 'selected' : '' }}>Social Media</option>
                        <option value="Phone" {{ old('source') === 'Phone' ? 'selected' : '' }}>Phone</option>
                        <option value="Event" {{ old('source') === 'Event' ? 'selected' : '' }}>Event / Workshop</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Assigned Counselor</label>
                    <select name="counselor_id" class="form-select">
                        <option value="">Select Counselor</option>
                        @foreach($counselors as $cn)
                            <option value="{{ $cn->id }}" {{ old('counselor_id') == $cn->id ? 'selected' : '' }}>{{ $cn->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Initial Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="New" {{ old('status') === 'New' ? 'selected' : '' }}>New</option>
                        <option value="Contacted" {{ old('status') === 'Contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="Follow-up" {{ old('status') === 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="Interested" {{ old('status') === 'Interested' ? 'selected' : '' }}>Interested</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Scheduled Next Follow-up</label>
                    <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Initial Remarks / Queries</label>
                    <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Any questions or specific student challenges discussed">
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('enquiries.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Save Enquiry</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
