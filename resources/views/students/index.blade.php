@extends('layouts.app')

@section('title', 'Student Master')

@section('content')
<!-- TOP FILTER BAR MATCHING REFERENCE UI (gopaldudhdairy.physiopii.in/farmers) -->
<div class="card-custom mb-3">
    <div class="p-3">
        <form action="{{ route('students.index') }}" method="GET" id="studentFilterForm">
            <!-- Filter Fields Grid -->
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="filter-label">SEARCH STUDENT</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-control border-start-0 ps-1" 
                               placeholder="Search by name, student code, phone, city..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">COURSE</label>
                    <select name="course_id" class="form-select filter-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">ASSIGNED BATCH</label>
                    <select name="batch_id" class="form-select filter-control">
                        <option value="">All Batches</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}" {{ request('batch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->batch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">STATUS</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="On Leave" {{ request('status') === 'On Leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="Dropped" {{ request('status') === 'Dropped' ? 'selected' : '' }}>Dropped</option>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="filter-label">BALANCE / FEE STATUS</label>
                    <select name="balance" class="form-select filter-control">
                        <option value="">All Balances</option>
                        <option value="paid" {{ request('balance') === 'paid' ? 'selected' : '' }}>Paid (No Dues)</option>
                        <option value="pending" {{ request('balance') === 'pending' ? 'selected' : '' }}>Pending Fee Dues</option>
                    </select>
                </div>
            </div>

            <!-- Sub Filter Bar: Action Buttons & Apply/Reset -->
            <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 mt-3 border-top gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('students.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-header-secondary">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export Student CSV
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn-header-secondary">
                        <i class="bi bi-download"></i> Download Template
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('students.index') }}" class="btn-filter-reset">Reset</a>
                    <button type="submit" class="btn-filter-apply">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- STUDENT TABLE CARD MATCHING REFERENCE UI -->
<div class="card-custom">
    <!-- Header with English Title & Add Buttons -->
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Student List
            </h5>
            <span class="page-sub-heading">
                All registered gurukulam students, assigned courses, batches, cognitive tracking, and fee balances
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('students.create') }}" class="btn-header-secondary">
                <i class="bi bi-people-fill"></i> Add Bulk Student
            </a>
            <a href="{{ route('students.create') }}" class="btn-header-primary">
                <i class="bi bi-plus-lg"></i> + Add Student
            </a>
        </div>
    </div>

    <!-- Data Table (English Only) -->
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 130px;">Student Code <span class="sort-arrow">⇅</span></th>
                    <th>Student Name <span class="sort-arrow">⇅</span></th>
                    <th>Phone Number <span class="sort-arrow">⇅</span></th>
                    <th>Village / City <span class="sort-arrow">⇅</span></th>
                    <th>Course <span class="sort-arrow">⇅</span></th>
                    <th>Assigned Batch <span class="sort-arrow">⇅</span></th>
                    <th>Assigned Counselor <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $avatarColors = ['avatar-green', 'avatar-teal', 'avatar-blue', 'avatar-purple', 'avatar-amber'];
                @endphp
                @forelse($students as $index => $st)
                    @php
                        $avatarClass = $avatarColors[$index % count($avatarColors)];
                        $initials = collect(explode(' ', $st->full_name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                        $firstCourse = $st->courses->first();
                        $firstEnrollment = $st->enrollments->first();
                        $batchName = $firstEnrollment?->batch?->batch_name ?? 'Regular Batch';
                    @endphp
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $students->firstItem() + $index }}
                        </td>

                        <!-- Student Code with pill -->
                        <td>
                            <a href="{{ route('students.show', $st->id) }}" class="text-decoration-none">
                                <span class="badge-code-pill">{{ $st->student_id }}</span>
                            </a>
                        </td>

                        <!-- Student Name with circular avatar -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('students.show', $st->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $st->full_name }}
                                    </a>
                                    @if($st->parent_name)
                                        <div class="text-muted" style="font-size: 0.72rem;">Parent: {{ $st->parent_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Phone Number -->
                        <td>
                            <span class="text-dark fw-medium font-monospace" style="font-size: 0.82rem;">
                                {{ $st->mobile }}
                            </span>
                        </td>

                        <!-- Village / City -->
                        <td>
                            <span class="text-dark">{{ $st->city ?? 'Campus' }}</span>
                        </td>

                        <!-- Course (Soft yellow tag matching reference) -->
                        <td>
                            @if($firstCourse)
                                <div class="badge-category-tag">
                                    <span>{{ $firstCourse->name }}</span>
                                    <span class="badge-sub-chart">₹{{ number_format($firstCourse->total_fees, 0) }}</span>
                                </div>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>

                        <!-- Assigned Batch (with clock icon) -->
                        <td>
                            <span class="d-inline-flex align-items-center gap-1 text-dark" style="font-size: 0.8rem;">
                                <i class="bi bi-clock-history text-muted"></i>
                                {{ $batchName }}
                            </span>
                        </td>

                        <!-- Assigned Counselor / Trainer -->
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                {{ $st->counselor?->name ?? 'Academic Counselor' }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td>
                            <span class="badge-status-pill {{ $st->status !== 'Active' ? 'badge-status-pending' : '' }}">
                                {{ strtoupper($st->status) }}
                            </span>
                        </td>

                        <!-- Circular Action Buttons -->
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('students.show', $st->id) }}" class="action-icon-btn" title="View Student Profile">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('students.pdf', $st->id) }}" class="action-icon-btn text-danger" title="Download Student Dossier PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="{{ route('students.edit', $st->id) }}" class="action-icon-btn text-primary" title="Edit Student">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('students.destroy', $st->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-icon-btn action-icon-delete btn-delete-confirm" title="Delete Student">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <h6>No students found matching your criteria</h6>
                                <p class="small">Try resetting your filter parameters or create a new student admission.</p>
                                <a href="{{ route('students.create') }}" class="btn-header-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> + Add First Student
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($students->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center bg-white">
            <span class="text-muted small">
                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
            </span>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
