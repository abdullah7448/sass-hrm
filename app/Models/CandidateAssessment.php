<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateAssessment extends Model
{
    use HasFactory;

    protected $fillable = ['candidate_id', 'answers'];

    // রিলেশন: একটি Assessment একজন ক্যান্ডিডেটের হয়
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}