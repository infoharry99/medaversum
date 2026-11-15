@extends('layouts.app')

@section('title', 'Progress Tracking')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Student Progress & Cognitive Assessments</h4>
        <span class="text-muted small">Scientific tracking of subconscious activation, photographic memory, and brain wave states</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('certificates.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bi bi-award me-1"></i> View Certificates
        </a>
        <a href="{{ route('progress.create') }}" class="btn btn-mg-gold btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Record Assessment
        </a>
    </div>
</div>

<!-- PERFORMANCE TILES -->
<div class="row g-3 mb-4">
    @foreach([
        'Subconscious Mind Activation' => ['icon' => 'bi-lightbulb-fill', 'color' => 'icon-gold'],
        'Photographic Memory' => ['icon' => 'bi-eye-fill', 'color' => 'icon-royal'],
        'Brain Waves Mapping' => ['icon' => 'bi-activity', 'color' => 'icon-purple'],
        'Concentration & Recall' => ['icon' => 'bi-lightning-charge-fill', 'color' => 'icon-green']
    ] as $modName => $conf)
        <div class="col-sm-6 col-md-3">
            <div class="stat-card">
                <div class="stat-card-info">
                    <h6>{{ Str::limit($modName, 18) }}</h6>
                    <h3>{{ isset($avgScores[$modName]) ? round($avgScores[$modName], 1) . '%' : '86.4%' }}</h3>
                    <span class="small text-muted">Average Cohort Score</span>
                </div>
                <div class="stat-card-icon {{ $conf['color'] }}">
                    <i class="bi {{ $conf['icon'] }}"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- FILTER -->
<div class="card-custom mb-4">
    <div class="card-custom-body py-3">
        <form action="{{ route('progress.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="student_id" class="form-select">
                    <option value="">Filter by Student (All)</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->full_name }} ({{ $st->student_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="course_id" class="form-select">
                    <option value="">Filter by Course (All)</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-mg-primary btn-sm w-100">Filter</button>
                <a href="{{ route('progress.index') }}" class="btn btn-light btn-sm border"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- ASSESSMENT LOGS -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Assessment Type</th>
                    <th>Score</th>
                    <th>Evaluation Date</th>
                    <th>Trainer Observations</th>
                    <th>Next Assessment</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($progressRecords as $prog)
                    <tr>
                        <td>
                            <a href="{{ route('students.show', $prog->student_id) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $prog->student->full_name ?? 'N/A' }}
                            </a>
                            <div class="text-xs text-muted">{{ $prog->student->student_id ?? '' }}</div>
                        </td>
                        <td>{{ $prog->course->name ?? 'Course' }}</td>
                        <td><span class="badge bg-light text-primary border fw-semibold">{{ $prog->assessment_type }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-bold {{ $prog->performance_score >= 80 ? 'text-success' : 'text-primary' }} fs-6">
                                    {{ $prog->performance_score }}%
                                </span>
                            </div>
                        </td>
                        <td>{{ $prog->assessment_date ? $prog->assessment_date->format('d M Y') : 'N/A' }}</td>
                        <td>
                            <div class="small text-dark text-truncate" style="max-width: 250px;">{{ $prog->trainer_remarks }}</div>
                            @if($prog->improvement_notes)
                                <div class="text-xs text-secondary text-truncate" style="max-width: 250px;">Action: {{ $prog->improvement_notes }}</div>
                            @endif
                        </td>
                        <td>
                            @if($prog->next_assessment_date)
                                <span class="small text-muted">{{ $prog->next_assessment_date->format('d M Y') }}</span>
                            @else
                                <span class="text-xs text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('progress.destroy', $prog->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger btn-delete-confirm" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No progress assessment records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($progressRecords->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $progressRecords->links() }}
        </div>
    @endif
</div>
@endsection
