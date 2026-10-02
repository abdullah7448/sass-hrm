<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'note',
        'ip_address',
        'late_reason',
        'is_late_approved',
        'working_minutes',
    ];

    // Employee Relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Company Relation
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}