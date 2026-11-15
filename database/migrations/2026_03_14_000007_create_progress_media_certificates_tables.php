<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->date('assessment_date');
            $table->string('assessment_type'); // Subconscious Mind Activation, Photographic Memory, Brain Waves Mapping, Focus, Recall Speed
            $table->decimal('performance_score', 5, 2); // 0.00 to 100.00
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('trainer_remarks')->nullable();
            $table->text('improvement_notes')->nullable();
            $table->date('next_assessment_date')->nullable();
            $table->timestamps();
        });

        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Student Photos, Student Videos, Classroom Activities, Events, Workshops, Certificates, Course Materials
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->default('image'); // image, video, document, other
            $table->bigInteger('file_size')->default(0);
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique(); // MG-CERT-2026-001
            $table->string('verification_code')->unique();   // 10-char alphanumeric
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->date('completion_date');
            $table->date('issue_date');
            $table->string('authorized_signatory')->default('Program Director & Master Trainer');
            $table->string('grade_score')->default('A+ (Distinction)');
            $table->enum('status', ['Issued', 'Revoked'])->default('Issued');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('media_files');
        Schema::dropIfExists('student_progress');
    }
};
