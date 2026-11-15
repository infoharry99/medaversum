<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $certificates = Certificate::with(['student', 'course'])->latest()->paginate(12);
        $students = Student::where('status', 'Active')->get();
        $courses = Course::where('status', 'Active')->get();

        return view('certificates.index', compact('certificates', 'students', 'courses'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'completion_date' => 'required|date',
            'grade_score' => 'required|string|max:100',
            'authorized_signatory' => 'required|string|max:255',
        ]);

        $lastCert = Certificate::latest('id')->first();
        $nextNum = $lastCert ? ($lastCert->id + 1) : 1;
        $certNumber = sprintf('MG-CERT-2026-%03d', $nextNum);
        $verificationCode = Str::upper(Str::random(10));

        $cert = Certificate::create([
            'certificate_number' => $certNumber,
            'verification_code' => $verificationCode,
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'completion_date' => $request->completion_date,
            'issue_date' => now(),
            'authorized_signatory' => $request->authorized_signatory,
            'grade_score' => $request->grade_score,
            'status' => 'Issued',
        ]);

        ActivityLog::record(
            'Generate Certificate',
            'Certificates',
            $cert->id,
            "Issued certificate {$cert->certificate_number} to Student ID {$request->student_id}."
        );

        return redirect()->route('certificates.download', $cert->id)
            ->with('success', "Certificate {$certNumber} generated successfully!");
    }

    public function download($id)
    {
        $certificate = Certificate::with(['student', 'course'])->findOrFail($id);

        $pdf = Pdf::loadView('certificates.pdf_certificate', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Certificate_{$certificate->certificate_number}.pdf");
    }

    public function verify($code)
    {
        $certificate = Certificate::with(['student', 'course'])
            ->where('verification_code', $code)
            ->first();

        return view('certificates.verify', compact('certificate', 'code'));
    }
}
