<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Models\ActivityLog;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::with(['course', 'trainer'])->withCount('enrollments');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $batches = $query->latest('start_date')->paginate(10)->withQueryString();
        $courses = Course::where('status', 'Active')->get();
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();

        return view('batches.index', compact('batches', 'courses', 'trainers'));
    }

    public function create()
    {
        $courses = Course::where('status', 'Active')->get();
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();

        return view('batches.create', compact('courses', 'trainers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_code' => 'required|string|max:50|unique:batches,batch_code',
            'batch_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule_time' => 'nullable|string|max:100',
            'days_of_week' => 'nullable|string|max:100',
            'max_capacity' => 'required|integer|min:1',
            'trainer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Upcoming,Ongoing,Completed,Cancelled',
        ]);

        $batch = Batch::create($validated);

        ActivityLog::record('Create', 'Batches', $batch->id, "Batch {$batch->batch_name} ({$batch->batch_code}) created.");

        return redirect()->route('batches.index')->with('success', "Batch '{$batch->batch_name}' scheduled successfully.");
    }

    public function edit($id)
    {
        $batch = Batch::findOrFail($id);
        $courses = Course::all();
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();

        return view('batches.edit', compact('batch', 'courses', 'trainers'));
    }

    public function update(Request $request, $id)
    {
        $batch = Batch::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_code' => 'required|string|max:50|unique:batches,batch_code,' . $batch->id,
            'batch_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule_time' => 'nullable|string|max:100',
            'days_of_week' => 'nullable|string|max:100',
            'max_capacity' => 'required|integer|min:1',
            'trainer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Upcoming,Ongoing,Completed,Cancelled',
        ]);

        $batch->update($validated);

        ActivityLog::record('Update', 'Batches', $batch->id, "Batch {$batch->batch_name} updated.");

        return redirect()->route('batches.index')->with('success', "Batch '{$batch->batch_name}' updated successfully.");
    }

    public function destroy($id)
    {
        $batch = Batch::withCount('enrollments')->findOrFail($id);

        if ($batch->enrollments_count > 0) {
            return back()->with('error', 'Cannot delete batch with active student enrollments.');
        }

        $name = $batch->batch_name;
        $batch->delete();

        ActivityLog::record('Delete', 'Batches', $id, "Batch {$name} deleted.");

        return redirect()->route('batches.index')->with('success', "Batch '{$name}' deleted successfully.");
    }
}
