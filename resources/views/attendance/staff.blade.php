@extends('layouts.app')

@section('title', 'Staff Attendance & Leaves')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Staff Attendance & Leave Management</h4>
        <span class="text-muted small">Daily employee check-in/out, biometric status, and leave request authorizations</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-mg-gold btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
            <i class="bi bi-calendar-plus me-1"></i> Apply Staff Leave
        </button>
    </div>
</div>

<!-- DATE SELECTOR -->
<div class="card-custom mb-4">
    <div class="card-custom-body py-3">
        <form action="{{ route('attendance.staff') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                    <input type="date" name="date" class="form-control border-start-0" value="{{ $selectedDate }}" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-3">
                <span class="badge bg-success bg-opacity-10 text-success p-2">
                    <i class="bi bi-check-circle-fill me-1"></i> Present Today: {{ $todayPresent }} / {{ $staffMembers->count() }}
                </span>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Staff Daily Log Table -->
    <div class="col-lg-8">
        <div class="card-custom mb-4">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-person-badge text-primary"></i> Staff Daily Attendance: {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Department</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th class="text-end">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffMembers as $stf)
                            @php
                                $att = $stf->attendances->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $stf->name }}</div>
                                    <div class="text-xs text-muted">{{ $stf->designation }} ({{ $stf->staff_code }})</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $stf->department }}</span></td>
                                <td>{{ $att && $att->check_in ? $att->check_in : '-' }}</td>
                                <td>{{ $att && $att->check_out ? $att->check_out : '-' }}</td>
                                <td>
                                    @if($att)
                                        <span class="badge {{ $att->status === 'Present' ? 'badge-active' : ($att->status === 'Half Day' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ $att->status }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Not Marked</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#updateStaffAttModal{{ $stf->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Update Modal -->
                                    <div class="modal fade text-start" id="updateStaffAttModal{{ $stf->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('attendance.staffCheckIn') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="staff_id" value="{{ $stf->id }}">
                                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Update Staff Attendance: {{ $stf->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="Present" {{ $att && $att->status === 'Present' ? 'selected' : '' }}>Present</option>
                                                                <option value="Half Day" {{ $att && $att->status === 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                                                <option value="Leave" {{ $att && $att->status === 'Leave' ? 'selected' : '' }}>On Leave</option>
                                                                <option value="Absent" {{ $att && $att->status === 'Absent' ? 'selected' : '' }}>Absent</option>
                                                            </select>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label">Check In Time</label>
                                                                <input type="time" name="check_in" class="form-control" value="{{ $att ? $att->check_in : '09:00' }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label">Check Out Time</label>
                                                                <input type="time" name="check_out" class="form-control" value="{{ $att ? $att->check_out : '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Remarks</label>
                                                            <input type="text" name="remarks" class="form-control" value="{{ $att ? $att->remarks : '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-mg-primary">Save Attendance</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No staff records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Staff Leave Applications -->
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-clock-history text-primary"></i> Leave Applications</h5>
            </div>
            <div class="card-custom-body p-3">
                @forelse($leaves as $lv)
                    <div class="p-3 border rounded-3 mb-3 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold text-dark">{{ $lv->staff->name ?? 'Staff' }}</span>
                            <span class="badge {{ $lv->status === 'Approved' ? 'badge-active' : ($lv->status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ $lv->status }}
                            </span>
                        </div>
                        <div class="text-xs text-muted mb-2">
                            {{ $lv->leave_type }} Leave &bull; {{ $lv->total_days }} days ({{ \Carbon\Carbon::parse($lv->start_date)->format('d M') }} to {{ \Carbon\Carbon::parse($lv->end_date)->format('d M') }})
                        </div>
                        <p class="small text-secondary mb-2 fst-italic">"{{ $lv->reason }}"</p>
                        @if($lv->status === 'Pending')
                            <div class="d-flex gap-2 pt-2 border-top">
                                <form action="{{ route('attendance.staffApproveLeave', $lv->id) }}" method="POST" class="w-50">
                                    @csrf
                                    <input type="hidden" name="action" value="Approve">
                                    <button type="submit" class="btn btn-sm btn-success w-100 py-1">Approve</button>
                                </form>
                                <form action="{{ route('attendance.staffApproveLeave', $lv->id) }}" method="POST" class="w-50">
                                    @csrf
                                    <input type="hidden" name="action" value="Reject">
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1">Reject</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">No recent leave requests.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('attendance.staffApplyLeave') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Apply Staff Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff Member <span class="text-danger">*</span></label>
                        <select name="staff_id" class="form-select" required>
                            <option value="">Choose Staff Member</option>
                            @foreach($staffMembers as $stf)
                                <option value="{{ $stf->id }}">{{ $stf->name }} ({{ $stf->designation }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            <option value="Casual">Casual Leave</option>
                            <option value="Sick">Sick Leave</option>
                            <option value="Earned">Earned / Privilege Leave</option>
                            <option value="Unpaid">Unpaid Leave</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Leave <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" required placeholder="Specify reason for leave"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-mg-gold">Submit Leave Application</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
