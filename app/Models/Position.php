<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'title', 'questions', 'is_active'];

    protected $casts = [
        'questions' => 'array', 
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}