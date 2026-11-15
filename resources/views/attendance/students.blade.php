@extends('layouts.app')

@section('title', 'Student Attendance')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Student Attendance Management</h4>
        <span class="text-muted small">Batch-wise daily attendance marking, regularity tracking, and low-attendance alerts</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('attendance.staff') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-person-badge me-1"></i> Staff Attendance
        </a>
    </div>
</div>

<!-- COURSE & BATCH SELECTOR -->
<div class="card-custom mb-4">
    <div class="card-custom-body py-3">
        <form action="{{ route('attendance.students') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Select Course</label>
                <select name="course_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Choose Course --</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ $selectedCourseId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->course_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Select Batch</label>
                <select name="batch_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Batches</option>
                    @foreach($batches as $b)
                        @if(!$selectedCourseId || $b->course_id == $selectedCourseId)
                            <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                                {{ $b->batch_name }} ({{ $b->schedule_time }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Attendance Date</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-1 d-flex align-items-end pt-4">
                <button type="submit" class="btn btn-mg-primary btn-sm w-100"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
        </form>
    </div>
</div>

@if($selectedCourseId)
    <!-- ATTENDANCE MARKING TABLE -->
    <div class="card-custom">
        <div class="card-custom-header">
            <div>
                <h5 class="card-custom-title"><i class="bi bi-calendar-check text-primary"></i> Attendance Sheet: {{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</h5>
                <span class="text-muted small">Mark attendance for active enrolled students</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" id="btnMarkAllPresent">
                    <i class="bi bi-check-all me-1"></i> Mark All Present
                </button>
            </div>
        </div>

        <form action="{{ route('attendance.studentSave') }}" method="POST">
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
            <input type="hidden" name="batch_id" value="{{ $selectedBatchId }}">
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Attendance History</th>
                            <th>Attendance Status</th>
                            <th>Trainer Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $st)
                            @php
                                $existing = $existingAttendance[$st->id] ?? null;
                                $status = $existing ? $existing->status : 'Present';
                            @endphp
                            <tr>
                                <td><span class="fw-bold text-primary">{{ $st->student_id }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $st->full_name }}</div>
                                    <div class="text-xs text-muted">{{ $st->mobile }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-success" style="width: {{ $st->attendance_percentage }}%;"></div>
                                        </div>
                                        <span class="text-xs fw-semibold {{ $st->attendance_percentage < 75 ? 'text-danger' : 'text-success' }}">{{ $st->attendance_percentage }}%</span>
                                    </div>
                                    @if($st->attendance_percentage < 75)
                                        <span class="badge bg-danger text-white text-xs mt-1"><i class="bi bi-exclamation-triangle"></i> Low Attendance</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check att-radio-present" name="attendance[{{ $st->id }}]" id="pres_{{ $st->id }}" value="Present" {{ $status === 'Present' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-success btn-sm px-3" for="pres_{{ $st->id }}">Present</label>

                                        <input type="radio" class="btn-check att-radio-late" name="attendance[{{ $st->id }}]" id="late_{{ $st->id }}" value="Late" {{ $status === 'Late' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-warning btn-sm px-3" for="late_{{ $st->id }}">Late</label>

                                        <input type="radio" class="btn-check att-radio-absent" name="attendance[{{ $st->id }}]" id="abs_{{ $st->id }}" value="Absent" {{ $status === 'Absent' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-danger btn-sm px-3" for="abs_{{ $st->id }}">Absent</label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="remarks[{{ $st->id }}]" class="form-control form-control-sm" placeholder="Optional notes" value="{{ $existing ? $existing->remarks : '' }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No students enrolled in this course or batch.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty())
                <div class="p-3 border-top bg-light text-end">
                    <button type="submit" class="btn btn-mg-gold px-4">
                        <i class="bi bi-check2-circle me-1"></i> Save Attendance Sheet
                    </button>
                </div>
            @endif
        </form>
    </div>
@else
    <div class="card-custom text-center py-5">
        <i class="bi bi-journal-bookmark text-muted" style="font-size: 3rem;"></i>
        <h5 class="fw-bold text-dark mt-3">Select a Course to Mark Attendance</h5>
        <p class="text-muted small">Choose an active course and date from the selector above to display enrolled students.</p>
    </div>
@endif
@endsection

@push('scripts')
<script>
    $('#btnMarkAllPresent').click(function() {
        $('.att-radio-present').prop('checked', true);
    });
</script>
@endpush
