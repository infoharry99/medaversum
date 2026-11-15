<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_code')->unique();
            $table->string('student_name');
            $table->string('parent_name')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->date('enquiry_date');
            $table->string('source')->default('Walk-in'); // Walk-in, Website, Referral, Social Media, Phone, Event
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_date')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['New', 'Contacted', 'Follow-up', 'Interested', 'Converted', 'Not Interested', 'Closed'])->default('New');
            $table->timestamps();
        });

        Schema::create('counselling_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('session_date');
            $table->text('discussion_notes');
            $table->enum('student_interest_level', ['High', 'Medium', 'Low'])->default('Medium');
            $table->foreignId('recommended_course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('next_step')->nullable();
            $table->timestamps();
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('follow_up_date');
            $table->string('follow_up_type')->default('Call'); // Call, WhatsApp, Meeting, Email
            $table->text('response_notes');
            $table->date('next_follow_up_date')->nullable();
            $table->enum('status', ['Completed', 'Pending', 'Rescheduled'])->default('Completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('counselling_records');
        Schema::dropIfExists('enquiries');
    }
};
