<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\CandidateAssessment;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class CandidateFunnelController extends Controller
{
    // ==========================================
    // Helper: আগের উত্তর না মুছে নতুনগুলো যোগ করা
    // ==========================================
    private function saveAnswersToDB($candidateId, $newAnswers)
    {
        $assessment = CandidateAssessment::firstOrCreate(
            ['candidate_id' => $candidateId],
            ['answers' => json_encode([])] 
        );
        
        $existingAnswers = $assessment->answers ? json_decode($assessment->answers, true) : [];
        $mergedAnswers = array_merge($existingAnswers, $newAnswers ?? []);
        
        $assessment->update(['answers' => json_encode($mergedAnswers)]);
    }

    // ==========================================
    // Helper: Redirect to Specific Company Apply Page
    // ==========================================
    private function redirectToApplyPage()
    {
        if (session()->has('company_slug')) {
            return redirect()->route('candidate.apply.show', session('company_slug'));
        }
        return redirect()->back(); 
    }

    // ==========================================
    // STEP 1: Registration
    // ==========================================
    public function showRegister($company_name)
    {
        $company = \App\Models\Company::whereRaw('LOWER(name) = ?', [strtolower(str_replace('-', ' ', $company_name))])->firstOrFail();
        $company_id = $company->id;

        session(['company_slug' => $company_name]);
        session(['company_id' => $company_id]);

        if (session()->has('candidate_id')) {
            $candidate = \App\Models\Candidate::find(session('candidate_id'));
            if ($candidate && $candidate->company_id == $company_id) {
                return redirect()->route('candidate.success');
            }
            session()->forget('candidate_id'); 
        }

        $positions = \App\Models\Position::where('company_id', $company_id)->where('is_active', true)->pluck('title');

        return Inertia::render('Funnel/Register', [
            'company' => $company,
            'departments' => $positions
        ]);
    }

    public function processRegister(Request $request, $company_name)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string',
        ]);

        $company = \App\Models\Company::whereRaw('LOWER(name) = ?', [strtolower(str_replace('-', ' ', $company_name))])->firstOrFail();
        $company_id = $company->id;

        $existingCandidate = \App\Models\Candidate::where('company_id', $company_id)
                                      ->where('email', $request->email)
                                      ->first();

        if ($existingCandidate) {
            session(['candidate_id' => $existingCandidate->id]);
            return redirect()->route('candidate.success');
        }

        if (session()->has('candidate_id')) {
            session()->forget('candidate_id');
        }

        $candidate = \App\Models\Candidate::create([
            'company_id' => $company_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'status' => 'Applied',
        ]);

        session(['candidate_id' => $candidate->id]);

        // সোজাসুজি IQ টেস্টে যাবে, কোনো প্যারামিটার লাগবে না!
        return redirect()->route('candidate.iq'); 
    }

    // ==========================================
    // STEP 2: IQ Test (Global + Company Specific)
    // ==========================================
    // public function showIQ()
    // {
    //     $candidateId = session('candidate_id');
    //     if (!$candidateId) return $this->redirectToApplyPage();

    //     $candidate = \App\Models\Candidate::findOrFail($candidateId);

    //     $globalIQ = \App\Models\Question::getGlobalIQQuestions();
    //     $companyIQ = \App\Models\Question::where('company_id', $candidate->company_id)
    //         ->where('category', 'iq')->get()->toArray();

    //     $questions = array_merge($globalIQ, $companyIQ);

    //     return Inertia::render('Funnel/Assessment', [
    //         'questions' => $questions,
    //         'step_title' => 'Phase 1: IQ Assessment',
    //         'next_route' => route('candidate.process_iq')
    //     ]);
    // }

    public function showIQ()
    {
        $candidateId = session('candidate_id');
        
        // আপনার আগের লজিক অনুযায়ী রিডাইরেক্ট
        if (!$candidateId) {
            return redirect()->route('home')->with('error', 'Session expired. Please register again.');
        }

        $candidate = \App\Models\Candidate::find($candidateId);
        if (!$candidate) {
            return redirect()->route('home');
        }

        $companyId = $candidate->company_id;
        $position = $candidate->position;

        // ১. গ্লোবাল IQ প্রশ্ন আনা
        $globalIQ = \App\Models\Question::getGlobalIQQuestions();

        // ২. কাস্টম IQ প্রশ্ন (যেগুলো শুধু ক্যান্ডিডেটের পজিশনের জন্য বা 'All' এর জন্য)
        $companyQuestions = \App\Models\Question::where('company_id', $companyId)
            ->where('category', 'iq')
            ->where(function($query) use ($position) {
                $query->where('department', $position)
                      ->orWhere('department', 'All');
            })
            ->get()
            ->toArray();

        // ৩. মার্জ করা
        $questions = array_merge($globalIQ, $companyQuestions);

        // 🟢 মূল ফিক্স: আপনার আগের ফাইলনেম (Funnel/Assessment) এবং Props গুলো ঠিক করে দেওয়া হলো
        return Inertia::render('Funnel/Assessment', [
            'questions' => $questions,
            'step_title' => 'Phase 1: IQ Assessment',
            'next_route' => route('candidate.process_iq')
        ]);
    }

    public function processIQ(Request $request)
    {
        $this->saveAnswersToDB(session('candidate_id'), $request->answers);
        return redirect()->route('candidate.departmental'); 
    }

    // ==========================================
    // STEP 3: Departmental Test (Position Specific)
    // ==========================================
    public function showDepartmental()
    {
        $candidateId = session('candidate_id');
        if (!$candidateId) return $this->redirectToApplyPage();

        $candidate = Candidate::findOrFail($candidateId);

        $questions = Question::where('company_id', $candidate->company_id)
            ->where('department', $candidate->position)
            ->where('category', 'departmental')->get();

        return Inertia::render('Funnel/Assessment', [
            'questions' => $questions,
            'step_title' => 'Phase 2: Departmental Assessment',
            'next_route' => route('candidate.process_departmental')
        ]);
    }

    public function processDepartmental(Request $request)
    {
        $this->saveAnswersToDB(session('candidate_id'), $request->answers);
        return redirect()->route('candidate.documents'); 
    }

    // ==========================================
    // STEP 4: Documents Upload
    // ==========================================
    public function showDocuments()
    {
        if (!session('candidate_id')) return $this->redirectToApplyPage();
        return Inertia::render('Funnel/Documents');
    }

    public function processDocuments(Request $request)
    {
        $candidateId = session('candidate_id');
        if (!$candidateId) return $this->redirectToApplyPage();

        $request->validate([
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'nid' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $candidate = Candidate::findOrFail($candidateId);
        $data = [];

        if ($request->hasFile('resume')) {
            $data['resume_path'] = $request->file('resume')->store('documents/resumes', 'public');
        }
        if ($request->hasFile('nid')) {
            $data['nid_path'] = $request->file('nid')->store('documents/nids', 'public');
        }
        if ($request->hasFile('certificate')) {
            $data['certificate_path'] = $request->file('certificate')->store('documents/certificates', 'public');
        }

        $candidate->update($data);

        return redirect()->route('candidate.rules'); 
    }

    // ==========================================
    // STEP 5: Office Rules
    // ==========================================
    public function showRules()
    {
        $candidateId = session('candidate_id');
        if (!$candidateId) return $this->redirectToApplyPage();
        
        return Inertia::render('Funnel/Rules');
    }

    public function processRules(Request $request)
    {
        return redirect()->route('candidate.success'); 
    }

    // ==========================================
    // Final: Success Page
    // ==========================================
    public function success()
    {
        $candidateId = session('candidate_id');
        
        if (!$candidateId) {
            return $this->redirectToApplyPage();
        }

        $candidate = Candidate::findOrFail($candidateId);

        return Inertia::render('Funnel/Success', [
            'candidate' => $candidate
        ]);
    }
}