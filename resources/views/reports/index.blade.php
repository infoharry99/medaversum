@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Reports & Business Intelligence</h4>
        <span class="text-muted small">Real-time audit summaries, revenue statements, student cohorts, and attendance logs</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
        <a href="{{ route('reports.exportCsv') }}?type={{ $reportType }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
        </a>
    </div>
</div>

<!-- REPORT SELECTOR TABS -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('reports.index', ['type' => 'financial']) }}" class="btn btn-sm rounded-pill {{ $reportType === 'financial' ? 'btn-mg-primary' : 'btn-light border' }} px-3">
        <i class="bi bi-cash-stack me-1"></i> Financial & Collections
    </a>
    <a href="{{ route('reports.index', ['type' => 'admissions']) }}" class="btn btn-sm rounded-pill {{ $reportType === 'admissions' ? 'btn-mg-primary' : 'btn-light border' }} px-3">
        <i class="bi bi-person-check me-1"></i> Admissions & Cohorts
    </a>
    <a href="{{ route('reports.index', ['type' => 'attendance']) }}" class="btn btn-sm rounded-pill {{ $reportType === 'attendance' ? 'btn-mg-primary' : 'btn-light border' }} px-3">
        <i class="bi bi-calendar-check me-1"></i> Attendance Telemetry
    </a>
    <a href="{{ route('reports.index', ['type' => 'staff']) }}" class="btn btn-sm rounded-pill {{ $reportType === 'staff' ? 'btn-mg-primary' : 'btn-light border' }} px-3">
        <i class="bi bi-people me-1"></i> Staff & Payroll
    </a>
</div>

<!-- FILTER CARD -->
<div class="card-custom mb-4">
    <div class="card-custom-body py-3">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="type" value="{{ $reportType }}">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Course Filter</label>
                <select name="course_id" class="form-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end pt-4">
                <button type="submit" class="btn btn-mg-primary btn-sm w-100">Generate</button>
            </div>
        </form>
    </div>
</div>

@if($reportType === 'financial')
    <!-- FINANCIAL REPORT CONTENT -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>Filtered Period Income</h6>
                    <h3 class="text-success">₹{{ number_format($data['totalCollected'], 2) }}</h3>
                    <span class="small text-muted">{{ $data['payments']->count() }} Transactions</span>
                </div>
                <div class="stat-card-icon icon-green"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>Overall Outstanding</h6>
                    <h3 class="text-danger">₹{{ number_format($data['totalPending'], 2) }}</h3>
                    <span class="small text-muted">Across all enrollments</span>
                </div>
                <div class="stat-card-icon icon-red"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>Primary Payment Mode</h6>
                    <h3>{{ array_key_first($data['methodBreakdown']) ?? 'UPI' }}</h3>
                    <span class="small text-muted">Most utilized channel</span>
                </div>
                <div class="stat-card-icon icon-royal"><i class="bi bi-credit-card"></i></div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-custom-header">
            <h5 class="card-custom-title"><i class="bi bi-receipt text-primary"></i> Collections Ledger ({{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name</th>
                        <th>Payment Date</th>
                        <th>Amount Paid</th>
                        <th>Method</th>
                        <th>Reference / UTR</th>
                        <th>Collected By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['payments'] as $pm)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $pm->receipt_no }}</span></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pm->student->full_name ?? 'N/A' }}</div>
                                <div class="text-xs text-muted">{{ $pm->student->student_id ?? '' }}</div>
                            </td>
                            <td>{{ $pm->payment_date ? $pm->payment_date->format('d M Y') : 'N/A' }}</td>
                            <td class="fw-bold text-success">₹{{ number_format($pm->amount, 2) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $pm->payment_method }}</span></td>
                            <td>{{ $pm->transaction_reference ?? '-' }}</td>
                            <td>{{ $pm->recordedBy->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No transactions found for this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($reportType === 'admissions')
    <!-- ADMISSIONS REPORT CONTENT -->
    <div class="card-custom mb-4">
        <div class="card-custom-header">
            <h5 class="card-custom-title"><i class="bi bi-mortarboard text-primary"></i> Course-wise Admissions in Selected Range</h5>
        </div>
        <div class="card-custom-body">
            <div class="row g-3">
                @foreach($data['courseWiseAdmissions'] as $cw)
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <h6 class="fw-bold text-dark mb-1">{{ $cw->name }}</h6>
                            <h3 class="fw-bold text-primary mb-0">{{ $cw->students_count }} Students</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-custom-header">
            <h5 class="card-custom-title"><i class="bi bi-people text-primary"></i> Admissions Register</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Parent / Contact</th>
                        <th>Admission Date</th>
                        <th>Counselor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['admissions'] as $ad)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $ad->student_id }}</span></td>
                            <td>{{ $ad->full_name }}</td>
                            <td>{{ $ad->courses->first()->name ?? 'N/A' }}</td>
                            <td>{{ $ad->parent_name }} ({{ $ad->mobile }})</td>
                            <td>{{ $ad->admission_date ? $ad->admission_date->format('d M Y') : 'N/A' }}</td>
                            <td>{{ $ad->counselor->name ?? 'Direct Walk-in' }}</td>
                            <td><span class="badge badge-active">{{ $ad->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No admissions registered in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($reportType === 'attendance')
    <!-- ATTENDANCE REPORT CONTENT -->
    <div class="card-custom">
        <div class="card-custom-header">
            <h5 class="card-custom-title"><i class="bi bi-calendar-check text-primary"></i> Attendance Telemetry Log</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Attendance Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['attendanceRecords'] as $att)
                        <tr>
                            <td>{{ $att->date ? $att->date->format('d M Y') : 'N/A' }}</td>
                            <td>{{ $att->student->full_name ?? 'N/A' }}</td>
                            <td>{{ $att->course->name ?? 'Course' }}</td>
                            <td>
                                <span class="badge {{ $att->status === 'Present' ? 'badge-active' : ($att->status === 'Late' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $att->status }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $att->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No student attendance logs in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($reportType === 'staff')
    <!-- STAFF & PAYROLL REPORT CONTENT -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>Total Staff Count</h6>
                    <h3>{{ $data['staffList']->count() }}</h3>
                    <span class="small text-muted">Active employees</span>
                </div>
                <div class="stat-card-icon icon-navy"><i class="bi bi-people"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>Total Monthly Payroll</h6>
                    <h3 class="text-success">₹{{ number_format($data['totalSalary'], 2) }}</h3>
                    <span class="small text-muted">Gross monthly compensation</span>
                </div>
                <div class="stat-card-icon icon-green"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-custom-header">
            <h5 class="card-custom-title"><i class="bi bi-person-badge text-primary"></i> Staff Salary & Department Roster</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Full Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Joining Date</th>
                        <th>Monthly Salary</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['staffList'] as $stf)
                        <tr>
                            <td><span class="fw-bold text-primary">{{ $stf->staff_code }}</span></td>
                            <td>{{ $stf->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $stf->department }}</span></td>
                            <td>{{ $stf->designation }}</td>
                            <td>{{ $stf->joining_date ? $stf->joining_date->format('d M Y') : 'N/A' }}</td>
                            <td class="fw-bold text-dark">₹{{ number_format($stf->salary, 2) }}</td>
                            <td><span class="badge badge-active">{{ $stf->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
