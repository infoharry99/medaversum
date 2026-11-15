<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;

    protected $table = 'student_progress';

    protected $fillable = [
        'student_id',
        'course_id',
        'batch_id',
        'assessment_date',
        'assessment_type',
        'performance_score',
        'trainer_id',
        'trainer_remarks',
        'improvement_notes',
        'next_assessment_date',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'next_assessment_date' => 'date',
            'performance_score' => 'float',
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
}
