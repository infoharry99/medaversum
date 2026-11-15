@extends('layouts.app')

@section('title', 'Staff Master')

@section('content')
<!-- TOP FILTER BAR MATCHING REFERENCE UI -->
<div class="card-custom mb-3">
    <div class="p-3">
        <form action="{{ route('staff.index') }}" method="GET" id="staffFilterForm">
            <!-- Filter Fields Grid -->
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="filter-label">SEARCH STAFF</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 py-1 px-2 text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control filter-control border-start-0 ps-1" 
                               placeholder="Search by name, staff ID, email, designation..." 
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-4">
                    <label class="filter-label">DEPARTMENT</label>
                    <select name="department" class="form-select filter-control">
                        <option value="">All Departments</option>
                        <option value="Administration" {{ request('department') === 'Administration' ? 'selected' : '' }}>Administration</option>
                        <option value="Training" {{ request('department') === 'Training' ? 'selected' : '' }}>Training & Faculty</option>
                        <option value="Counselling" {{ request('department') === 'Counselling' ? 'selected' : '' }}>Counselling</option>
                        <option value="Accounts" {{ request('department') === 'Accounts' ? 'selected' : '' }}>Accounts & Finance</option>
                        <option value="Support" {{ request('department') === 'Support' ? 'selected' : '' }}>Operations & Support</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('staff.index') }}" class="btn-filter-reset">Reset</a>
                    <button type="submit" class="btn-filter-apply">
                        <i class="bi bi-funnel-fill"></i> Apply Filters
                    </button>
                </div>
            </div>

            <!-- Sub Filter Bar: Action Buttons -->
            <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 mt-3 border-top gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('attendance.staff') }}" class="btn-header-secondary">
                        <i class="bi bi-calendar-check"></i> Staff Attendance Register
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn-header-secondary">
                        <i class="bi bi-file-earmark-person"></i> Faculty Performance
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('staff.create') }}" class="btn-header-primary">
                        <i class="bi bi-plus-lg"></i> + Add Staff Member
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- STAT METRIC CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Active Staff</h6>
                <h3>{{ $totalActive }}</h3>
                <span class="small text-muted">Across all functional faculties</span>
            </div>
            <div class="stat-card-icon icon-navy"><i class="bi bi-people-fill"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Monthly Payroll</h6>
                <h3 class="text-success">₹{{ number_format($totalPayroll, 2) }}</h3>
                <span class="small text-muted">Committed salary disbursements</span>
            </div>
            <div class="stat-card-icon icon-green"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Departments</h6>
                <h3>5</h3>
                <span class="small text-muted">Administration, Training, Counselling, Finance</span>
            </div>
            <div class="stat-card-icon icon-royal"><i class="bi bi-building"></i></div>
        </div>
    </div>
</div>

<!-- STAFF TABLE CARD MATCHING REFERENCE UI -->
<div class="card-custom">
    <!-- Header with English Title & Add Buttons -->
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Staff List
            </h5>
            <span class="page-sub-heading">
                Comprehensive directory of verified mentors, neuro-trainers, counsellors, and administrative officers
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('attendance.staff') }}" class="btn-header-secondary">
                <i class="bi bi-calendar-check"></i> Attendance & Leaves
            </a>
            <a href="{{ route('staff.create') }}" class="btn-header-primary">
                <i class="bi bi-plus-lg"></i> + Add Staff
            </a>
        </div>
    </div>

    <!-- Data Table (English Only) -->
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">S.no <span class="sort-arrow">⇅</span></th>
                    <th class="active-col-header" style="width: 130px;">Staff ID <span class="sort-arrow">⇅</span></th>
                    <th>Staff Name <span class="sort-arrow">⇅</span></th>
                    <th>Department <span class="sort-arrow">⇅</span></th>
                    <th>Designation <span class="sort-arrow">⇅</span></th>
                    <th>Phone Number <span class="sort-arrow">⇅</span></th>
                    <th>Email Address <span class="sort-arrow">⇅</span></th>
                    <th>Monthly Salary <span class="sort-arrow">⇅</span></th>
                    <th>Status <span class="sort-arrow">⇅</span></th>
                    <th class="text-end" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $avatarColors = ['avatar-purple', 'avatar-blue', 'avatar-teal', 'avatar-green', 'avatar-amber'];
                @endphp
                @forelse($staff as $index => $stf)
                    @php
                        $avatarClass = $avatarColors[$index % count($avatarColors)];
                        $initials = collect(explode(' ', $stf->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                    @endphp
                    <tr>
                        <!-- S.no -->
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Staff ID -->
                        <td>
                            <a href="{{ route('staff.show', $stf->id) }}" class="text-decoration-none">
                                <span class="badge-code-pill">{{ $stf->staff_code }}</span>
                            </a>
                        </td>

                        <!-- Staff Name -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-initials {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('staff.show', $stf->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $stf->name }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        <!-- Department (Yellow Tag) -->
                        <td>
                            <div class="badge-category-tag">
                                <span>{{ $stf->department }}</span>
                                <span class="badge-sub-chart">{{ $stf->designation }}</span>
                            </div>
                        </td>

                        <!-- Designation -->
                        <td>
                            <span class="text-dark fw-medium" style="font-size: 0.8rem;">
                                {{ $stf->designation }}
                            </span>
                        </td>

                        <!-- Phone Number -->
                        <td>
                            <span class="text-dark fw-medium font-monospace" style="font-size: 0.82rem;">
                                {{ $stf->mobile }}
                            </span>
                        </td>

                        <!-- Email Address -->
                        <td>
                            <span class="text-muted" style="font-size: 0.8rem;">
                                {{ $stf->email }}
                            </span>
                        </td>

                        <!-- Monthly Salary -->
                        <td>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">
                                ₹{{ number_format($stf->salary, 2) }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td>
                            <span class="badge-status-pill {{ $stf->status !== 'Active' ? 'badge-status-pending' : '' }}">
                                {{ strtoupper($stf->status) }}
                            </span>
                        </td>

                        <!-- Circular Action Buttons -->
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('staff.show', $stf->id) }}" class="action-icon-btn" title="View Staff Profile">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('staff.edit', $stf->id) }}" class="action-icon-btn text-primary" title="Edit Staff Member">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('staff.destroy', $stf->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-icon-btn action-icon-delete btn-delete-confirm" title="Delete Staff Member">
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
                                <h6>No staff members found</h6>
                                <p class="small">Add trainers, counselors, and staff members to manage attendance and courses.</p>
                                <a href="{{ route('staff.create') }}" class="btn-header-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> + Add First Staff Member
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($staff->hasPages())
        <div class="p-3 border-top d-flex justify-content-end bg-white">
            {{ $staff->links() }}
        </div>
    @endif
</div>
@endsection
