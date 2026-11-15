@extends('layouts.app')

@section('title', 'Record Progress Assessment')

@section('content')
<div class="mb-4">
    <a href="{{ route('progress.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Assessments</a>
    <h4 class="fw-bold text-dark m-0">Record Student Cognitive Evaluation</h4>
    <span class="text-muted small">Score performance across subconscious activation, focus, and memory protocols</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('progress.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Select Student <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Choose Student --</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ (old('student_id') == $st->id || $selectedStudentId == $st->id) ? 'selected' : '' }}>
                                {{ $st->full_name }} ({{ $st->student_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Course Module <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-select" required>
                        <option value="">-- Choose Course --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Assessment Date <span class="text-danger">*</span></label>
                    <input type="date" name="assessment_date" class="form-control" value="{{ old('assessment_date', date('Y-m-d')) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Assessment Protocol Type <span class="text-danger">*</span></label>
                    <select name="assessment_type" class="form-select" required>
                        <option value="Subconscious Mind Activation">Subconscious Mind Activation</option>
                        <option value="Photographic Memory">Photographic Memory</option>
                        <option value="Brain Waves Mapping">Brain Waves Mapping</option>
                        <option value="Concentration & Recall Speed">Concentration & Recall Speed</option>
                        <option value="Mid-Brain Intuition Test">Mid-Brain Intuition Test</option>
                        <option value="General Cognitive Evaluation">General Cognitive Evaluation</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Performance Score (0 - 100%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="performance_score" class="form-control fw-bold text-primary" value="{{ old('performance_score', 85.0) }}" min="0" max="100" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Trainer Remarks & Cognitive Observations <span class="text-danger">*</span></label>
                    <textarea name="trainer_remarks" rows="3" class="form-control" required placeholder="Detail the student's alpha-theta wave induction response, memory recall latency, visualization clarity...">{{ old('trainer_remarks') }}</textarea>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Action Plan / Improvement Exercises</label>
                    <input type="text" name="improvement_notes" class="form-control" value="{{ old('improvement_notes') }}" placeholder="e.g. 15-minute daily binaural beats breathing exercises">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Next Scheduled Evaluation</label>
                    <input type="date" name="next_assessment_date" class="form-control" value="{{ old('next_assessment_date', \Carbon\Carbon::now()->addDays(14)->format('Y-m-d')) }}">
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('progress.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Save Assessment Record</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
