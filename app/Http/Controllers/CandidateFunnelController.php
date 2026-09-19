<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\CandidateAssessment;
use Illuminate\Support\Facades\Hash;

class CandidateFunnelController extends Controller
{
    // === Step 1: Registration ===
    public function showRegister($company_id)
    {
        $company = \App\Models\Company::findOrFail($company_id);
        
        $positions = \App\Models\Position::where('company_id', $company->id)
                               ->where('is_active', true)
                               ->pluck('title');

        return Inertia::render('Funnel/Register', [
            'company' => $company,
            'departments' => $positions
        ]);
    }

    public function processRegister(Request $request, $company_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'applied_for' => 'required|string',
        ]);

        $candidate = \App\Models\Candidate::create([
            'company_id' => $company_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->applied_for,
            'status' => 'Pending',
        ]);

        session(['candidate_id' => $candidate->id]);

        return redirect()->route('candidate.assessment');
    }

    // === Step 2 & 3: IQ and Departmental Assessment ===
    public function showAssessment()
    {
        $candidateId = session('candidate_id');
        
        if (!$candidateId) {
            return redirect('/'); 
        }

        $candidate = \App\Models\Candidate::findOrFail($candidateId);
        
        $questions = \App\Models\Question::where('category', 'iq')
            ->orWhere(function($query) use ($candidate) {
                $query->where('category', 'departmental')
                      ->where('department', $candidate->position); 
            })->get();

        return Inertia::render('Funnel/Assessment', ['questions' => $questions]);
    }

    public function processAssessment(Request $request)
    {
        $request->validate(['answers' => 'nullable|array']);
        
        $candidateId = session('candidate_id');
        $candidate = \App\Models\Candidate::findOrFail($candidateId);

        CandidateAssessment::create([
            'candidate_id' => $candidate->id,
            'answers' => json_encode($request->answers),
        ]);
        
        return redirect()->route('candidate.documents');
    }

    // === Step 4: Documents Upload ===
    public function showDocuments()
    {
        if (!session('candidate_id')) return redirect('/');

        return Inertia::render('Funnel/Documents');
    }

    public function processDocuments(Request $request)
    {
        $request->validate([
            'cv' => 'required|mimes:pdf,doc,docx|max:5120',
            'nid' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $candidateId = session('candidate_id');
        $candidate = \App\Models\Candidate::findOrFail($candidateId);

        if ($request->hasFile('cv')) {
            $candidate->resume_path = $request->file('cv')->store('documents', 'public');
        }
        if ($request->hasFile('nid')) {
            $candidate->nid_path = $request->file('nid')->store('documents', 'public');
        }
        if ($request->hasFile('certificate')) {
            $candidate->certificate_path = $request->file('certificate')->store('documents', 'public');
        }
        $candidate->save();

        return redirect()->route('candidate.rules');
    }

    // === Step 5: Office Rules & NDA ===
    public function showRules()
    {
        if (!session('candidate_id')) return redirect('/');

        $rules = Question::where('category', 'office_rules')->get();
        return Inertia::render('Funnel/Rules', ['rules' => $rules]);
    }

    public function processRules(Request $request)
    {
        $request->validate(['answers' => 'required|array']);
        
        $candidateId = session('candidate_id');
        $candidate = \App\Models\Candidate::findOrFail($candidateId);
        
        $assessment = CandidateAssessment::where('candidate_id', $candidate->id)->first();

        // আগের উত্তরের সাথে রুলস এর উত্তরগুলো যোগ করা হচ্ছে
        $existingAnswers = $assessment ? json_decode($assessment->answers, true) : [];
        $allAnswers = array_merge($existingAnswers, $request->answers);
        
        if ($assessment) {
            $assessment->update(['answers' => json_encode($allAnswers)]);
        } else {
            CandidateAssessment::create([
                'candidate_id' => $candidate->id,
                'answers' => json_encode($allAnswers)
            ]);
        }

        $candidate->update(['status' => 'Applied']);
        
        return redirect()->route('candidate.success');
    }

    // === Step 6: Success ===
    public function success()
    {
        if (!session('candidate_id')) return redirect('/');
        
        // ফানেল শেষ হলে সেশন মুছে দেওয়া ভালো, যাতে ক্যান্ডিডেট বারবার না ঢুকতে পারে
        session()->forget('candidate_id');

        return Inertia::render('Funnel/Success');
    }
}