@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('content')
<div class="card-custom mb-3">
    <div class="card-custom-header">
        <div class="page-title-box">
            <h5 class="page-main-heading">
                Executive Dashboard
            </h5>
            <span class="page-sub-heading">
                Medaversum Gurukulam &bull; Real-time operational intelligence, student admissions, fee tracking, and cognitive analytics
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.index') }}" class="btn-header-secondary">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports & Analytics
            </a>
            <a href="{{ route('students.create') }}" class="btn-header-primary">
                <i class="bi bi-person-plus-fill"></i> + New Admission
            </a>
        </div>
    </div>
</div>

<!-- STATS METRICS ROW -->
<div class="row g-3 mb-4">
    <!-- Total Students -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Students</h6>
                <h3>{{ $totalStudents }}</h3>
                <span class="badge badge-active">{{ $activeStudents }} Active</span>
            </div>
            <div class="stat-card-icon icon-navy">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Revenue</h6>
                <h3>₹{{ number_format($totalRevenue, 2) }}</h3>
                <span class="text-success small fw-semibold"><i class="bi bi-arrow-up-right"></i> All time collections</span>
            </div>
            <div class="stat-card-icon icon-green">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <!-- Pending Fees -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Pending Fees</h6>
                <h3>₹{{ number_format($pendingFees, 2) }}</h3>
                <a href="{{ route('fees.index') }}" class="text-danger small fw-semibold text-decoration-none">View installment dues &rarr;</a>
            </div>
            <div class="stat-card-icon icon-red">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
    </div>

    <!-- Today's Collections -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Today's Collections</h6>
                <h3>₹{{ number_format($todayCollections, 2) }}</h3>
                <span class="text-muted small">Real-time receipts</span>
            </div>
            <div class="stat-card-icon icon-gold">
                <i class="bi bi-wallet-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Enquiries -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Total Enquiries</h6>
                <h3>{{ $totalEnquiries }}</h3>
                <a href="{{ route('enquiries.index') }}" class="text-primary small fw-semibold text-decoration-none">Manage pipeline &rarr;</a>
            </div>
            <div class="stat-card-icon icon-royal">
                <i class="bi bi-person-lines-fill"></i>
            </div>
        </div>
    </div>

    <!-- New Admissions This Month -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Month Admissions</h6>
                <h3>{{ $newAdmissionsThisMonth }}</h3>
                <span class="text-muted small">Current month registrations</span>
            </div>
            <div class="stat-card-icon icon-purple">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
        </div>
    </div>

    <!-- Active Courses -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Active Courses</h6>
                <h3>{{ $activeCourses }}</h3>
                <a href="{{ route('courses.index') }}" class="text-muted small text-decoration-none">View syllabus & batches &rarr;</a>
            </div>
            <div class="stat-card-icon icon-navy">
                <i class="bi bi-journal-check"></i>
            </div>
        </div>
    </div>

    <!-- Staff & Attendance -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-card-info">
                <h6>Staff On Duty</h6>
                <h3>{{ $todayStaffPresent }} / {{ $totalStaff }}</h3>
                <span class="badge bg-light text-primary">Checked in today</span>
            </div>
            <div class="stat-card-icon icon-royal">
                <i class="bi bi-person-badge-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="row g-4 mb-4">
    <!-- Revenue Trend Chart -->
    <div class="col-lg-8">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-graph-up text-primary"></i> Monthly Revenue Trend (₹)</h5>
                <span class="badge bg-light text-secondary border">Last 6 Months</span>
            </div>
            <div class="card-custom-body">
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Distribution Chart -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-pie-chart text-primary"></i> Course Enrollment</h5>
                <span class="badge bg-light text-secondary border">Distribution</span>
            </div>
            <div class="card-custom-body d-flex flex-column align-items-center justify-content-center">
                <div style="height: 250px; width: 100%;">
                    <canvas id="courseChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RECENT ADMISSIONS & RECENT PAYMENTS -->
