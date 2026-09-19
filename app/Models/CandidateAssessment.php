<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAssessment extends Model
{
    use HasFactory;

    // Mass assignment error বন্ধ করার জন্য
    protected $guarded = [];

    // রিলেশনশিপ (Candidate এর সাথে)
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}