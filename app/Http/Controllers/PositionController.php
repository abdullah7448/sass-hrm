<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    // পজিশন লিস্ট দেখানোর জন্য
  public function index()
    {
        $companyId = Auth::user()->company_id;
        $company = \App\Models\Company::findOrFail($companyId);
        
        $positions = \App\Models\Position::where('company_id', $companyId)->latest()->get();
        
        // টেমপ্লেট এবং কোম্পানির নিজস্ব প্রশ্নগুলো Merge করা
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

    // নতুন পজিশন তৈরি করার জন্য
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Position::create([
            'company_id' => Auth::user()->company_id,
            'title' => $request->title,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Position created successfully!');
    }

    // পজিশন Active/Inactive করার জন্য
    public function update(Request $request, Position $position)
    {
        // সিকিউরিটি চেক (অন্য কোম্পানির অ্যাডমিন যেন চেঞ্জ করতে না পারে)
        if ($position->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $position->update([
            'is_active' => $request->is_active
        ]);

        return redirect()->back()->with('success', 'Position status updated!');
    }
}