<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'name',
        'slug',
        'description',
        'image',
        'duration_weeks',
        'total_fees',
        'admission_fees',
        'installment_count',
        'batch_timing',
        'trainer_id',
        'status',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function enrollments()
    {
        return $this->hasMany(StudentCourse::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_courses')->withPivot('batch_id', 'status', 'final_payable_amount');
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function progressRecords()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function getHindiNameDisplayAttribute(): string
    {
        $map = [
            'Subconscious Mind Activation' => 'सबकॉन्शस माइंड एक्टिवेशन',
            'Photographic Memory' => 'फोटोग्राफिक मेमोरी',
            'Brain Waves Mapping' => 'ब्रेन वेव्स मैपिंग',
        ];

        return $map[$this->name] ?? $this->name;
    }
}
