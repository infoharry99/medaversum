<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('student_course_id')->constrained('student_courses')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('admission_fee', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('pending_amount', 10, 2);
            $table->enum('fee_status', ['Pending', 'Partial', 'Paid'])->default('Pending');
            $table->timestamps();
        });

        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_fee_id')->constrained('student_fees')->cascadeOnDelete();
            $table->integer('installment_no');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->enum('status', ['Pending', 'Partial', 'Paid', 'Overdue'])->default('Pending');
            $table->date('paid_date')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique(); // PAY-2026-0001
            $table->string('receipt_no')->unique();   // REC-2026-0001
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('student_fee_id')->constrained('student_fees')->cascadeOnDelete();
            $table->foreignId('installment_id')->nullable()->constrained('fee_installments')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['Cash', 'UPI', 'Bank Transfer', 'Card', 'Other'])->default('UPI');
            $table->string('transaction_reference')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Successful', 'Refunded', 'Corrected'])->default('Successful');
            $table->foreignId('original_payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->text('audit_notes')->nullable();
            $table->boolean('is_correction')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('student_fees');
    }
};
