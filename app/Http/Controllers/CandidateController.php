<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
public function index()
    {
        // বর্তমানে লগ-ইন করা অ্যাডমিনের কোম্পানি আইডি নিচ্ছি
        $companyId = Auth::user()->company_id;

        // শুধুমাত্র এই কোম্পানির ক্যান্ডিডেটদের ডাটা ফিল্টার করা হলো
        $candidates = Candidate::with('assessment')
            ->where('company_id', $companyId)
            ->latest()
            ->get();
        
        // শুধুমাত্র এই কোম্পানির প্রশ্নগুলো ফিল্টার করা হলো
        $questions = Question::where('company_id', $companyId)->get(); 

        return Inertia::render('Admin/Candidates/Index', [ 
            'candidates' => $candidates,
            'questions' => $questions
        ]);
    }

    public function approve(Candidate $candidate)
    {
        // ১. ক্যান্ডিডেটের স্ট্যাটাস আপডেট করা
        $candidate->update(['status' => 'Approved']);

        // ২. নতুন Employee একাউন্ট তৈরি করা (ইউজার টেবিলে)
        $employee = User::firstOrCreate(
            ['email' => $candidate->email], // ইমেইল চেক করবে আগে থেকে আছে কিনা
            [
                'name' => $candidate->name,
                'company_id' => $candidate->company_id,
                'phone' => $candidate->phone,
                'password' => Hash::make('password123'), // ডিফল্ট পাসওয়ার্ড
                'is_active' => true,
            ]
        );

        // ৩. তাকে Employee রোল দিয়ে দেওয়া
        $employee->assignRole('Employee');

        return redirect()->back()->with('success', 'Candidate approved and added as Employee successfully!');
    }

    // ==========================================
    // Update Candidate Status (Interview, Reject, etc.)
    // ==========================================
    public function updateStatus(Request $request, Candidate $candidate)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $candidate->update(['status' => $request->status]);

        return back()->with('success', 'Candidate status updated successfully!');
    }
}