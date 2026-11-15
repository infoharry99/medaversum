<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\StudentAttendance;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // Student Attendance view & marking
    public function studentIndex(Request $request)
    {
        $selectedCourseId = $request->course_id;
        $selectedBatchId = $request->batch_id;
        $selectedDate = $request->date ?? Carbon::today()->format('Y-m-d');

        $courses = Course::where('status', 'Active')->get();
        $batches = Batch::where('status', 'Ongoing')->get();

        // If course selected, fetch enrolled students
        $students = collect();
        if ($selectedCourseId) {
            $students = Student::whereHas('courses', function($q) use ($selectedCourseId, $selectedBatchId) {
                $q->where('courses.id', $selectedCourseId);
                if ($selectedBatchId) {
                    $q->where('student_courses.batch_id', $selectedBatchId);
                }
            })->where('status', 'Active')->get();
        }

        // Fetch existing attendance records for the chosen date
        $existingAttendance = StudentAttendance::whereDate('date', $selectedDate)
            ->when($selectedCourseId, function($q) use ($selectedCourseId) {
                $q->where('course_id', $selectedCourseId);
            })->get()->keyBy('student_id');

        // Overall stats
        $totalSessions = StudentAttendance::count();
        $todayAttendance = StudentAttendance::whereDate('date', $selectedDate)->count();
        $todayPresent = StudentAttendance::whereDate('date', $selectedDate)->whereIn('status', ['Present', 'Late'])->count();
        $todayPercent = $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 0;

        return view('attendance.students', compact(
            'courses',
            'batches',
            'students',
            'selectedCourseId',
            'selectedBatchId',
            'selectedDate',
            'existingAttendance',
            'todayPercent',
            'todayAttendance'
        ));
    }

    public function studentSave(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:Present,Absent,Late',
            'remarks' => 'nullable|array',
        ]);

        return DB::transaction(function() use ($request) {
            $courseId = $request->course_id;
            $batchId = $request->batch_id;
            $date = $request->date;

            foreach ($request->attendance as $studentId => $status) {
                $remarks = $request->remarks[$studentId] ?? null;

                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'date' => $date,
                    ],
                    [
                        'batch_id' => $batchId,
                        'status' => $status,
                        'trainer_id' => auth()->id(),
                        'remarks' => $remarks,
                    ]
                );
            }

            ActivityLog::record(
                'Student Attendance',
                'Attendance',
                $courseId,
                "Saved attendance for Course ID {$courseId} on {$date} for " . count($request->attendance) . " students."
            );

            return back()->with('success', 'Student attendance recorded successfully!');
        });
    }

    // Staff Attendance & Leaves
    public function staffIndex(Request $request)
    {
        $selectedDate = $request->date ?? Carbon::today()->format('Y-m-d');
        $staffMembers = Staff::where('status', 'Active')->with(['attendances' => function($q) use ($selectedDate) {
            $q->whereDate('date', $selectedDate);
        }])->get();

        $leaves = StaffLeave::with('staff')->latest()->take(10)->get();

        $todayPresent = StaffAttendance::whereDate('date', $selectedDate)->whereIn('status', ['Present', 'Half Day'])->count();

        return view('attendance.staff', compact('staffMembers', 'selectedDate', 'leaves', 'todayPresent'));
    }

    public function staffCheckIn(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Half Day,Leave',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'remarks' => 'nullable|string',
        ]);

        StaffAttendance::updateOrCreate(
            ['staff_id' => $request->staff_id, 'date' => $request->date],
            [
                'status' => $request->status,
                'check_in' => $request->check_in ?? Carbon::now()->format('H:i:s'),
                'check_out' => $request->check_out,
                'remarks' => $request->remarks,
            ]
        );

        return back()->with('success', 'Staff attendance updated successfully.');
    }

    public function staffApplyLeave(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        StaffLeave::create([
            'staff_id' => $request->staff_id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $days,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Leave application submitted.');
    }

    public function staffApproveLeave(Request $request, $id)
    {
        $leave = StaffLeave::findOrFail($id);
        $action = $request->action === 'Approve' ? 'Approved' : 'Rejected';

        $leave->update([
            'status' => $action,
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', "Leave request {$action}.");
    }
}
