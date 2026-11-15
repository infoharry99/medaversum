<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('duration_weeks')->default(12);
            $table->decimal('total_fees', 10, 2)->default(0.00);
            $table->decimal('admission_fees', 10, 2)->default(0.00);
            $table->integer('installment_count')->default(3);
            $table->string('batch_timing')->nullable();
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('batch_code')->unique();
            $table->string('batch_name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('schedule_time')->nullable();
            $table->string('days_of_week')->nullable();
            $table->integer('max_capacity')->default(25);
            $table->foreignId('trainer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'])->default('Upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
        Schema::dropIfExists('courses');
    }
};
