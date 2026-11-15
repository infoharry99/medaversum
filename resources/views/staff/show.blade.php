@extends('layouts.app')

@section('title', 'Staff: ' . $staffMember->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('staff.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Staff Directory</a>
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3 shadow-sm" style="width: 60px; height: 60px;">
                {{ substr($staffMember->name, 0, 1) }}
            </div>
            <div>
                <h4 class="fw-bold text-dark m-0">{{ $staffMember->name }}</h4>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-light text-primary border fw-bold">{{ $staffMember->staff_code }}</span>
                    <span class="badge badge-active">{{ $staffMember->status }}</span>
                    <span class="text-muted small">&bull; {{ $staffMember->designation }} &bull; {{ $staffMember->department }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.edit', $staffMember->id) }}" class="btn btn-mg-gold btn-sm rounded-pill px-3">
                <i class="bi bi-pencil me-1"></i> Edit Profile
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-person text-primary"></i> Employment Details</h5>
            </div>
            <div class="card-custom-body p-3">
                <table class="table table-sm border-0 mb-0">
                    <tbody>
                        <tr><td class="text-muted">Staff Code:</td><td class="fw-bold text-primary">{{ $staffMember->staff_code }}</td></tr>
                        <tr><td class="text-muted">Designation:</td><td class="fw-bold text-dark">{{ $staffMember->designation }}</td></tr>
                        <tr><td class="text-muted">Department:</td><td><span class="badge bg-light text-dark border">{{ $staffMember->department }}</span></td></tr>
                        <tr><td class="text-muted">Mobile:</td><td class="fw-semibold">{{ $staffMember->mobile }}</td></tr>
                        <tr><td class="text-muted">Email:</td><td>{{ $staffMember->email }}</td></tr>
                        <tr><td class="text-muted">Monthly Salary:</td><td class="fw-bold text-success">₹{{ number_format($staffMember->salary, 2) }}</td></tr>
                        <tr><td class="text-muted">Joining Date:</td><td>{{ $staffMember->joining_date ? $staffMember->joining_date->format('d M Y') : 'N/A' }}</td></tr>
                        <tr><td class="text-muted">System Role:</td><td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $staffMember->user->role->name ?? 'Standard User' }}</span></td></tr>
                        <tr><td class="text-muted">Address:</td><td class="small">{{ $staffMember->address ?? 'Campus Residence' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Attendance History -->
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-calendar-check text-primary"></i> Attendance History</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMember->attendances()->latest('date')->take(10)->get() as $att)
                            <tr>
                                <td>{{ $att->date ? $att->date->format('d M Y (D)') : 'N/A' }}</td>
                                <td>{{ $att->check_in ?? '-' }}</td>
                                <td>{{ $att->check_out ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $att->status === 'Present' ? 'badge-active' : ($att->status === 'Half Day' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $att->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">No attendance records logged yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Leaves History -->
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-clock-history text-primary"></i> Leave Applications</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Dates</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMember->leaves as $lv)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $lv->leave_type }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($lv->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($lv->end_date)->format('d M Y') }}</td>
                                <td>{{ $lv->total_days }} days</td>
                                <td class="small">{{ $lv->reason }}</td>
                                <td>
                                    <span class="badge {{ $lv->status === 'Approved' ? 'badge-active' : ($lv->status === 'Pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $lv->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">No leave applications on file.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
