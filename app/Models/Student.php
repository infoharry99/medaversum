<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'enquiry_id',
        'full_name',
        'hindi_name',
        'profile_photo',
        'dob',
        'gender',
        'email',
        'mobile',
        'alternate_contact',
        'address',
        'city',
        'state',
        'pincode',
        'parent_name',
        'parent_contact',
        'parent_relation',
        'parent_email',
        'admission_date',
        'status',
        'counselor_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'admission_date' => 'date',
        ];
    }

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentCourse::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses')
            ->withPivot('id', 'batch_id', 'trainer_id', 'status', 'total_course_fee', 'discount', 'final_payable_amount')
            ->withTimestamps();
    }

    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function progressRecords()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // Helper attributes
    public function getHindiNameDisplayAttribute(): string
    {
        if (!empty($this->attributes['hindi_name'])) {
            return $this->attributes['hindi_name'];
        }

        $map = [
            'Aarav Sharma' => 'आरव शर्मा',
            'Ananya Patel' => 'अनन्या पटेल',
            'Devansh Singhal' => 'देवांश सिंघल',
            'Kunal Sharma' => 'कुणाल शर्मा',
            'Arvind Garg' => 'अरविंद गर्ग',
            'Rameshwar Patel' => 'रामेश्वर पटेल',
            'Pooja Sharma' => 'पूजा शर्मा',
            'Rohit Verma' => 'रोहित वर्मा',
            'Vikramaditya Rao' => 'विक्रमादित्य राव',
            'Dr. Rajeshwar Shastri' => 'डॉ. राजेश्वर शास्त्री',
            'Neha Gupta' => 'नेहा गुप्ता',
            'Sunita Singhal' => 'सुनीता सिंघल',
            'Mahesh Sharma' => 'महेश शर्मा',
            'Mukesh Patel' => 'मुकेश पटेल',
        ];

        return $map[$this->full_name] ?? ($this->attributes['hindi_name'] ?? $this->full_name);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->fees->sum('paid_amount');
    }

    public function getTotalPendingAttribute(): float
    {
        return (float) $this->fees->sum('pending_amount');
    }

    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 100.0;
        $present = $this->attendances()->whereIn('status', ['Present', 'Late'])->count();
        return round(($present / $total) * 100, 1);
    }
}
