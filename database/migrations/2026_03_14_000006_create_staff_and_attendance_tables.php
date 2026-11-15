<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_code')->unique(); // STF-2026-001
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('profile_photo')->nullable();
            $table->string('email')->unique();
            $table->string('mobile');
            $table->string('designation');
            $table->string('department'); // Administration, Training, Counselling, Accounts, Support
            $table->date('joining_date');
            $table->decimal('salary', 10, 2)->default(0.00);
            $table->text('address')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'On Leave'])->default('Active');
            $table->timestamps();
        });

        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['Present', 'Absent', 'Half Day', 'Leave'])->default('Present');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'date']);
        });

        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('leave_type')->default('Casual'); // Casual, Sick, Earned, Unpaid
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(1);
            $table->text('reason');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->date('date');
            $table->enum('status', ['Present', 'Absent', 'Late'])->default('Present');
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
        Schema::dropIfExists('staff_leaves');
        Schema::dropIfExists('staff_attendance');
        Schema::dropIfExists('staff');
    }
};
