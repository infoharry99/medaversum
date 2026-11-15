<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique(); // MG-2026-0001
            $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->string('full_name');
            $table->string('profile_photo')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('alternate_contact')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('parent_name');
            $table->string('parent_contact');
            $table->string('parent_relation')->default('Father');
            $table->string('parent_email')->nullable();
            $table->date('admission_date');
            $table->enum('status', ['Active', 'Completed', 'On Leave', 'Dropped'])->default('Active');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('enrollment_date');
            $table->enum('status', ['Active', 'Completed', 'Dropped'])->default('Active');
            $table->decimal('total_course_fee', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('final_payable_amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('document_title');
            $table->string('document_type')->nullable(); // Aadhaar, Birth Certificate, Marksheet, etc.
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_courses');
        Schema::dropIfExists('students');
    }
};
