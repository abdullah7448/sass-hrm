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
        // 🟢 Base Controller থেকে ডাইনামিক কোম্পানি আইডি নেওয়া হলো
        $companyId = $this->getActiveCompanyId();

        $candidates = \App\Models\Candidate::with('assessment')
            ->where('company_id', $companyId)->latest()->get();
        
        // টেমপ্লেট এবং কোম্পানির প্রশ্নগুলো Merge করা
        $globalIQ = \App\Models\Question::getGlobalIQQuestions();
        $companyQuestions = \App\Models\Question::where('company_id', $companyId)->get()->toArray();
        $questions = array_merge($globalIQ, $companyQuestions); 

        return Inertia::render('Admin/Candidates/Index', [ 
            'candidates' => $candidates,
            'questions' => $questions
        ]);
    }

    public function approve(Candidate $candidate)
    {
        // 🟢 সিকিউরিটি চেক: অন্য কোম্পানির ক্যান্ডিডেটকে যেন অ্যাপ্রুভ করতে না পারে
        $companyId = $this->getActiveCompanyId();
        if ($candidate->company_id !== $companyId) {
            abort(403);
        }

        // ১. ক্যান্ডিডেটের স্ট্যাটাস আপডেট করা
        $candidate->update(['status' => 'Approved']);

        // ২. নতুন Employee একাউন্ট তৈরি করা (ইউজার টেবিলে)
        $employee = User::firstOrCreate(
            ['email' => $candidate->email], // ইমেইল চেক করবে আগে থেকে আছে কিনা
            [
                'name' => $candidate->name,
                'company_id' => $companyId, // 🟢 নিশ্চিত করা হলো যে বর্তমান কোম্পানিতেই অ্যাড হচ্ছে
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
        // 🟢 সিকিউরিটি চেক
        $companyId = $this->getActiveCompanyId();
        if ($candidate->company_id !== $companyId) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|string'
        ]);

        $candidate->update(['status' => $request->status]);

        return back()->with('success', 'Candidate status updated successfully!');
    }
}