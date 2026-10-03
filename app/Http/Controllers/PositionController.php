<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    // ১. পজিশন লিস্ট দেখানোর জন্য
    public function index()
    {
        // 🟢 Base Controller থেকে ডাইনামিক কোম্পানি আইডি নেওয়া হলো
        $companyId = $this->getActiveCompanyId();

        $company = \App\Models\Company::findOrFail($companyId);
        
        $positions = \App\Models\Position::where('company_id', $companyId)->latest()->get();
        
        $globalIQ = \App\Models\Question::getGlobalIQQuestions();
        $companyQuestions = \App\Models\Question::where('company_id', $companyId)->get()->toArray();
        $questions = array_merge($globalIQ, $companyQuestions);

        $companySlug = str_replace(' ', '-', strtolower($company->name));
        $applyUrl = url('/apply/' . $companySlug);

        return Inertia::render('Admin/Positions/Index', [
            'positions' => $positions,
            'questions' => $questions,
            'applyUrl' => $applyUrl
        ]);
    }

    // ২. নতুন পজিশন তৈরি করার জন্য
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // 🟢 ডাইনামিক কোম্পানি আইডি
        $companyId = $this->getActiveCompanyId();

        Position::create([
            'company_id' => $companyId,
            'title' => $request->title,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Position created successfully!');
    }

    // ৩. পজিশন Active/Inactive করার জন্য
    public function update(Request $request, Position $position)
    {
        // 🟢 ডাইনামিক কোম্পানি আইডি
        $companyId = $this->getActiveCompanyId();

        // সিকিউরিটি চেক (যাতে শুধু নির্দিষ্ট কোম্পানির অ্যাডমিন বা সুপার অ্যাডমিন চেঞ্জ করতে পারে)
        if ($position->company_id !== $companyId) {
            abort(403);
        }

        $position->update([
            'is_active' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Position status updated!');
    }
}