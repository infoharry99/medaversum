<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\User;
use App\Models\StudentCourse;
use App\Models\StudentFee;
use App\Models\FeeInstallment;
use App\Models\Payment;
use App\Models\StudentDocument;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['courses', 'enrollments.batch', 'enrollments.trainer', 'counselor', 'fees'])->latest('admission_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('hindi_name', 'like', "%{$s}%")
                  ->orWhere('student_id', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('parent_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('courses', function($q) use ($request) {
                $q->where('courses.id', $request->course_id);
            });
        }

        if ($request->filled('batch_id')) {
            $query->whereHas('enrollments', function($q) use ($request) {
                $q->where('batch_id', $request->batch_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('balance')) {
            if ($request->balance === 'paid') {
                $query->whereHas('fees', function($q) {
                    $q->where('pending_amount', '<=', 0);
                });
            } elseif ($request->balance === 'pending') {
                $query->whereHas('fees', function($q) {
                    $q->where('pending_amount', '>', 0);
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('admission_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('admission_date', '<=', $request->date_to);
        }

        if ($request->get('export') === 'csv') {
            $studentsToExport = $query->get();
            $filename = 'students_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $callback = function() use ($studentsToExport) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student ID', 'Full Name', 'Hindi Name', 'Mobile', 'City', 'Course', 'Status', 'Pending Fee']);
                foreach ($studentsToExport as $st) {
                    fputcsv($file, [
                        $st->student_id,
                        $st->full_name,
                        $st->hindi_name_display,
                        $st->mobile,
                        $st->city ?? 'Campus',
                        $st->courses->first()->name ?? 'N/A',
                        $st->status,
                        $st->total_pending,
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        $students = $query->paginate(15)->withQueryString();
        $courses = Course::where('status', 'Active')->get();
        $batches = Batch::orderBy('batch_name')->get();

        return view('students.index', compact('students', 'courses', 'batches'));
    }

    public function create(Request $request)
    {
        $courses = Course::with('batches')->where('status', 'Active')->get();
        $batches = Batch::whereIn('status', ['Upcoming', 'Ongoing'])->get();
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();
        $trainers = User::whereHas('role', function($q) {
            $q->where('slug', 'trainer');
        })->get();

        // Calculate next student ID
        $prefix = Setting::get('student_id_prefix', 'MG-2026-');
        $lastStudent = Student::withTrashed()->latest('id')->first();
        $nextNum = $lastStudent ? ($lastStudent->id + 1) : 1;
        $suggestedStudentId = sprintf('%s%04d', $prefix, $nextNum);

        // Pre-fill from enquiry if passed
        $prefill = [];
        if ($request->filled('enquiry_id')) {
            $enq = \App\Models\Enquiry::find($request->enquiry_id);
            if ($enq) {
                $prefill = [
                    'enquiry_id' => $enq->id,
                    'full_name' => $enq->student_name,
                    'parent_name' => $enq->parent_name,
                    'mobile' => $enq->mobile,
                    'email' => $enq->email,
                    'course_id' => $enq->course_id,
                    'counselor_id' => $enq->counselor_id,
                ];
            }
        }

        return view('students.create', compact('courses', 'batches', 'counselors', 'trainers', 'suggestedStudentId', 'prefill'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:50|unique:students,student_id',
            'full_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:20',
            'alternate_contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'parent_name' => 'required|string|max:255',
            'parent_contact' => 'required|string|max:20',
            'parent_relation' => 'required|string|max:50',
            'parent_email' => 'nullable|email|max:255',
            'admission_date' => 'required|date',
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'nullable|exists:batches,id',
            'trainer_id' => 'nullable|exists:users,id',
            'counselor_id' => 'nullable|exists:users,id',
            'discount' => 'nullable|numeric|min:0',
            'initial_payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:Cash,UPI,Bank Transfer,Card,Other',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'enquiry_id' => 'nullable|exists:enquiries,id',
        ]);

        return DB::transaction(function() use ($request, $validated) {
            $student = Student::create([
                'student_id' => $validated['student_id'],
                'enquiry_id' => $validated['enquiry_id'] ?? null,
                'full_name' => $validated['full_name'],
                'dob' => $validated['dob'] ?? null,
                'gender' => $validated['gender'],
                'email' => $validated['email'] ?? null,
                'mobile' => $validated['mobile'],
                'alternate_contact' => $validated['alternate_contact'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'pincode' => $validated['pincode'] ?? null,
                'parent_name' => $validated['parent_name'],
                'parent_contact' => $validated['parent_contact'],
                'parent_relation' => $validated['parent_relation'],
                'parent_email' => $validated['parent_email'] ?? null,
                'admission_date' => $validated['admission_date'],
                'status' => 'Active',
                'counselor_id' => $validated['counselor_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $course = Course::findOrFail($validated['course_id']);
            $totalFee = $course->total_fees;
            $discount = floatval($validated['discount'] ?? 0);
            $netPayable = max(0, $totalFee - $discount);

            // Enrollment
            $studentCourse = StudentCourse::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'batch_id' => $validated['batch_id'] ?? null,
                'trainer_id' => $validated['trainer_id'] ?? $course->trainer_id,
                'enrollment_date' => $validated['admission_date'],
                'status' => 'Active',
                'total_course_fee' => $totalFee,
                'discount' => $discount,
                'final_payable_amount' => $netPayable,
            ]);

            // Fee record
            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'student_course_id' => $studentCourse->id,
                'total_amount' => $totalFee,
                'admission_fee' => $course->admission_fees,
                'discount' => $discount,
                'net_amount' => $netPayable,
                'paid_amount' => 0.00,
                'pending_amount' => $netPayable,
                'fee_status' => 'Pending',
            ]);

            // Installment schedules
            $installmentCount = max(1, $course->installment_count);
            $admFee = min($netPayable, $course->admission_fees);
            $remainingFee = max(0, $netPayable - $admFee);

            // 1st installment (Admission fee)
            FeeInstallment::create([
                'student_fee_id' => $studentFee->id,
                'installment_no' => 1,
                'due_date' => $validated['admission_date'],
                'amount' => $admFee,
                'paid_amount' => 0.00,
                'status' => 'Pending',
            ]);

            // Subsequent installments
            if ($installmentCount > 1 && $remainingFee > 0) {
                $subCount = $installmentCount - 1;
                $perSub = round($remainingFee / $subCount, 2);
                for ($i = 2; $i <= $installmentCount; $i++) {
                    FeeInstallment::create([
                        'student_fee_id' => $studentFee->id,
                        'installment_no' => $i,
                        'due_date' => Carbon::parse($validated['admission_date'])->addMonths($i - 1),
                        'amount' => $perSub,
                        'paid_amount' => 0.00,
                        'status' => 'Pending',
                    ]);
                }
            }

            // Initial Payment if provided
            $initialPay = floatval($validated['initial_payment'] ?? 0);
            if ($initialPay > 0) {
                $lastPayment = Payment::latest('id')->first();
                $payNum = $lastPayment ? ($lastPayment->id + 1) : 1;
                $payCode = sprintf('PAY-2026-%04d', $payNum);
                $recNo = sprintf('REC-2026-%04d', 1000 + $payNum);

                Payment::create([
                    'payment_code' => $payCode,
                    'receipt_no' => $recNo,
                    'student_id' => $student->id,
                    'student_fee_id' => $studentFee->id,
                    'installment_id' => null,
                    'amount' => $initialPay,
                    'payment_date' => $validated['admission_date'],
                    'payment_method' => $validated['payment_method'] ?? 'UPI',
                    'transaction_reference' => $validated['transaction_reference'] ?? null,
                    'remarks' => 'Admission / Initial Payment',
                    'recorded_by' => auth()->id(),
                    'status' => 'Successful',
                ]);

                $studentFee->recalculate();
            }

            // If converted from enquiry, mark enquiry converted
            if (!empty($validated['enquiry_id'])) {
                $enq = \App\Models\Enquiry::find($validated['enquiry_id']);
                if ($enq) {
                    $enq->update(['status' => 'Converted']);
                }
            }

            ActivityLog::record(
                'Admission',
                'Students',
                $student->id,
                "New Student Registered: {$student->full_name} ({$student->student_id}) in course {$course->name}."
            );

            Notification::create([
                'user_id' => null,
                'title' => 'New Admission Confirmed',
                'message' => "Student {$student->full_name} ({$student->student_id}) enrolled in {$course->name}.",
                'type' => 'Admission',
                'icon' => 'bi-person-check-fill',
                'link' => route('students.show', $student->id),
            ]);

            return redirect()->route('students.show', $student->id)
                ->with('success', "Student {$student->full_name} successfully registered with ID: {$student->student_id}!");
        });
    }

    public function show($id)
    {
        $student = Student::with([
            'enquiry',
            'counselor',
            'courses.trainer',
            'courses.batches',
            'enrollments.course',
            'enrollments.batch',
            'fees.installments',
            'fees.payments',
            'payments.recordedBy',
            'attendances.course',
            'progressRecords.course',
            'documents',
            'mediaFiles',
            'certificates.course'
        ])->findOrFail($id);

        $courses = Course::where('status', 'Active')->get();
        $batches = Batch::whereIn('status', ['Upcoming', 'Ongoing'])->get();

        return view('students.show', compact('student', 'courses', 'batches'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $counselors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['counselor', 'admin', 'super-admin']);
        })->get();

        return view('students.edit', compact('student', 'counselors'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'mobile' => 'required|string|max:20',
            'alternate_contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'parent_name' => 'required|string|max:255',
            'parent_contact' => 'required|string|max:20',
            'parent_relation' => 'required|string|max:50',
            'parent_email' => 'nullable|email|max:255',
            'status' => 'required|in:Active,Completed,On Leave,Dropped',
            'counselor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $student->update($validated);

        ActivityLog::record('Update', 'Students', $student->id, "Student record {$student->student_id} updated.");

        return redirect()->route('students.show', $student->id)->with('success', 'Student details updated successfully.');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->full_name;
        $studentId = $student->student_id;

        $student->delete(); // Soft delete

        ActivityLog::record('Delete', 'Students', $id, "Student {$name} ({$studentId}) soft deleted.");

        return redirect()->route('students.index')->with('success', "Student {$name} has been removed.");
    }

    public function downloadPdf($id)
    {
        $student = Student::with([
            'courses',
            'enrollments.course',
            'enrollments.batch',
            'fees.installments',
            'fees.payments',
            'attendances',
            'progressRecords'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('students.pdf_profile', compact('student'));
        return $pdf->download("Student_Profile_{$student->student_id}.pdf");
    }

    public function uploadDocument(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'document_title' => 'required|string|max:255',
            'document_type' => 'nullable|string|max:100',
            'document_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('student_docs/' . $student->id, $fileName, 'public');

        StudentDocument::create([
            'student_id' => $student->id,
            'document_title' => $request->document_title,
            'document_type' => $request->document_type ?? 'Other',
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
        ]);

        ActivityLog::record('Upload Document', 'Students', $student->id, "Uploaded document '{$request->document_title}' for {$student->student_id}.");

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function deleteDocument($id, $docId)
    {
        $document = StudentDocument::where('student_id', $id)->findOrFail($docId);
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
