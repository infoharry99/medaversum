@extends('layouts.app')

@section('title', $course->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Courses</a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark m-0">{{ $course->name }}</h4>
            <span class="badge bg-light text-primary border me-2">{{ $course->course_code }}</span>
            <span class="badge {{ $course->status === 'Active' ? 'badge-active' : 'bg-secondary' }}">{{ $course->status }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('batches.create') }}?course_id={{ $course->id }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-plus-lg me-1"></i> Add Batch
            </a>
            <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-mg-gold rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Edit Course
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Fee</h6>
                <h3>₹{{ number_format($course->total_fees, 2) }}</h3>
                <span class="small text-muted">Admission: ₹{{ number_format($course->admission_fees, 2) }}</span>
            </div>
            <div class="stat-card-icon icon-navy">
                <i class="bi bi-currency-rupee"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Course Revenue</h6>
                <h3>₹{{ number_format($totalRevenue, 2) }}</h3>
                <span class="small text-success">Total collected</span>
            </div>
            <div class="stat-card-icon icon-green">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Duration</h6>
                <h3>{{ $course->duration_weeks }} Wks</h3>
                <span class="small text-muted">{{ $course->installment_count }} Installments</span>
            </div>
            <div class="stat-card-icon icon-royal">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Students Enrolled</h6>
                <h3>{{ $course->students->count() }}</h3>
                <span class="small text-muted">Across {{ $course->batches->count() }} batches</span>
            </div>
            <div class="stat-card-icon icon-gold">
                <i class="bi bi-mortarboard"></i>
            </div>
        </div>
    </div>
</div>

<!-- DETAILS & BATCHES -->
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-info-circle text-primary"></i> Course Details</h5>
            </div>
            <div class="card-custom-body">
                <p class="text-secondary mb-4">{{ $course->description ?? 'No detailed description provided.' }}</p>
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Master Trainer:</span>
                        <span class="fw-bold text-dark">{{ $course->trainer->name ?? 'Unassigned' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Preferred Timing:</span>
                        <span class="fw-semibold">{{ $course->batch_timing ?? 'Flexible' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Installment Scheme:</span>
                        <span class="fw-semibold">{{ $course->installment_count }} parts (Monthly)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-calendar3 text-primary"></i> Active Batches</h5>
                <a href="{{ route('batches.create') }}?course_id={{ $course->id }}" class="btn btn-sm btn-light border">+ Add Batch</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Schedule</th>
                            <th>Timing</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($course->batches as $batch)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $batch->batch_name }}</div>
                                    <span class="text-xs text-muted">{{ $batch->batch_code }}</span>
                                </td>
                                <td>{{ $batch->start_date ? \Carbon\Carbon::parse($batch->start_date)->format('d M Y') : 'N/A' }}</td>
                                <td>{{ $batch->schedule_time }}</td>
                                <td>
                                    <span class="badge {{ $batch->status === 'Ongoing' ? 'badge-active' : 'bg-info' }}">{{ $batch->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No batches created for this course yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-people text-primary"></i> Enrolled Students</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Enrolled Date</th>
                            <th>Fee Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($course->students as $student)
                            <tr>
                                <td>
                                    <a href="{{ route('students.show', $student->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $student->full_name }}
                                    </a>
                                    <div class="text-xs text-muted">{{ $student->student_id }}</div>
                                </td>
                                <td>{{ $student->pivot->created_at ? $student->pivot->created_at->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    @php
                                        $fee = $student->fees->first();
                                    @endphp
                                    @if($fee)
                                        <span class="badge {{ $fee->fee_status === 'Paid' ? 'badge-paid' : ($fee->fee_status === 'Partial' ? 'badge-partial' : 'badge-pending') }}">
                                            {{ $fee->fee_status }} (Paid: ₹{{ number_format($fee->paid_amount, 0) }})
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No fee record</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">No students currently enrolled.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
