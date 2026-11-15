<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
