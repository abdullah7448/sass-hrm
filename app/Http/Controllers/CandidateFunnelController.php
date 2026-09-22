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
    // Helper: Redirect to Specific Company Apply Page if Session is Missing
    // ==========================================
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
        // ইউআরএল থেকে হাইফেন (-) সরিয়ে স্পেস দিয়ে কোম্পানির নাম মেলানো হচ্ছে
        $company = \App\Models\Company::whereRaw('LOWER(name) = ?', [strtolower(str_replace('-', ' ', $company_name))])->firstOrFail();
        $company_id = $company->id;

        // সেশনে slug সেভ করে রাখছি ফলব্যাকের জন্য
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

        // ডাটাবেস থেকে কোম্পানি আইডি বের করা
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

        return redirect()->route('candidate.iq'); 
    }

    
   // ==========================================
    // STEP 2: IQ Test (Global + Company Specific)
    // ==========================================
    public function showIQ()
    {
        $candidateId = session('candidate_id');
        if (!$candidateId) return $this->redirectToApplyPage();

        $candidate = \App\Models\Candidate::findOrFail($candidateId);

        // ১. গ্লোবাল/টেমপ্লেট প্রশ্নগুলো নিয়ে আসা
        $globalIQ = \App\Models\Question::getGlobalIQQuestions();

        // ২. এই কোম্পানির নিজস্ব অ্যাড করা প্রশ্নগুলো নিয়ে আসা
        $companyIQ = \App\Models\Question::where('company_id', $candidate->company_id)
            ->where('category', 'iq')->get()->toArray();

        // ৩. দুটো একসাথে যুক্ত (Merge) করে ভিউতে পাঠানো
        $questions = array_merge($globalIQ, $companyIQ);

        return Inertia::render('Funnel/Assessment', [
            'questions' => $questions,
            'step_title' => 'Phase 1: IQ Assessment',
            'next_route' => route('candidate.process_iq')
        ]);
    }

    public function processIQ(Request $request)
    {
        $this->saveAnswersToDB(session('candidate_id'), $request->answers);
        return redirect()->route('candidate.departmental'); // IQ -> Departmental
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
        return redirect()->route('candidate.documents'); // Departmental -> Documents
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

        return redirect()->route('candidate.rules'); // Documents -> Rules
    }

    // ==========================================
    // STEP 5: Office Rules (Static Agreement Page)
    // ==========================================
    public function showRules()
    {
        $candidateId = session('candidate_id');
        if (!$candidateId) return $this->redirectToApplyPage();
        
        return Inertia::render('Funnel/Rules');
    }

    public function processRules(Request $request)
    {
        return redirect()->route('candidate.success'); // Rules -> Success
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