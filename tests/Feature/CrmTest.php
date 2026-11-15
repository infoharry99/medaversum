<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enquiry;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Certificate;

class CrmTest extends TestCase
{
    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::where('email', 'admin@medhaversum.com')->first();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('MEDHAVERSUM');
    }

    public function test_users_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@medhaversum.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_dashboard_renders_with_real_metrics(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Executive Dashboard');
        $response->assertSee('Total Students');
        $response->assertSee('Total Revenue');
    }

    public function test_courses_and_batches_screens(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('Subconscious Mind Activation', false);

        $response = $this->actingAs($this->superAdmin)->get('/batches');
        $response->assertStatus(200);
        $response->assertSee('Batch List', false);
    }

    public function test_students_directory_and_profile(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/students');
        $response->assertStatus(200);
        $response->assertSee('Student List', false);

        $student = Student::first();
        $response = $this->actingAs($this->superAdmin)->get("/students/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee($student->full_name, false);
        $response->assertSee('Personal Info', false);
    }

    public function test_enquiries_pipeline_and_conversion(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/enquiries');
        $response->assertStatus(200);
        $response->assertSee('Enquiry List', false);

        $enquiry = Enquiry::first();
        $response = $this->actingAs($this->superAdmin)->get("/enquiries/{$enquiry->id}");
        $response->assertStatus(200);
        $response->assertSee($enquiry->student_name, false);
    }

    public function test_fees_and_payments(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/fees');
        $response->assertStatus(200);
        $response->assertSee('Student Fee Ledgers', false);

        $payment = Payment::first();
        $response = $this->actingAs($this->superAdmin)->get("/fees/receipt/{$payment->id}");
        $response->assertStatus(200);
        $response->assertSee($payment->receipt_no, false);
    }

    public function test_attendance_modules(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/attendance/students?course_id=1');
        $response->assertStatus(200);
        $response->assertSee('Student Attendance Management', false);

        $response = $this->actingAs($this->superAdmin)->get('/attendance/staff');
        $response->assertStatus(200);
        $response->assertSee('Staff Attendance & Leave Management', false);
    }

    public function test_progress_and_certificates(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/progress');
        $response->assertStatus(200);
        $response->assertSee('Student Progress & Cognitive Assessments', false);

        $response = $this->actingAs($this->superAdmin)->get('/certificates');
        $response->assertStatus(200);
        $response->assertSee('Certificate Management', false);

        $cert = Certificate::first();
        $response = $this->get("/verify/{$cert->verification_code}");
        $response->assertStatus(200);
        $response->assertSee('Authentic & Verified Certificate', false);
    }

    public function test_staff_and_media(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/staff');
        $response->assertStatus(200);
        $response->assertSee('Staff List', false);

        $response = $this->actingAs($this->superAdmin)->get('/media');
        $response->assertStatus(200);
        $response->assertSee('Centralized Media Library', false);
    }

    public function test_reports_and_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Reports & Business Intelligence', false);

        $response = $this->actingAs($this->superAdmin)->get('/roles');
        $response->assertStatus(200);
        $response->assertSee('Roles & Module Permissions', false);

        $response = $this->actingAs($this->superAdmin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('System Settings & Audit Log', false);
    }
}
