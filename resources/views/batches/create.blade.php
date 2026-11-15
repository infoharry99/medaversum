@extends('layouts.app')

@section('title', 'Create Batch')

@section('content')
<div class="mb-4">
    <a href="{{ route('batches.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Batches</a>
    <h4 class="fw-bold text-dark m-0">Create New Course Batch</h4>
    <span class="text-muted small">Schedule timing, assign trainers and allocate batch capacity</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('batches.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Select Associated Course</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ (old('course_id') == $c->id || request('course_id') == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batch Code <span class="text-danger">*</span></label>
                    <input type="text" name="batch_code" class="form-control text-uppercase" value="{{ old('batch_code') }}" required placeholder="e.g. SMA-BATCH-02">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batch Name <span class="text-danger">*</span></label>
                    <input type="text" name="batch_name" class="form-control" value="{{ old('batch_name') }}" required placeholder="e.g. Morning Alpha Track">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Schedule Timing</label>
                    <input type="text" name="schedule_time" class="form-control" value="{{ old('schedule_time', '09:00 AM - 11:00 AM') }}" placeholder="e.g. 09:00 AM - 11:00 AM">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Days of Week</label>
                    <input type="text" name="days_of_week" class="form-control" value="{{ old('days_of_week', 'Mon, Wed, Fri') }}" placeholder="e.g. Mon, Wed, Fri">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Max Student Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity', 25) }}" required min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assigned Trainer</label>
                    <select name="trainer_id" class="form-select">
                        <option value="">Select Trainer</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Batch Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Upcoming" {{ old('status') === 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="Ongoing" {{ old('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('batches.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Save Batch</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
