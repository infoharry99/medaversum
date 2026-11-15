@extends('layouts.app')

@section('title', 'Courses Master')

@section('content')
<!-- TOP FILTER BAR MATCHING REFERENCE UI -->
<div class="card-custom mb-3">
    <div class="p-3">
        <form action="{{ route('courses.index') }}" method="GET" id="courseFilterForm">
            <!-- Filter Fields Grid -->
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="filter-label">SEARCH COURSE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-control border-start-0 ps-1" 
                               placeholder="Course name, code, description..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <label class="filter-label">STATUS</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('courses.index') }}" class="btn-filter-reset">Reset</a>
                    <button type="submit" class="btn-filter-apply">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- COURSES TABLE CARD MATCHING REFERENCE UI -->
<div class="card-custom">
    <!-- Header with English Title & Add Buttons -->
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Course List
            </h5>
            <span class="page-sub-heading">
                Comprehensive directory of neuroscience programs, syllabus, fees structure, and assigned batches
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('batches.index') }}" class="btn-header-secondary">
                <i class="bi bi-clock-history"></i> Manage Batches
            </a>
            <a href="{{ route('courses.create') }}" class="btn-header-primary">
                <i class="bi bi-plus-lg"></i> + Add Course
            </a>
        </div>
    </div>

    <!-- Data Table (English Only) -->
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 130px;">Course Code <span class="sort-arrow">⇅</span></th>
                    <th>Course Name <span class="sort-arrow">⇅</span></th>
                    <th>Duration <span class="sort-arrow">⇅</span></th>
                    <th>Fee Structure <span class="sort-arrow">⇅</span></th>
                    <th>Active Batches <span class="sort-arrow">⇅</span></th>
                    <th>Enrolled Students <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $avatarColors = ['avatar-amber', 'avatar-blue', 'avatar-purple', 'avatar-teal', 'avatar-green'];
                @endphp
                @forelse($courses as $index => $course)
                    @php
                        $avatarClass = $avatarColors[$index % count($avatarColors)];
                        $initials = collect(explode(' ', $course->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    @endphp
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Course Code -->
                        <td>
                            <a href="{{ route('courses.show', $course->id) }}" class="text-decoration-none">
                                <span class="badge-code-pill">{{ $course->course_code }}</span>
                            </a>
                        </td>

                        <!-- Course Name -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('courses.show', $course->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $course->name }}
                                    </a>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 280px;">
                                        {{ $course->description }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Duration -->
                        <td>
                            <span class="d-inline-flex align-items-center gap-1 text-dark" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar3 text-muted"></i>
                                {{ $course->duration_weeks }} Weeks
                            </span>
                        </td>

                        <!-- Fee Structure (Yellow Tag) -->
                        <td>
                            <div class="badge-category-tag">
                                <span>₹{{ number_format($course->total_fees, 0) }}</span>
                                <span class="badge-sub-chart">Adm: ₹{{ number_format($course->admission_fees, 0) }}</span>
                            </div>
                        </td>

                        <!-- Active Batches -->
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.74rem;">
                                <i class="bi bi-calendar-event me-1"></i>{{ $course->batches->count() }} Batches
                            </span>
                        </td>

                        <!-- Enrolled Students -->
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fw-bold" style="font-size: 0.74rem;">
                                <i class="bi bi-people me-1"></i>{{ $course->students_count ?? $course->students()->count() }} Students
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td>
                            <span class="badge-status-pill {{ $course->status !== 'Active' ? 'badge-status-pending' : '' }}">
                                {{ strtoupper($course->status) }}
                            </span>
                        </td>

                        <!-- Circular Action Buttons -->
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('courses.show', $course->id) }}" class="action-icon-btn" title="View Course Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('courses.edit', $course->id) }}" class="action-icon-btn text-primary" title="Edit Course">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-icon-btn action-icon-delete btn-delete-confirm" title="Delete Course">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <h6>No courses found</h6>
                                <p class="small">Add a new curriculum course to get started.</p>
                                <a href="{{ route('courses.create') }}" class="btn-header-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> + Add First Course
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
