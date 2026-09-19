<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Candidate;
use Inertia\Inertia;

class FunnelController extends Controller
{
    // ফানেলের প্রথম পেজ (Registration) দেখাবে
    public function start(Company $company)
    {
        return Inertia::render('Funnel/Step1Registration', [
            'company' => $company
        ]);
    }

    // ক্যান্ডিডেটের ডাটা সেভ করবে
    public function register(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'required|string',
            'position' => 'required|string',
        ]);

        // নতুন ক্যান্ডিডেট তৈরি হচ্ছে এবং কোম্পানির ID যুক্ত হচ্ছে
        $candidate = Candidate::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'status' => 'Pending', // ডিফল্ট স্ট্যাটাস
        ]);

        // আপাতত আমরা রেজিস্ট্রেশন শেষেই সাকসেস মেসেজ দেখাচ্ছি। 
        // পরে এখান থেকে Step 2 (IQ Test) এ রিডাইরেক্ট করবো।
        return redirect()->back()->with('success', 'Registration successful!');
    }
}