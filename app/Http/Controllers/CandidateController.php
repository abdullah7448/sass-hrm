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
// public function index()
// {
//     // ডাইনামিক কোম্পানি আইডি পিক করা (সুপার অ্যাডমিন ইমপারসনেশন সহ)
//     $isSuperAdmin = auth()->user()->hasRole('super_admin');
//     $companyId = ($isSuperAdmin && session()->has('active_company_id')) 
//                     ? session('active_company_id') 
//                     : auth()->user()->company_id;

//     $candidates = Candidate::with('assessment')
//         ->where('company_id', $companyId)
//         ->latest()
//         ->get();
    
//     $questions = Question::where('company_id', $companyId)->get(); 

//     return Inertia::render('Admin/Candidates/Index', [ 
//         'candidates' => $candidates,
//         'questions' => $questions
//     ]);
// }

public function index()
{
    // ১. ডাইনামিক কোম্পানি আইডি পিক করা
    $isSuperAdmin = auth()->user()->hasRole('super_admin');
    $companyId = ($isSuperAdmin && session()->has('active_company_id')) 
                    ? session('active_company_id') 
                    : auth()->user()->company_id;

    $candidates = Candidate::with('assessment')
        ->where('company_id', $companyId)
        ->latest()
        ->get();
    
    // ২. 🟢 ফিক্স: Global IQ এবং Company Questions একসাথ করে Vue তে পাঠানো
    $globalIQ = \App\Models\Question::getGlobalIQQuestions();
    $companyQuestions = \App\Models\Question::where('company_id', $companyId)->get()->toArray();
    $questions = array_merge($globalIQ, $companyQuestions); 

    return Inertia::render('Admin/Candidates/Index', [ 
        'candidates' => $candidates,
        'questions' => $questions
    ]);
}

  // ৪. Approve Candidate (Make Employee)
    // public function approve($id)
    // {
    //     $companyId = auth()->user()->company_id;
    //     $candidate = Candidate::where('company_id', $companyId)->findOrFail($id);

    //     // কোম্পানির নাম বের করে আনা (স্লাগ বা নামের প্রথম অংশ হিসেবে)
    //     $company = \App\Models\Company::find($companyId);
    //     $companyPrefix = $company ? strtolower(str_replace(' ', '', $company->name)) : 'company';
        
    //     // ডাইনামিক পাসওয়ার্ড তৈরি করা (যেমন: buzzblu@123)
    //     $defaultPassword = $companyPrefix . '@123';

    //     // ইউজার টেবিলে এমপ্লয়ি হিসেবে সেভ করা
    //     $user = User::firstOrCreate(
    //         ['email' => $candidate->email],
    //         [
    //             'name' => $candidate->name,
    //             'password' => bcrypt($defaultPassword),
    //             'role' => 'employee',
    //             'company_id' => $companyId,
    //             'phone' => $candidate->phone,
    //         ]
    //     );

    //     // ক্যান্ডিডেট স্ট্যাটাস আপডেট
    //     $candidate->update(['status' => 'approved']);

    //     return redirect()->back()->with('success', "Candidate Approved! Default password is: {$defaultPassword}");
    // }

    // ৪. Approve Candidate (Make Employee)
    public function approve($id)
    {
        $companyId = auth()->user()->company_id;
        $candidate = Candidate::where('company_id', $companyId)->findOrFail($id);

        // কোম্পানির নাম বের করে আনা
        $company = \App\Models\Company::find($companyId);
        $companyPrefix = $company ? strtolower(str_replace(' ', '', $company->name)) : 'company';
        
        // ডাইনামিক পাসওয়ার্ড তৈরি করা
        $defaultPassword = $companyPrefix . '@123';

        // ইউজার টেবিলে এমপ্লয়ি হিসেবে সেভ করা (role কলাম বাদ দেওয়া হলো)
        $user = User::firstOrCreate(
            ['email' => $candidate->email],
            [
                'name' => $candidate->name,
                'password' => bcrypt($defaultPassword),
                'company_id' => $companyId,
                'phone' => $candidate->phone,
                
                // ডকুমেন্টগুলো ট্রান্সফার করা হলো
                'resume_path' => $candidate->resume_path,
                'nid_path' => $candidate->nid_path,
                'certificate_path' => $candidate->certificate_path,
            ]
        );

        // 🟢 ফিক্স: Spatie প্যাকেজ দিয়ে ডাটাবেসের সাথে মিলিয়ে 'Employee' (বড় হাতের E) রোল অ্যাসাইন করা
        if (!$user->hasRole('Employee')) {
            $user->assignRole('Employee');
        }

        // ক্যান্ডিডেট স্ট্যাটাস আপডেট
        $candidate->update(['status' => 'approved']);

        return redirect()->back()->with('success', "Candidate Approved! Default password is: {$defaultPassword}");
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