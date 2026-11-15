<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Course;
use App\Models\User;
use App\Models\CounsellingRecord;
use App\Models\FollowUp;
use App\Models\ActivityLog;
use App\Models\Notification;
use Carbon\Carbon;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['course', 'counselor', 'followUps'])->latest('enquiry_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('student_name', 'like', "%{$s}%")
                  ->orWhere('hindi_name', 'like', "%{$s}%")
                  ->orWhere('enquiry_code', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('parent_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('counselor_id')) {
            $query->where('counselor_id', $request->counselor_id);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $enquiries = $query->paginate(12)->withQueryString();
        $courses = Course::where('status', 'Active')->get();
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();

        // Pipeline counts
        $statusCounts = [
            'All' => Enquiry::count(),
            'New' => Enquiry::where('status', 'New')->count(),
            'Contacted' => Enquiry::where('status', 'Contacted')->count(),
            'Follow-up' => Enquiry::where('status', 'Follow-up')->count(),
            'Interested' => Enquiry::where('status', 'Interested')->count(),
            'Converted' => Enquiry::where('status', 'Converted')->count(),
            'Closed' => Enquiry::where('status', 'Closed')->count(),
        ];

        return view('enquiries.index', compact('enquiries', 'courses', 'counselors', 'statusCounts'));
    }

    public function create()
    {
        $courses = Course::where('status', 'Active')->get();
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();

        $lastEnq = Enquiry::latest('id')->first();
        $nextNum = $lastEnq ? ($lastEnq->id + 1) : 1;
        $suggestedCode = sprintf('ENQ-2026-%03d', $nextNum);

        return view('enquiries.create', compact('courses', 'counselors', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enquiry_code' => 'required|string|max:50|unique:enquiries,enquiry_code',
            'student_name' => 'required|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'enquiry_date' => 'required|date',
            'source' => 'required|string|max:50',
            'counselor_id' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'status' => 'required|in:New,Contacted,Follow-up,Interested,Converted,Not Interested,Closed',
        ]);

        // Duplicate check alert
        $existing = Enquiry::where('mobile', $request->mobile)
            ->orWhere(function($q) use ($request) {
                if ($request->filled('email')) {
                    $q->where('email', $request->email);
                }
            })->first();

        if ($existing) {
            return back()->withInput()->with('error', "A lead already exists with mobile/email: {$existing->student_name} ({$existing->enquiry_code}).");
        }

        $enquiry = Enquiry::create($validated);

        ActivityLog::record('Create', 'Enquiries', $enquiry->id, "New lead created: {$enquiry->student_name} ({$enquiry->enquiry_code}).");

        if ($enquiry->follow_up_date) {
            Notification::create([
                'user_id' => $enquiry->counselor_id ?? auth()->id(),
                'title' => 'Follow-up Scheduled',
                'message' => "Follow up scheduled for {$enquiry->student_name} on " . Carbon::parse($enquiry->follow_up_date)->format('d M Y'),
                'type' => 'Follow-up',
                'icon' => 'bi-telephone-outbound',
                'link' => route('enquiries.show', $enquiry->id),
            ]);
        }

        return redirect()->route('enquiries.show', $enquiry->id)->with('success', "Enquiry {$enquiry->enquiry_code} created successfully.");
    }

    public function show($id)
    {
        $enquiry = Enquiry::with(['course', 'counselor', 'counsellingRecords.counselor', 'counsellingRecords.recommendedCourse', 'followUps.user'])->findOrFail($id);
        $courses = Course::where('status', 'Active')->get();
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();

        return view('enquiries.show', compact('enquiry', 'courses', 'counselors'));
    }

    public function edit($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $courses = Course::where('status', 'Active')->get();
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();

        return view('enquiries.edit', compact('enquiry', 'courses', 'counselors'));
    }

    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'enquiry_date' => 'required|date',
            'source' => 'required|string|max:50',
            'counselor_id' => 'nullable|exists:users,id',
            'follow_up_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'status' => 'required|in:New,Contacted,Follow-up,Interested,Converted,Not Interested,Closed',
        ]);

        $enquiry->update($validated);

        ActivityLog::record('Update', 'Enquiries', $enquiry->id, "Enquiry {$enquiry->enquiry_code} updated.");

        return redirect()->route('enquiries.show', $enquiry->id)->with('success', 'Enquiry details updated.');
    }

    public function addCounselling(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $request->validate([
            'session_date' => 'required|date',
            'discussion_notes' => 'required|string',
            'student_interest_level' => 'required|in:High,Medium,Low',
            'recommended_course_id' => 'nullable|exists:courses,id',
            'next_step' => 'nullable|string|max:255',
        ]);

        CounsellingRecord::create([
            'enquiry_id' => $enquiry->id,
            'counselor_id' => auth()->id(),
            'session_date' => $request->session_date,
            'discussion_notes' => $request->discussion_notes,
            'student_interest_level' => $request->student_interest_level,
            'recommended_course_id' => $request->recommended_course_id ?? $enquiry->course_id,
            'next_step' => $request->next_step,
        ]);

        if ($enquiry->status === 'New') {
            $enquiry->update(['status' => 'Contacted']);
        }

        ActivityLog::record('Counselling', 'Enquiries', $enquiry->id, "Recorded counselling session for {$enquiry->student_name}.");

        return back()->with('success', 'Counselling notes recorded successfully.');
    }

    public function addFollowUp(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $request->validate([
            'follow_up_date' => 'required|date',
            'follow_up_type' => 'required|string|max:50',
            'response_notes' => 'required|string',
            'next_follow_up_date' => 'nullable|date',
            'status' => 'required|in:Completed,Pending,Rescheduled',
        ]);

        FollowUp::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => auth()->id(),
            'follow_up_date' => $request->follow_up_date,
            'follow_up_type' => $request->follow_up_type,
            'response_notes' => $request->response_notes,
            'next_follow_up_date' => $request->next_follow_up_date,
            'status' => $request->status,
        ]);

        $enquiry->update([
            'follow_up_date' => $request->next_follow_up_date,
            'status' => 'Follow-up',
        ]);

        ActivityLog::record('Follow-up', 'Enquiries', $enquiry->id, "Logged follow-up for {$enquiry->student_name}.");

        return back()->with('success', 'Follow-up interaction logged.');
    }

    public function destroy($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $name = $enquiry->student_name;
        $enquiry->delete();

        ActivityLog::record('Delete', 'Enquiries', $id, "Deleted enquiry {$name}.");

        return redirect()->route('enquiries.index')->with('success', "Enquiry for {$name} deleted.");
    }
}
