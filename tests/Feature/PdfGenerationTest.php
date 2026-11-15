<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Certificate;

class PdfGenerationTest extends TestCase
{
    public function test_receipt_pdf_download(): void
    {
        $admin = User::where('email', 'admin@medhaversum.com')->first();
        $payment = Payment::first();

        $response = $this->actingAs($admin)->get("/fees/receipt/{$payment->id}/pdf");
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_student_profile_pdf_download(): void
    {
        $admin = User::where('email', 'admin@medhaversum.com')->first();
        $student = Student::first();

        $response = $this->actingAs($admin)->get("/students/{$student->id}/pdf");
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_certificate_pdf_download(): void
    {
        $admin = User::where('email', 'admin@medhaversum.com')->first();
        $cert = Certificate::first();

        $response = $this->actingAs($admin)->get("/certificates/{$cert->id}/download");
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
