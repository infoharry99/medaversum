@extends('layouts.app')

@section('title', 'Edit Batch')

@section('content')
<div class="mb-4">
    <a href="{{ route('batches.index') }}" class="btn btn-sm btn-light border mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Batches</a>
    <h4 class="fw-bold text-dark m-0">Edit Batch: {{ $batch->batch_name }}</h4>
    <span class="text-muted small">Update scheduling, timings or trainer allocation</span>
</div>

<div class="card-custom">
    <div class="card-custom-body p-4">
        <form action="{{ route('batches.update', $batch->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id', $batch->course_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->course_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batch Code <span class="text-danger">*</span></label>
                    <input type="text" name="batch_code" class="form-control text-uppercase" value="{{ old('batch_code', $batch->batch_code) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Batch Name <span class="text-danger">*</span></label>
                    <input type="text" name="batch_name" class="form-control" value="{{ old('batch_name', $batch->batch_name) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $batch->start_date) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $batch->end_date) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Schedule Timing</label>
                    <input type="text" name="schedule_time" class="form-control" value="{{ old('schedule_time', $batch->schedule_time) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Days of Week</label>
                    <input type="text" name="days_of_week" class="form-control" value="{{ old('days_of_week', $batch->days_of_week) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Max Student Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity', $batch->max_capacity) }}" required min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assigned Trainer</label>
                    <select name="trainer_id" class="form-select">
                        <option value="">Select Trainer</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ old('trainer_id', $batch->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Batch Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Upcoming" {{ old('status', $batch->status) === 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="Ongoing" {{ old('status', $batch->status) === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="Completed" {{ old('status', $batch->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('status', $batch->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-12 text-end pt-3 border-top mt-4">
                    <a href="{{ route('batches.index') }}" class="btn btn-light border me-2">Cancel</a>
                    <button type="submit" class="btn btn-mg-gold px-4">Update Batch</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
