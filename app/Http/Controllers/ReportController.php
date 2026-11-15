<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Enquiry;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\FeeInstallment;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use App\Models\StudentAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->type ?? 'financial';
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::now()->format('Y-m-d');
        $courseId = $request->course_id;

        $courses = Course::where('status', 'Active')->get();
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();

        $data = [];

        if ($reportType === 'financial') {
            // Financial report data
            $paymentsQuery = Payment::with(['student.courses', 'recordedBy'])
                ->where('status', 'Successful')
                ->whereBetween('payment_date', [$dateFrom, $dateTo]);

            if ($courseId) {
                $paymentsQuery->whereHas('student.courses', function($q) use ($courseId) {
                    $q->where('courses.id', $courseId);
                });
            }

            $payments = $paymentsQuery->latest('payment_date')->get();
            $totalCollected = $payments->sum('amount');
            $totalPending = StudentFee::sum('pending_amount');

            // Payment method breakdown
            $methodBreakdown = Payment::where('status', 'Successful')
                ->whereBetween('payment_date', [$dateFrom, $dateTo])
                ->selectRaw('payment_method, SUM(amount) as total')
                ->groupBy('payment_method')
                ->pluck('total', 'payment_method')
                ->toArray();

            $data = compact('payments', 'totalCollected', 'totalPending', 'methodBreakdown');
        } elseif ($reportType === 'admissions') {
            // Admissions report data
            $admissionsQuery = Student::with(['courses', 'counselor'])
                ->whereBetween('admission_date', [$dateFrom, $dateTo]);

            if ($courseId) {
                $admissionsQuery->whereHas('courses', function($q) use ($courseId) {
                    $q->where('courses.id', $courseId);
                });
            }

            $admissions = $admissionsQuery->latest('admission_date')->get();

            // Course-wise count
            $courseWiseAdmissions = Course::withCount(['students' => function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('students.admission_date', [$dateFrom, $dateTo]);
            }])->get();

            // Counselor-wise count
            $counselorWiseAdmissions = User::whereHas('role', function($q) {
                $q->where('slug', 'counselor');
            })->withCount(['assignedStudents' => function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('admission_date', [$dateFrom, $dateTo]);
            }])->get();

            $data = compact('admissions', 'courseWiseAdmissions', 'counselorWiseAdmissions');
        } elseif ($reportType === 'attendance') {
            // Student & Staff Attendance report
            $attendanceRecords = StudentAttendance::with(['student', 'course'])
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->when($courseId, function($q) use ($courseId) {
                    $q->where('course_id', $courseId);
                })
                ->latest('date')
                ->get();

            $staffAttendance = StaffAttendance::with('staff')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->latest('date')
                ->get();

            $data = compact('attendanceRecords', 'staffAttendance');
        } elseif ($reportType === 'staff') {
            // Staff & Leaves Report
            $staffList = Staff::with(['attendances', 'leaves'])->get();
            $leaves = StaffLeave::with('staff')->whereBetween('start_date', [$dateFrom, $dateTo])->get();
            $totalSalary = Staff::where('status', 'Active')->sum('salary');

            $data = compact('staffList', 'leaves', 'totalSalary');
        }

        return view('reports.index', compact('reportType', 'dateFrom', 'dateTo', 'courseId', 'courses', 'counselors', 'data'));
    }

    public function exportCsv(Request $request)
    {
        $type = $request->type ?? 'financial';
        $fileName = "Medaversum_{$type}_Report_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'financial') {
                fputcsv($handle, ['Receipt No', 'Payment Code', 'Student ID', 'Student Name', 'Amount (INR)', 'Payment Date', 'Method', 'Reference']);
                $payments = Payment::with('student')->where('status', 'Successful')->get();
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->receipt_no,
                        $p->payment_code,
                        $p->student->student_id ?? 'N/A',
                        $p->student->full_name ?? 'N/A',
                        $p->amount,
                        $p->payment_date ? $p->payment_date->format('Y-m-d') : '',
                        $p->payment_method,
                        $p->transaction_reference ?? ''
                    ]);
                }
            } elseif ($type === 'admissions') {
                fputcsv($handle, ['Student ID', 'Full Name', 'Admission Date', 'Gender', 'Mobile', 'Email', 'Parent Name', 'Parent Contact', 'Course', 'Status']);
                $students = Student::with('courses')->get();
                foreach ($students as $s) {
                    fputcsv($handle, [
                        $s->student_id,
                        $s->full_name,
                        $s->admission_date ? $s->admission_date->format('Y-m-d') : '',
                        $s->gender,
                        $s->mobile,
                        $s->email ?? '',
                        $s->parent_name,
                        $s->parent_contact,
                        $s->courses->first()->name ?? 'N/A',
                        $s->status
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
