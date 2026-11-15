<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentProgress;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\ActivityLog;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentProgress::with(['student', 'course', 'trainer'])->latest('assessment_date');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        $progressRecords = $query->paginate(12)->withQueryString();
        $students = Student::where('status', 'Active')->get();
        $courses = Course::where('status', 'Active')->get();

        // Calculate average performance scores by assessment type
        $avgScores = StudentProgress::selectRaw('assessment_type, AVG(performance_score) as avg_score')
            ->groupBy('assessment_type')
            ->pluck('avg_score', 'assessment_type')
            ->toArray();

        return view('progress.index', compact('progressRecords', 'students', 'courses', 'avgScores'));
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'Active')->get();
        $courses = Course::where('status', 'Active')->get();
        $selectedStudentId = $request->student_id;

        return view('progress.create', compact('students', 'courses', 'selectedStudentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'assessment_date' => 'required|date',
            'assessment_type' => 'required|string|max:100',
            'performance_score' => 'required|numeric|min:0|max:100',
            'trainer_remarks' => 'required|string',
            'improvement_notes' => 'nullable|string',
            'next_assessment_date' => 'nullable|date',
        ]);

        $validated['trainer_id'] = auth()->id();

        $prog = StudentProgress::create($validated);

        ActivityLog::record(
            'Progress Assessment',
            'Progress',
            $prog->id,
            "Recorded {$prog->assessment_type} assessment for Student ID {$prog->student_id} with score {$prog->performance_score}%."
        );

        return redirect()->route('progress.index')->with('success', 'Cognitive assessment evaluation recorded.');
    }

    public function destroy($id)
    {
        $prog = StudentProgress::findOrFail($id);
        $prog->delete();

        return redirect()->route('progress.index')->with('success', 'Assessment record deleted.');
    }
}
