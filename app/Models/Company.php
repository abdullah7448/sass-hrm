<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function candidates() {
        return $this->hasMany(Candidate::class);
    }
}