<div class="row g-4 mb-4">
    <!-- Recent Admissions -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-person-plus text-primary"></i> Recent Student Admissions</h5>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-light border">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Admission Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAdmissions as $student)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($student->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('students.show', $student->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $student->full_name }}
                                            </a>
                                            <div class="text-xs text-muted" style="font-size: 0.72rem;">{{ $student->student_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="small fw-semibold">{{ $student->courses->first()->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-active">{{ $student->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No admissions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-receipt text-primary"></i> Recent Fee Collections</h5>
                <a href="{{ route('fees.index') }}" class="btn btn-sm btn-light border">View Fees</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Receipt / ID</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">{{ $payment->receipt_no }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('students.show', $payment->student_id) }}" class="fw-semibold text-dark text-decoration-none">
                                        {{ $payment->student->full_name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="fw-bold text-success">
                                    ₹{{ number_format($payment->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span>
                                </td>
                                <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No fee payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- UPCOMING BATCHES, FOLLOW-UPS & RECENT ACTIVITY -->
<div class="row g-4">
    <!-- Upcoming Batches -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-clock-history text-primary"></i> Active Batches</h5>
                <a href="{{ route('batches.index') }}" class="small text-decoration-none text-primary fw-semibold">View all</a>
            </div>
            <div class="card-custom-body p-3">
                @forelse($upcomingBatches as $batch)
                    <div class="p-3 border rounded-3 mb-2 bg-light bg-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">{{ $batch->batch_name }}</span>
                            <span class="badge {{ $batch->status === 'Ongoing' ? 'bg-success' : 'bg-info' }}">{{ $batch->status }}</span>
                        </div>
                        <div class="small text-muted mb-2">{{ $batch->course->name ?? 'Course' }}</div>
                        <div class="d-flex justify-content-between align-items-center small text-secondary">
                            <span><i class="bi bi-calendar-event me-1"></i> {{ $batch->schedule_time }}</span>
                            <span><i class="bi bi-people me-1"></i> {{ $batch->enrollments_count ?? $batch->students()->count() }}/{{ $batch->max_capacity }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">No active batches available.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Upcoming Follow-ups -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-telephone-outbound text-primary"></i> Pending Follow-ups</h5>
                <a href="{{ route('enquiries.index') }}" class="small text-decoration-none text-primary fw-semibold">View all</a>
            </div>
            <div class="card-custom-body p-3">
                @forelse($upcomingFollowUps as $enq)
                    <div class="p-3 border rounded-3 mb-2 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">{{ $enq->student_name }}</span>
                            <span class="badge badge-followup">{{ $enq->status }}</span>
                        </div>
                        <div class="small text-muted mb-2">Interested in: <strong class="text-dark">{{ $enq->course->name ?? 'Course' }}</strong></div>
                        <div class="d-flex justify-content-between align-items-center small text-secondary">
                            <span><i class="bi bi-calendar-check me-1 text-primary"></i> Due: {{ $enq->follow_up_date ?? 'Soon' }}</span>
                            <a href="tel:{{ $enq->mobile }}" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill"><i class="bi bi-telephone"></i> Call</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3 small">No follow-ups pending today.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Audit Logs -->
    <div class="col-lg-4">
        <div class="card-custom h-100">
            <div class="card-custom-header">
                <h5 class="card-custom-title"><i class="bi bi-activity text-primary"></i> Activity Trail</h5>
            </div>
            <div class="card-custom-body p-3">
                <div class="activity-feed">
                    @forelse($recentActivities as $act)
                        <div class="d-flex align-items-start gap-2 mb-3 pb-2 border-bottom">
                            <div class="p-2 bg-light text-primary rounded-circle small">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold small text-dark">{{ $act->action }} &bull; {{ $act->module }}</span>
                                    <span class="text-xs text-muted" style="font-size: 0.7rem;">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-muted small" style="font-size: 0.78rem;">{{ $act->description }}</div>
                                <div class="text-xs text-secondary" style="font-size: 0.7rem;">By: {{ $act->user->name ?? 'System' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3 small">No recent activities logged.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Revenue Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRevenueLabels) !!},
            datasets: [{
                label: 'Collections (₹)',
                data: {!! json_encode($monthlyRevenueData) !!},
                borderColor: '#1E40AF',
                backgroundColor: 'rgba(30, 64, 175, 0.08)',
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointBackgroundColor: '#C59B27',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₹' + value.toLocaleString(); }
                    },
                    grid: { color: '#F1F5F9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Course Distribution Chart
    const ctxCourse = document.getElementById('courseChart').getContext('2d');
    new Chart(ctxCourse, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($courseLabels) !!},
            datasets: [{
                data: {!! json_encode($courseData) !!},
                backgroundColor: [
                    '#0B1B3D',
                    '#1E40AF',
                    '#C59B27',
                    '#10B981',
                    '#8B5CF6'
                ],
                borderWidth: 2,
                borderColor: '#FFFFFF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            },
            cutout: '65%'
        }
    });
</script>
@endpush
