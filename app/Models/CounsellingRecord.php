<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounsellingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_id',
        'counselor_id',
        'session_date',
        'discussion_notes',
        'student_interest_level',
        'recommended_course_id',
        'next_step',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function recommendedCourse()
    {
        return $this->belongsTo(Course::class, 'recommended_course_id');
    }
}
