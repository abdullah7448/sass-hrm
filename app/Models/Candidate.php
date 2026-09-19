<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\models\CandidateAssessment;

class Candidate extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function assessment() {
        return $this->hasOne(CandidateAssessment::class);
    }
}
