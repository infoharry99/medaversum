<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'verification_code',
        'student_id',
        'course_id',
        'completion_date',
        'issue_date',
        'authorized_signatory',
        'grade_score',
        'status',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'completion_date' => 'date',
            'issue_date' => 'date',
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
}
