@extends('layouts.app')

@section('title', 'Batch Master')

@section('content')
<!-- TOP FILTER BAR MATCHING REFERENCE UI -->
<div class="card-custom mb-3">
    <div class="p-3">
        <form action="{{ route('batches.index') }}" method="GET" id="batchFilterForm">
            <!-- Filter Fields Grid -->
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="filter-label">COURSE</label>
                    <select name="course_id" class="form-select filter-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4">
                    <label class="filter-label">STATUS</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All Statuses</option>
                        <option value="Upcoming" {{ request('status') === 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="Ongoing" {{ request('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('batches.index') }}" class="btn-filter-reset">Reset</a>
                    <button type="submit" class="btn-filter-apply">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- BATCHES TABLE CARD MATCHING REFERENCE UI -->
<div class="card-custom">
    <!-- Header with English Title & Add Buttons -->
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Batch List
            </h5>
            <span class="page-sub-heading">
                Course cohorts, timings, classroom assignments, trainer allocations, and seat capacity
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('courses.index') }}" class="btn-header-secondary">
                <i class="bi bi-journal-bookmark"></i> View Courses
            </a>
            <a href="{{ route('batches.create') }}" class="btn-header-primary">
                <i class="bi bi-plus-lg"></i> + Add Batch
            </a>
        </div>
    </div>

    <!-- Data Table (English Only) -->
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 130px;">Batch Code <span class="sort-arrow">⇅</span></th>
                    <th>Batch Name <span class="sort-arrow">⇅</span></th>
                    <th>Course <span class="sort-arrow">⇅</span></th>
                    <th>Start Date <span class="sort-arrow">⇅</span></th>
                    <th>Timing & Schedule <span class="sort-arrow">⇅</span></th>
                    <th>Assigned Trainer <span class="sort-arrow">⇅</span></th>
                    <th>Capacity <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $avatarColors = ['avatar-blue', 'avatar-purple', 'avatar-green', 'avatar-teal', 'avatar-amber'];
                @endphp
                @forelse($batches as $index => $batch)
                    @php
                        $avatarClass = $avatarColors[$index % count($avatarColors)];
                        $initials = collect(explode(' ', $batch->batch_name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    @endphp
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Batch Code -->
                        <td>
                            <a href="{{ route('batches.show', $batch->id) }}" class="text-decoration-none">
                                <span class="badge-code-pill">{{ $batch->batch_code }}</span>
                            </a>
                        </td>

                        <!-- Batch Name -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('batches.show', $batch->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $batch->batch_name }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        <!-- Course (Yellow tag) -->
                        <td>
                            <div class="badge-category-tag">
                                <span>{{ $batch->course->name ?? 'N/A' }}</span>
                                <span class="badge-sub-chart">{{ $batch->course->course_code ?? '' }}</span>
                            </div>
                        </td>

                        <!-- Start Date -->
                        <td>
                            <span class="d-inline-flex align-items-center gap-1 text-dark" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar-event text-muted"></i>
                                {{ \Carbon\Carbon::parse($batch->start_date)->format('d M Y') }}
                            </span>
                        </td>

                        <!-- Timing & Schedule -->
                        <td>
                            <div class="text-dark fw-medium" style="font-size: 0.8rem;">
                                <i class="bi bi-clock-history text-muted me-1"></i>{{ $batch->schedule_time ?? '09:00 AM' }}
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $batch->days_of_week ?? 'Mon - Fri' }}</div>
                        </td>

                        <!-- Assigned Trainer -->
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                {{ $batch->trainer?->name ?? 'Lead Trainer' }}
                            </span>
                        </td>

                        <!-- Capacity -->
                        <td>
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.76rem;">
                                {{ $batch->enrollments_count ?? $batch->enrollments()->count() }} / {{ $batch->max_capacity }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td>
                            @php
                                $badgeColor = match($batch->status) {
                                    'Ongoing' => 'badge-status-pill',
                                    'Upcoming' => 'bg-info bg-opacity-10 text-info fw-bold',
                                    'Completed' => 'bg-success bg-opacity-10 text-success fw-bold',
                                    default => 'bg-light text-muted border',
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }}" style="font-size: 0.72rem; padding: 4px 8px;">
                                {{ strtoupper($batch->status) }}
                            </span>
                        </td>

                        <!-- Circular Action Buttons -->
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('batches.show', $batch->id) }}" class="action-icon-btn" title="View Batch Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('batches.edit', $batch->id) }}" class="action-icon-btn text-primary" title="Edit Batch">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-icon-btn action-icon-delete btn-delete-confirm" title="Delete Batch">
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
                                <h6>No batches scheduled</h6>
                                <p class="small">Create cohorts to organize student class timings and trainers.</p>
                                <a href="{{ route('batches.create') }}" class="btn-header-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> + Add First Batch
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
