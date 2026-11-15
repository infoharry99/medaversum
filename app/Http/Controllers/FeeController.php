<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeInstallment;
use App\Models\Payment;
use App\Models\Course;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentFee::with(['student.courses', 'installments', 'payments'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', function($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('student_id', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%");
            });
        }

        if ($request->filled('fee_status')) {
            $query->where('fee_status', $request->fee_status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $fees = $query->paginate(12)->withQueryString();

        // Financial totals
        $totalPayable = StudentFee::sum('net_amount');
        $totalCollected = Payment::where('status', 'Successful')->sum('amount');
        $totalPending = StudentFee::sum('pending_amount');
        $todayCollected = Payment::where('status', 'Successful')->whereDate('payment_date', Carbon::today())->sum('amount');

        // Recent payments list for the right pane or modal
        $recentPayments = Payment::with(['student', 'recordedBy'])->latest('id')->take(10)->get();

        $students = Student::with('fees')->where('status', 'Active')->get();

        return view('fees.index', compact('fees', 'totalPayable', 'totalCollected', 'totalPending', 'todayCollected', 'recentPayments', 'students'));
    }

    public function collectPayment(Request $request)
    {
        $validated = $request->validate([
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:Cash,UPI,Bank Transfer,Card,Other',
            'transaction_reference' => 'nullable|string|max:100',
            'installment_id' => 'nullable|exists:fee_installments,id',
            'remarks' => 'nullable|string',
        ]);

        return DB::transaction(function() use ($validated) {
            $studentFee = StudentFee::with('student')->findOrFail($validated['student_fee_id']);
            $student = $studentFee->student;

            // Generate unique payment code and receipt number
            $lastPayment = Payment::latest('id')->first();
            $payNum = $lastPayment ? ($lastPayment->id + 1) : 1;
            $prefix = Setting::get('receipt_prefix', 'REC-2026-');

            $payCode = sprintf('PAY-2026-%04d', $payNum);
            $recNo = sprintf('%s%04d', $prefix, 1000 + $payNum);

            $payment = Payment::create([
                'payment_code' => $payCode,
                'receipt_no' => $recNo,
                'student_id' => $student->id,
                'student_fee_id' => $studentFee->id,
                'installment_id' => $validated['installment_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'remarks' => $validated['remarks'] ?? 'Fee installment payment',
                'recorded_by' => auth()->id(),
                'status' => 'Successful',
            ]);

            // Update installment if linked
            if (!empty($validated['installment_id'])) {
                $inst = FeeInstallment::find($validated['installment_id']);
                if ($inst) {
                    $inst->paid_amount += $validated['amount'];
                    if ($inst->paid_amount >= $inst->amount) {
                        $inst->status = 'Paid';
                        $inst->paid_date = $validated['payment_date'];
                    } else {
                        $inst->status = 'Partial';
                    }
                    $inst->save();
                }
            }

            // Recalculate fee status
            $studentFee->recalculate();

            // Activity Log
            ActivityLog::record(
                'Fee Collection',
                'Fees',
                $payment->id,
                "Collected ₹" . number_format($payment->amount, 2) . " from {$student->full_name} ({$student->student_id}) via {$payment->payment_method}."
            );

            // In-app Notification
            Notification::create([
                'user_id' => null,
                'title' => 'Fee Collected',
                'message' => "Payment of ₹" . number_format($payment->amount, 2) . " recorded for {$student->full_name} ({$recNo}).",
                'type' => 'Fee Collection',
                'icon' => 'bi-wallet2',
                'link' => route('fees.index'),
            ]);

            return redirect()->route('fees.receiptPdf', $payment->id)
                ->with('success', "Payment of ₹" . number_format($payment->amount, 2) . " recorded. Receipt {$recNo} created!");
        });
    }

    public function downloadReceipt($id)
    {
        $payment = Payment::with(['student.courses', 'studentFee.studentCourse.course', 'recordedBy'])->findOrFail($id);
        $settingInstitute = Setting::get('institute_name', 'Medhaversum Gurukulam');
        $settingTagline = Setting::get('institute_tagline', 'A Universe of Infinite Potential');
        $settingAddress = Setting::get('address', 'New Delhi, India');
        $settingPhone = Setting::get('contact_phone', '+91 98765 43210');
        $settingEmail = Setting::get('contact_email', 'contact@medhaversum.com');
        $settingGst = Setting::get('gst_number', '07AAAAA0000A1Z5');

        $pdf = Pdf::loadView('fees.pdf_receipt', compact(
            'payment',
            'settingInstitute',
            'settingTagline',
            'settingAddress',
            'settingPhone',
            'settingEmail',
            'settingGst'
        ));

        return $pdf->download("Receipt_{$payment->receipt_no}.pdf");
    }

    public function viewReceiptHtml($id)
    {
        $payment = Payment::with(['student.courses', 'studentFee.studentCourse.course', 'recordedBy'])->findOrFail($id);
        return view('fees.receipt_view', compact('payment'));
    }

    public function correctPayment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
            'action_type' => 'required|in:Refund,Correction',
        ]);

        return DB::transaction(function() use ($request, $id) {
            $payment = Payment::with('studentFee.student')->findOrFail($id);

            $oldStatus = $payment->status;
            $payment->status = $request->action_type === 'Refund' ? 'Refunded' : 'Corrected';
            $payment->audit_notes = "Adjusted on " . date('Y-m-d H:i') . " by " . auth()->user()->name . ". Reason: " . $request->reason;
            $payment->is_correction = true;
            $payment->save();

            // Recalculate fee
            $payment->studentFee->recalculate();

            ActivityLog::record(
                'Payment Adjustment',
                'Fees',
                $payment->id,
                "Payment {$payment->receipt_no} (₹{$payment->amount}) updated to {$payment->status}. Reason: {$request->reason}"
            );

            return back()->with('success', "Payment {$payment->receipt_no} marked as {$payment->status} with audit trail.");
        });
    }
}
