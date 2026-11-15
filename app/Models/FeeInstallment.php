<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_id',
        'installment_no',
        'due_date',
        'amount',
        'paid_amount',
        'status',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'installment_id');
    }
}
