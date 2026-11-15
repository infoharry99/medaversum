<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'receipt_no',
        'student_id',
        'student_fee_id',
        'installment_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'remarks',
        'recorded_by',
        'status',
        'original_payment_id',
        'audit_notes',
        'is_correction',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'is_correction' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function installment()
    {
        return $this->belongsTo(FeeInstallment::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function originalPayment()
    {
        return $this->belongsTo(Payment::class, 'original_payment_id');
    }

    public function corrections()
    {
        return $this->hasMany(Payment::class, 'original_payment_id');
    }
}
