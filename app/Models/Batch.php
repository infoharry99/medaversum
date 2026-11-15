<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'batch_code',
        'batch_name',
        'start_date',
        'end_date',
        'schedule_time',
        'days_of_week',
        'max_capacity',
        'trainer_id',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentCourse::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, StudentCourse::class, 'batch_id', 'id', 'id', 'student_id');
    }

    public function getHindiNameDisplayAttribute(): string
    {
        $map = [
            'Morning Alpha Cohort 1' => 'प्रातः कालीन अल्फा बैच १',
            'Evening Theta Cohort 1' => 'सायं कालीन थीटा बैच १',
            'Weekend Mind Camp 1' => 'सप्ताहांत माइंड कैंप १',
        ];

        return $map[$this->batch_name] ?? $this->batch_name;
    }
}
