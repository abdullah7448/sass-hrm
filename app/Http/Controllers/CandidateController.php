<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate; // ক্যান্ডিডেট মডেল
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    public function index()
    {
        // লগইন করা ইউজারের (Company Admin) company_id বের করা হচ্ছে
        $companyId = Auth::user()->company_id;

        // শুধুমাত্র ওই কোম্পানির ক্যান্ডিডেটদের ডাটা আনা হচ্ছে
        $candidates = Candidate::where('company_id', $companyId)
                        ->latest()
                        ->get();

        return Inertia::render('Admin/Candidates/Index', [
            'candidates' => $candidates
        ]);
    }
}