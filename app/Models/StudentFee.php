<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_course_id',
        'total_amount',
        'admission_fee',
        'discount',
        'net_amount',
        'paid_amount',
        'pending_amount',
        'fee_status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentCourse()
    {
        return $this->belongsTo(StudentCourse::class);
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculate(): void
    {
        $paid = $this->payments()->where('status', 'Successful')->sum('amount');
        $this->paid_amount = $paid;
        $this->pending_amount = max(0, $this->net_amount - $paid);

        if ($this->paid_amount >= $this->net_amount) {
            $this->fee_status = 'Paid';
        } elseif ($this->paid_amount > 0) {
            $this->fee_status = 'Partial';
        } else {
            $this->fee_status = 'Pending';
        }

        $this->save();
    }
}
