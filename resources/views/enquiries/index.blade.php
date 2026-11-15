@extends('layouts.app')

@section('title', 'Enquiry Management')

@section('content')
<!-- TOP FILTER BAR MATCHING REFERENCE UI (gopaldudhdairy.physiopii.in/farmers) -->
<div class="card-custom mb-3">
    <div class="p-3">
        <form action="{{ route('enquiries.index') }}" method="GET" id="enquiryFilterForm">
            <!-- Filter Fields Grid -->
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="filter-label">SEARCH ENQUIRY</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-control border-start-0 ps-1" 
                               placeholder="Search by student name, code, phone, parent..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">INTERESTED COURSE</label>
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
                    <label class="filter-label">ASSIGNED COUNSELOR</label>
                    <select name="counselor_id" class="form-select filter-control">
                        <option value="">All Counselors</option>
                        @foreach($counselors as $cn)
                            <option value="{{ $cn->id }}" {{ request('counselor_id') == $cn->id ? 'selected' : '' }}>
                                {{ $cn->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="filter-label">STATUS</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All Statuses</option>
                        @foreach(['New', 'Contacted', 'Follow-up', 'Interested', 'Converted', 'Not Interested', 'Closed'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="filter-label">LEAD SOURCE</label>
                    <select name="source" class="form-select filter-control">
                        <option value="">All Sources</option>
                        @foreach(['Walk-in', 'Website', 'Referral', 'Social Media', 'Phone'] as $src)
                            <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Sub Filter Bar: Action Buttons & Apply/Reset -->
            <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 mt-3 border-top gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @foreach($statusCounts as $stName => $stCount)
                        <a href="{{ route('enquiries.index', $stName === 'All' ? [] : ['status' => $stName]) }}" 
                           class="btn btn-sm rounded-pill {{ (request('status') === $stName || (empty(request('status')) && $stName === 'All')) ? 'btn-header-primary' : 'btn-header-secondary' }} px-3 py-1" style="font-size: 0.76rem;">
                            {{ $stName }}: <strong>{{ $stCount }}</strong>
                        </a>
                    @endforeach
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('enquiries.index') }}" class="btn-filter-reset">Reset</a>
                    <button type="submit" class="btn-filter-apply">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ENQUIRY TABLE CARD MATCHING REFERENCE UI -->
<div class="card-custom">
    <!-- Header with English Title & Add Buttons -->
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Enquiry List
            </h5>
            <span class="page-sub-heading">
                All registered leads, counseling interactions, follow-up calendar, and conversion workflow
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('students.create') }}" class="btn-header-secondary">
                <i class="bi bi-person-check-fill"></i> Direct Admission
            </a>
            <a href="{{ route('enquiries.create') }}" class="btn-header-primary">
                <i class="bi bi-plus-lg"></i> + Add Enquiry
            </a>
        </div>
    </div>

    <!-- Data Table (English Only) -->
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 130px;">Enquiry Code <span class="sort-arrow">⇅</span></th>
                    <th>Student Name <span class="sort-arrow">⇅</span></th>
                    <th>Phone Number <span class="sort-arrow">⇅</span></th>
                    <th>Parent / Contact <span class="sort-arrow">⇅</span></th>
                    <th>Interested Course <span class="sort-arrow">⇅</span></th>
                    <th>Source <span class="sort-arrow">⇅</span></th>
                    <th>Assigned Counselor <span class="sort-arrow">⇅</span></th>
                    <th>Follow-up <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $avatarColors = ['avatar-teal', 'avatar-purple', 'avatar-blue', 'avatar-green', 'avatar-amber'];
                @endphp
                @forelse($enquiries as $index => $enq)
                    @php
                        $avatarClass = $avatarColors[$index % count($avatarColors)];
                        $initials = collect(explode(' ', $enq->student_name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    @endphp
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $enquiries->firstItem() + $index }}
                        </td>

                        <!-- Enquiry Code with pill -->
                        <td>
                            <a href="{{ route('enquiries.show', $enq->id) }}" class="text-decoration-none">
                                <span class="badge-code-pill">{{ $enq->enquiry_code }}</span>
                            </a>
                        </td>

                        <!-- Student Name with circular avatar -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('enquiries.show', $enq->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $enq->student_name }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        Date: {{ \Carbon\Carbon::parse($enq->enquiry_date)->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Phone Number -->
                        <td>
                            <span class="text-dark fw-medium font-monospace" style="font-size: 0.82rem;">
                                {{ $enq->mobile }}
                            </span>
                        </td>

                        <!-- Parent / Contact -->
                        <td>
                            <span class="text-dark">{{ $enq->parent_name ?? '&mdash;' }}</span>
                        </td>

                        <!-- Interested Course (Soft yellow tag) -->
                        <td>
                            @if($enq->course)
                                <div class="badge-category-tag">
                                    <span>{{ $enq->course->name }}</span>
                                    <span class="badge-sub-chart">₹{{ number_format($enq->course->total_fees, 0) }}</span>
                                </div>
                            @else
                                <span class="badge bg-light text-muted border">General Guidance</span>
                            @endif
                        </td>

                        <!-- Source -->
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                <i class="bi bi-tag text-muted me-1"></i>{{ $enq->source }}
                            </span>
                        </td>

                        <!-- Assigned Counselor -->
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                {{ $enq->counselor?->name ?? 'Unassigned' }}
                            </span>
                        </td>

                        <!-- Follow-up -->
                        <td>
                            @if($enq->follow_up_date)
                                <span class="text-dark fw-medium" style="font-size: 0.8rem;">
                                    <i class="bi bi-calendar-event text-primary me-1"></i>
                                    {{ \Carbon\Carbon::parse($enq->follow_up_date)->format('d M') }}
                                </span>
                            @else
                                <span class="text-muted">&mdash;</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td>
                            @php
                                $statusClass = match($enq->status) {
                                    'Converted' => 'bg-success text-white',
                                    'Interested' => 'badge-status-pill',
                                    'Follow-up' => 'badge-status-pending',
                                    'New' => 'bg-info bg-opacity-10 text-info fw-bold',
                                    default => 'bg-light text-muted border',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}" style="font-size: 0.72rem; padding: 4px 8px;">
                                {{ strtoupper($enq->status) }}
                            </span>
                        </td>

                        <!-- Circular Action Buttons -->
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($enq->status !== 'Converted')
                                    <a href="{{ route('students.create', ['enquiry_id' => $enq->id]) }}" class="action-icon-btn text-success" title="Convert to Admission" style="background: #ECFDF5; border-color: #A7F3D0;">
                                        <i class="bi bi-person-check"></i>
                                    </a>
                                @endif
                                <a href="{{ route('enquiries.show', $enq->id) }}" class="action-icon-btn" title="View Enquiry Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('enquiries.edit', $enq->id) }}" class="action-icon-btn text-primary" title="Edit Enquiry">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('enquiries.destroy', $enq->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-icon-btn action-icon-delete btn-delete-confirm" title="Delete Enquiry">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <h6>No enquiries found matching your criteria</h6>
                                <p class="small">Try resetting your filter parameters or record a new enquiry lead.</p>
                                <a href="{{ route('enquiries.create') }}" class="btn-header-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> + Add First Enquiry
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if($enquiries->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center bg-white">
            <span class="text-muted small">
                Showing {{ $enquiries->firstItem() }} to {{ $enquiries->lastItem() }} of {{ $enquiries->total() }} enquiries
            </span>
            <div>
                {{ $enquiries->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
