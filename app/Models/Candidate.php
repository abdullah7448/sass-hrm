<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\models\CandidateAssessment;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 
        'name', 
        'email', 
        'phone', 
        'position', 
        'iq_score', 
        'department_score', 
        'resume_path', 
        'nid_path', 
        'certificate_path', 
        'status'
    ];
    
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    // public function assessment() 
    // {
    //     // এখানে \App\Models\ যুক্ত করা হয়েছে
    //     return $this->hasOne(\App\Models\CandidateAssessment::class);
    // }

    // এই রিলেশনশিপটি অ্যাড করুন
    public function assessment()
    {
        return $this->hasOne(\App\Models\CandidateAssessment::class, 'candidate_id');
    }
}
