<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'department' => 'nullable|string',
            'type' => 'required|string|in:mcq,text,yes_no',
            'question_text' => 'required|string',
            'options' => 'nullable|array',
        ]);

        // 🟢 Base Controller থেকে ডাইনামিক কোম্পানি আইডি নেওয়া হলো
        $companyId = $this->getActiveCompanyId();

        Question::create([
            'company_id' => $companyId,
            'category' => $request->category,
            'department' => $request->department,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'options' => $request->options ? json_encode($request->options) : null,
        ]);

        return redirect()->back()->with('success', 'Question added successfully!');
    }

    public function destroy(Question $question)
    {
        // 🟢 ডাইনামিক কোম্পানি আইডি
        $companyId = $this->getActiveCompanyId();

        // 🟢 সিকিউরিটি চেক: অন্য কোম্পানির অ্যাডমিন যেন ডিলিট করতে না পারে
        if ($question->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }

        $question->delete();
        
        return redirect()->back()->with('success', 'Question deleted!');
    }
}