@extends('layouts.app')

@section('title', 'New Student Admission')

@section('content')
<div class="mb-4">
    <a href="{{ route('students.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Directory</a>
    <h4 class="fw-bold text-dark m-0">Student Registration & Admission</h4>
    <span class="text-muted small">Complete enrollment, batch assignment and initial fee processing</span>
</div>

<form action="{{ route('students.store') }}" method="POST">
    @csrf
    @if(!empty($prefill['enquiry_id']))
        <input type="hidden" name="enquiry_id" value="{{ $prefill['enquiry_id'] }}">
        <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>Converting from Enquiry #{{ $prefill['enquiry_id'] }}. Fields have been pre-filled automatically!</div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Student Information Card -->
        <div class="col-lg-8">
            <div class="card-custom mb-4">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-person text-primary"></i> 1. Student Personal Details</h5>
                </div>
                <div class="card-custom-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Student ID <span class="text-danger">*</span></label>
                            <input type="text" name="student_id" class="form-control fw-bold text-primary bg-light" value="{{ old('student_id', $suggestedStudentId) }}" required readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $prefill['full_name'] ?? '') }}" required placeholder="Student's complete name">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $prefill['mobile'] ?? '') }}" required placeholder="+91 XXXXX XXXXX">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $prefill['email'] ?? '') }}" placeholder="student@email.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alternate Contact / WhatsApp</label>
                            <input type="text" name="alternate_contact" class="form-control" value="{{ old('alternate_contact') }}" placeholder="Alternative phone">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Residential Address</label>
                            <textarea name="address" rows="2" class="form-control" placeholder="House no, street, locality">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', 'New Delhi') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', 'Delhi') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PIN Code</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}" placeholder="e.g. 110001">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent Information Card -->
            <div class="card-custom">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-people text-primary"></i> 2. Parent / Guardian Details</h5>
                </div>
                <div class="card-custom-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Parent / Guardian Name <span class="text-danger">*</span></label>
                            <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $prefill['parent_name'] ?? '') }}" required placeholder="Primary guardian name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <select name="parent_relation" class="form-select" required>
                                <option value="Father" {{ old('parent_relation') === 'Father' ? 'selected' : '' }}>Father</option>
                                <option value="Mother" {{ old('parent_relation') === 'Mother' ? 'selected' : '' }}>Mother</option>
                                <option value="Guardian" {{ old('parent_relation') === 'Guardian' ? 'selected' : '' }}>Guardian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Parent Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="parent_contact" class="form-control" value="{{ old('parent_contact', $prefill['mobile'] ?? '') }}" required placeholder="Phone number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Email Address</label>
                            <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email', $prefill['email'] ?? '') }}" placeholder="parent@email.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic & Financial Setup Column -->
        <div class="col-lg-4">
            <!-- Academic Enrollment Card -->
            <div class="card-custom mb-4">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-journal-bookmark text-primary"></i> 3. Enrollment</h5>
                </div>
                <div class="card-custom-body">
                    <div class="mb-3">
                        <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                        <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="courseSelect" class="form-select" required>
                            <option value="">Choose Course</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" data-fee="{{ $c->total_fees }}" data-admission="{{ $c->admission_fees }}" data-installments="{{ $c->installment_count }}" {{ (old('course_id') == $c->id || ($prefill['course_id'] ?? null) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }} (₹{{ number_format($c->total_fees, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Batch</label>
                        <select name="batch_id" class="form-select">
                            <option value="">Select Batch (Optional)</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->batch_name }} ({{ $b->course->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assigned Counselor</label>
                        <select name="counselor_id" class="form-select">
                            <option value="">Choose Counselor</option>
                            @foreach($counselors as $cn)
                                <option value="{{ $cn->id }}" {{ (old('counselor_id') == $cn->id || ($prefill['counselor_id'] ?? null) == $cn->id) ? 'selected' : '' }}>
                                    {{ $cn->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assigned Trainer</label>
                        <select name="trainer_id" class="form-select">
                            <option value="">Choose Trainer</option>
                            @foreach($trainers as $tr)
                                <option value="{{ $tr->id }}" {{ old('trainer_id') == $tr->id ? 'selected' : '' }}>
                                    {{ $tr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fee & Payment Card -->
            <div class="card-custom mb-4">
                <div class="card-custom-header">
                    <h5 class="card-custom-title"><i class="bi bi-wallet2 text-primary"></i> 4. Fee & Payment</h5>
                </div>
                <div class="card-custom-body">
                    <div class="mb-3">
                        <label class="form-label">Special Discount (₹)</label>
                        <input type="number" step="0.01" name="discount" id="discountInput" class="form-control" value="{{ old('discount', 0) }}" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Initial Payment / Admission Fee (₹)</label>
                        <input type="number" step="0.01" name="initial_payment" id="initialPayInput" class="form-control fw-bold text-success" value="{{ old('initial_payment', 5000) }}" min="0">
                        <span class="text-xs text-muted" style="font-size: 0.75rem;">Receipt will be generated automatically.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="UPI">UPI / QR Code</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer (NEFT/IMPS)</option>
                            <option value="Card">Debit / Credit Card</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control" value="{{ old('transaction_reference') }}" placeholder="UPI Ref / Cheque no / UTR">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admission Remarks / Notes</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Any special notes">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-mg-gold btn-lg py-2">
                            <i class="bi bi-check2-circle me-1"></i> Register & Enroll Student
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
