<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_code',
        'student_name',
        'hindi_name',
        'parent_name',
        'mobile',
        'email',
        'course_id',
        'enquiry_date',
        'source',
        'counselor_id',
        'follow_up_date',
        'remarks',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function counsellingRecords()
    {
        return $this->hasMany(CounsellingRecord::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

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
            'Siddharth Joshi' => 'सिद्धार्थ जोशी',
            'Isha Agrawal' => 'ईशा अग्रवाल',
            'Tanmay Bakshi' => 'तन्मय बख्शी',
            'Pooja Sharma' => 'पूजा शर्मा',
        ];

        return $map[$this->student_name] ?? ($this->attributes['hindi_name'] ?? $this->student_name);
    }
}
