<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['trainer', 'batches'])->withCount('students');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('course_code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->paginate(10)->withQueryString();
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();

        return view('courses.index', compact('courses', 'trainers'));
    }

    public function create()
    {
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();
        return view('courses.create', compact('trainers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:50|unique:courses,course_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1',
            'total_fees' => 'required|numeric|min:0',
            'admission_fees' => 'required|numeric|min:0',
            'installment_count' => 'required|integer|min:1|max:12',
            'batch_timing' => 'nullable|string|max:100',
            'trainer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $validated['slug'] = Str::slug($request->name) . '-' . Str::random(5);

        $course = Course::create($validated);

        ActivityLog::record('Create', 'Courses', $course->id, "Course {$course->name} ({$course->course_code}) created.");

        return redirect()->route('courses.index')->with('success', "Course '{$course->name}' added successfully.");
    }

    public function show($id)
    {
        $course = Course::with(['trainer', 'batches.trainer', 'students.fees'])->findOrFail($id);
        
        $totalRevenue = $course->enrollments()->whereHas('fee', function($q) {
            $q->where('paid_amount', '>', 0);
        })->get()->sum(function($e) {
            return $e->fee ? $e->fee->paid_amount : 0;
        });

        return view('courses.show', compact('course', 'totalRevenue'));
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();

        return view('courses.edit', compact('course', 'trainers'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_code' => 'required|string|max:50|unique:courses,course_code,' . $course->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1',
            'total_fees' => 'required|numeric|min:0',
            'admission_fees' => 'required|numeric|min:0',
            'installment_count' => 'required|integer|min:1|max:12',
            'batch_timing' => 'nullable|string|max:100',
            'trainer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $course->update($validated);

        ActivityLog::record('Update', 'Courses', $course->id, "Course {$course->name} updated.");

        return redirect()->route('courses.index')->with('success', "Course '{$course->name}' updated successfully.");
    }

    public function destroy($id)
    {
        $course = Course::withCount('students')->findOrFail($id);

        if ($course->students_count > 0) {
            return back()->with('error', 'Cannot delete course with enrolled students. Deactivate it instead.');
        }

        $name = $course->name;
        $course->delete();

        ActivityLog::record('Delete', 'Courses', $id, "Course {$name} deleted.");

        return redirect()->route('courses.index')->with('success', "Course '{$name}' deleted successfully.");
    }
}
