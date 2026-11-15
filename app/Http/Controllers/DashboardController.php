<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Enquiry;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Staff;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use App\Models\FollowUp;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // High Level Metrics
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'Active')->count();
        $newAdmissionsThisMonth = Student::whereMonth('admission_date', $now->month)
            ->whereYear('admission_date', $now->year)
            ->count();
        
        $totalEnquiries = Enquiry::count();
        $totalRevenue = Payment::where('status', 'Successful')->sum('amount');
        $pendingFees = StudentFee::sum('pending_amount');
        $activeCourses = Course::where('status', 'Active')->count();
        $totalStaff = Staff::where('status', 'Active')->count();

        // Today's collections
        $todayCollections = Payment::whereDate('payment_date', $today)
            ->where('status', 'Successful')
            ->sum('amount');

        // Today's attendance
        $todayStudentTotal = StudentAttendance::whereDate('date', $today)->count();
        $todayStudentPresent = StudentAttendance::whereDate('date', $today)->whereIn('status', ['Present', 'Late'])->count();
        $todayStudentAttendanceRate = $todayStudentTotal > 0 ? round(($todayStudentPresent / $todayStudentTotal) * 100, 1) : 0;

        $todayStaffPresent = StaffAttendance::whereDate('date', $today)->whereIn('status', ['Present', 'Half Day'])->count();

        // Recent Admissions
        $recentAdmissions = Student::with(['courses', 'counselor'])
            ->latest('admission_date')
            ->take(5)
            ->get();

        // Recent Payments
        $recentPayments = Payment::with(['student'])
            ->latest('payment_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Recent Activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        // Upcoming Batches
        $upcomingBatches = Batch::with(['course', 'trainer'])
            ->whereIn('status', ['Upcoming', 'Ongoing'])
            ->orderBy('start_date')
            ->take(4)
            ->get();

        // Upcoming Follow-ups
        $upcomingFollowUps = Enquiry::with(['counselor', 'course'])
            ->whereNotNull('follow_up_date')
            ->whereIn('status', ['Follow-up', 'Interested', 'New'])
            ->orderBy('follow_up_date')
            ->take(5)
            ->get();

        // Chart 1: Monthly Admissions (Last 6 Months)
        $monthlyAdmissionsLabels = [];
        $monthlyAdmissionsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $label = $monthDate->format('M Y');
            $count = Student::whereMonth('admission_date', $monthDate->month)
                ->whereYear('admission_date', $monthDate->year)
                ->count();
            
            $monthlyAdmissionsLabels[] = $label;
            $monthlyAdmissionsData[] = $count;
        }

        // Chart 2: Monthly Revenue (Last 6 Months)
        $monthlyRevenueLabels = [];
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $label = $monthDate->format('M Y');
            $sum = Payment::where('status', 'Successful')
                ->whereMonth('payment_date', $monthDate->month)
                ->whereYear('payment_date', $monthDate->year)
                ->sum('amount');
            
            $monthlyRevenueLabels[] = $label;
            $monthlyRevenueData[] = (float) $sum;
        }

        // Chart 3: Course-wise student distribution
        $courseDistribution = Course::withCount('students')->get();
        $courseLabels = $courseDistribution->pluck('name')->toArray();
        $courseData = $courseDistribution->pluck('students_count')->toArray();

        return view('dashboard.index', compact(
            'totalStudents',
            'activeStudents',
            'newAdmissionsThisMonth',
            'totalEnquiries',
            'totalRevenue',
            'pendingFees',
            'activeCourses',
            'totalStaff',
            'todayCollections',
            'todayStudentAttendanceRate',
            'todayStaffPresent',
            'recentAdmissions',
            'recentPayments',
            'recentActivities',
            'upcomingBatches',
            'upcomingFollowUps',
            'monthlyAdmissionsLabels',
            'monthlyAdmissionsData',
            'monthlyRevenueLabels',
            'monthlyRevenueData',
            'courseLabels',
            'courseData'
        ));
    }
}
