<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staff_code',
        'user_id',
        'name',
        'profile_photo',
        'email',
        'mobile',
        'designation',
        'department',
        'joining_date',
        'salary',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(StaffLeave::class);
    }

    public function getHindiNameDisplayAttribute(): string
    {
        $map = [
            'Dr. Rajeshwar Shastri' => 'डॉ. राजेश्वर शास्त्री',
            'Pooja Sharma' => 'पूजा शर्मा',
            'Rohit Verma' => 'रोहित वर्मा',
            'Vikramaditya Rao' => 'विक्रमादित्य राव',
        ];

        return $map[$this->name] ?? $this->name;
    }
}
