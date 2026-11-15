@extends('layouts.app')

@section('title', $student->full_name . ' (' . $student->student_id . ')')

@section('content')
<div class="mb-4">
    <a href="{{ route('students.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Directory</a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3 shadow-sm" style="width: 60px; height: 60px;">
                {{ substr($student->full_name, 0, 1) }}
            </div>
            <div>
                <h4 class="fw-bold text-dark m-0">{{ $student->full_name }}</h4>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-light text-primary border fw-bold">{{ $student->student_id }}</span>
                    <span class="badge {{ $student->status === 'Active' ? 'badge-active' : 'bg-secondary' }}">{{ $student->status }}</span>
                    <span class="text-muted small">&bull; Admitted on {{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('students.pdf', $student->id) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download Profile PDF
            </a>
            <a href="{{ route('fees.index') }}?student_id={{ $student->id }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-wallet2 me-1"></i> Record Fee Payment
            </a>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-mg-gold btn-sm rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Edit Student
            </a>
        </div>
    </div>
</div>

<!-- QUICK METRICS ROW -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Fee</h6>
                <h3>₹{{ number_format($student->fees->sum('net_amount'), 2) }}</h3>
                <span class="small text-muted">Discount: ₹{{ number_format($student->fees->sum('discount'), 2) }}</span>
            </div>
            <div class="stat-card-icon icon-navy"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Paid Amount</h6>
                <h3 class="text-success">₹{{ number_format($student->total_paid, 2) }}</h3>
                <span class="small text-success fw-semibold"><i class="bi bi-check-circle"></i> Cleared</span>
            </div>
            <div class="stat-card-icon icon-green"><i class="bi bi-check2-all"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Pending Balance</h6>
                <h3 class="{{ $student->total_pending > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($student->total_pending, 2) }}</h3>
                <span class="small text-muted">Remaining fee</span>
            </div>
            <div class="stat-card-icon icon-red"><i class="bi bi-hourglass-split"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Attendance</h6>
                <h3>{{ $student->attendance_percentage }}%</h3>
                <span class="small text-muted">{{ $student->attendances->count() }} Sessions recorded</span>
            </div>
            <div class="stat-card-icon icon-royal"><i class="bi bi-calendar-check"></i></div>
        </div>
    </div>
</div>

<!-- PROFILE TABS -->
<div class="card-custom">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs border-0 px-3 pt-2" id="studentTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab"><i class="bi bi-person me-1"></i> Personal Info</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab"><i class="bi bi-journal-bookmark me-1"></i> Courses ({{ $student->courses->count() }})</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button" role="tab"><i class="bi bi-wallet2 me-1"></i> Fees & Payments</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab"><i class="bi bi-calendar-check me-1"></i> Attendance</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab"><i class="bi bi-graph-up me-1"></i> Progress Reports</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab"><i class="bi bi-folder2-open me-1"></i> Documents ({{ $student->documents->count() }})</button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab"><i class="bi bi-award me-1"></i> Certificates ({{ $student->certificates->count() }})</button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="studentTabsContent">
            
            <!-- 1. PERSONAL INFORMATION TAB -->
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i> Student Details</h6>
                        <table class="table table-sm border-0">
                            <tbody>
                                <tr><td class="text-muted" style="width: 35%;">Student ID:</td><td class="fw-bold text-primary">{{ $student->student_id }}</td></tr>
                                <tr><td class="text-muted">Full Name:</td><td class="fw-semibold">{{ $student->full_name }}</td></tr>
                                <tr><td class="text-muted">Date of Birth:</td><td>{{ $student->dob ? $student->dob->format('d M Y') : 'Not specified' }}</td></tr>
                                <tr><td class="text-muted">Gender:</td><td>{{ $student->gender }}</td></tr>
                                <tr><td class="text-muted">Mobile:</td><td class="fw-semibold">{{ $student->mobile }}</td></tr>
                                <tr><td class="text-muted">Email:</td><td>{{ $student->email ?? 'N/A' }}</td></tr>
                                <tr><td class="text-muted">Alternate Contact:</td><td>{{ $student->alternate_contact ?? 'N/A' }}</td></tr>
                                <tr><td class="text-muted">Address:</td><td>{{ $student->address ?? 'N/A' }}, {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-people-fill text-primary me-2"></i> Parent / Guardian Details</h6>
                        <table class="table table-sm border-0">
                            <tbody>
                                <tr><td class="text-muted" style="width: 35%;">Parent Name:</td><td class="fw-bold text-dark">{{ $student->parent_name }}</td></tr>
                                <tr><td class="text-muted">Relationship:</td><td>{{ $student->parent_relation }}</td></tr>
                                <tr><td class="text-muted">Contact Number:</td><td class="fw-semibold">{{ $student->parent_contact }}</td></tr>
                                <tr><td class="text-muted">Parent Email:</td><td>{{ $student->parent_email ?? 'N/A' }}</td></tr>
                                <tr><td class="text-muted">Assigned Counselor:</td><td>{{ $student->counselor->name ?? 'Unassigned' }}</td></tr>
                                <tr><td class="text-muted">Admission Date:</td><td>{{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</td></tr>
                                <tr><td class="text-muted">Internal Remarks:</td><td>{{ $student->notes ?? 'None' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. COURSES TAB -->
            <div class="tab-pane fade" id="courses" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Enrolled Programs & Batches</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Batch Code / Timing</th>
                                <th>Trainer</th>
                                <th>Enrollment Date</th>
                                <th>Course Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->enrollments as $enr)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $enr->course->name ?? 'Course' }}</div>
                                        <span class="text-xs text-muted">{{ $enr->course->course_code ?? '' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $enr->batch->batch_name ?? 'Batch Unassigned' }}</div>
                                        <div class="text-xs text-muted">{{ $enr->batch->schedule_time ?? '' }}</div>
                                    </td>
                                    <td>{{ $enr->trainer->name ?? 'Lead Master Trainer' }}</td>
                                    <td>{{ $enr->created_at->format('d M Y') }}</td>
                                    <td class="fw-bold">₹{{ number_format($enr->final_payable_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-active">{{ $enr->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-3 text-muted">No course enrollments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. FEES & PAYMENTS TAB -->
            <div class="tab-pane fade" id="fees" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Installment Schedule & Fee History</h6>
                    <a href="{{ route('fees.index') }}?student_id={{ $student->id }}" class="btn btn-sm btn-mg-gold">
                        <i class="bi bi-plus-lg me-1"></i> Record New Payment
                    </a>
                </div>

                <!-- Fee Installments Schedule -->
                <div class="mb-4">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Installment Plan</h6>
                    @foreach($student->fees as $fee)
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Installment #</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fee->installments as $inst)
                                        <tr>
                                            <td>Installment {{ $inst->installment_no }}</td>
                                            <td>{{ $inst->due_date ? $inst->due_date->format('d M Y') : 'N/A' }}</td>
                                            <td class="fw-semibold">₹{{ number_format($inst->amount, 2) }}</td>
                                            <td>₹{{ number_format($inst->paid_amount, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $inst->status === 'Paid' ? 'badge-paid' : ($inst->status === 'Partial' ? 'badge-partial' : 'badge-pending') }}">
                                                    {{ $inst->status }}
                                                </span>
                                            </td>
                                            <td>{{ $inst->paid_date ? $inst->paid_date->format('d M Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>

                <!-- Payment Receipts Table -->
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Payment Receipts Recorded</h6>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Receipt No</th>
                                <th>Payment Date</th>
                                <th>Amount Paid</th>
                                <th>Method</th>
                                <th>Ref / Transaction</th>
                                <th>Recorded By</th>
                                <th class="text-end">Receipt PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->payments as $pm)
                                <tr>
                                    <td><span class="fw-bold text-primary">{{ $pm->receipt_no }}</span></td>
                                    <td>{{ $pm->payment_date ? $pm->payment_date->format('d M Y') : 'N/A' }}</td>
                                    <td class="fw-bold text-success">₹{{ number_format($pm->amount, 2) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $pm->payment_method }}</span></td>
                                    <td>{{ $pm->transaction_reference ?? 'N/A' }}</td>
                                    <td>{{ $pm->recordedBy->name ?? 'System' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('fees.receiptPdf', $pm->id) }}" class="btn btn-sm btn-outline-danger" title="Download Official PDF Receipt">
                                            <i class="bi bi-file-earmark-pdf"></i> Receipt
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-3 text-muted">No payments recorded for this student yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. ATTENDANCE TAB -->
            <div class="tab-pane fade" id="attendance" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Attendance Log & Regularity</h6>
                    <span class="badge bg-success bg-opacity-10 text-success fs-6">{{ $student->attendance_percentage }}% Present</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Trainer Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->attendances()->latest('date')->take(20)->get() as $att)
                                <tr>
                                    <td>{{ $att->date ? $att->date->format('d M Y (D)') : 'N/A' }}</td>
                                    <td>{{ $att->course->name ?? 'Course' }}</td>
                                    <td>
                                        <span class="badge {{ $att->status === 'Present' ? 'badge-active' : ($att->status === 'Late' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $att->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">No attendance marked yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 5. PROGRESS REPORTS TAB -->
            <div class="tab-pane fade" id="progress" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Cognitive & Neuro-Performance Evaluations</h6>
                    <a href="{{ route('progress.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-mg-gold">
                        <i class="bi bi-plus-lg me-1"></i> Add Assessment
                    </a>
                </div>
                <div class="row g-3">
                    @forelse($student->progressRecords as $prog)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-dark m-0">{{ $prog->assessment_type }}</h6>
                                    <span class="badge bg-primary fs-6">{{ $prog->performance_score }}%</span>
                                </div>
                                <div class="text-xs text-muted mb-2">
                                    <i class="bi bi-calendar"></i> Date: {{ $prog->assessment_date ? $prog->assessment_date->format('d M Y') : 'N/A' }} &bull; Trainer: {{ $prog->trainer->name ?? 'Acharya' }}
                                </div>
                                <div class="small text-secondary mb-2">
                                    <strong>Trainer Remarks:</strong> {{ $prog->trainer_remarks }}
                                </div>
                                @if($prog->improvement_notes)
                                    <div class="small text-dark bg-white p-2 rounded border">
                                        <strong class="text-warning"><i class="bi bi-lightning-charge"></i> Action Plan:</strong> {{ $prog->improvement_notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">No performance assessments logged yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- 6. DOCUMENTS TAB -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Uploaded Verification Documents</h6>
                    <button class="btn btn-sm btn-mg-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Document
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Document Title</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Upload Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->documents as $doc)
                                <tr>
                                    <td><i class="bi bi-file-earmark-text text-primary me-2"></i><strong>{{ $doc->document_title }}</strong></td>
                                    <td><span class="badge bg-light text-dark border">{{ $doc->document_type }}</span></td>
                                    <td>{{ round($doc->file_size / 1024, 1) }} KB</td>
                                    <td>{{ $doc->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-light border text-primary" title="View Document">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('students.deleteDocument', [$student->id, $doc->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">No documents uploaded for this student yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 7. CERTIFICATES TAB -->
            <div class="tab-pane fade" id="certificates" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Graduation & Course Completion Certificates</h6>
                    <a href="{{ route('certificates.index') }}" class="btn btn-sm btn-mg-gold">
                        <i class="bi bi-award me-1"></i> Issue Certificate
                    </a>
                </div>
                <div class="row g-3">
                    @forelse($student->certificates as $cert)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-light text-primary border fw-bold">{{ $cert->certificate_number }}</span>
                                        <h5 class="fw-bold text-dark mt-2 mb-1">{{ $cert->course->name ?? 'Course' }}</h5>
                                        <div class="text-xs text-muted">Verification Code: <code>{{ $cert->verification_code }}</code></div>
                                    </div>
                                    <span class="badge badge-active">{{ $cert->status }}</span>
                                </div>
                                <div class="small text-muted mb-3">
                                    Grade: <strong class="text-dark">{{ $cert->grade_score }}</strong> &bull; Completed: {{ $cert->completion_date ? $cert->completion_date->format('d M Y') : 'N/A' }}
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('certificates.download', $cert->id) }}" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                    </a>
                                    <a href="{{ route('certificates.verify', $cert->verification_code) }}" target="_blank" class="btn btn-sm btn-light border w-100">
                                        <i class="bi bi-shield-check me-1"></i> Verify
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">No certificates issued yet.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('students.uploadDocument', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Upload Student Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Document Title <span class="text-danger">*</span></label>
                        <input type="text" name="document_title" class="form-control" required placeholder="e.g. Aadhaar Card / Birth Certificate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document Category</label>
                        <select name="document_type" class="form-select">
                            <option value="Aadhaar Card">Aadhaar Card</option>
                            <option value="Birth Certificate">Birth Certificate</option>
                            <option value="Previous Marksheet">Previous Marksheet</option>
                            <option value="Medical Certificate">Medical Certificate</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Choose File (PDF, Image) <span class="text-danger">*</span></label>
                        <input type="file" name="document_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold">Upload Document</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
