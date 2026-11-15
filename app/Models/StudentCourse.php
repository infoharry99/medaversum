<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'batch_id',
        'trainer_id',
        'enrollment_date',
        'status',
        'total_course_fee',
        'discount',
        'final_payable_amount',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function fee()
    {
        return $this->hasOne(StudentFee::class, 'student_course_id');
    }
}
