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
            'question_text' => 'required|string', // এটি আপডেট করা হয়েছে
            'options' => 'nullable|array',
        ]);

        Question::create([
            'company_id' => Auth::user()->company_id,
            'category' => $request->category,
            'department' => $request->department,
            'type' => $request->type,
            'question_text' => $request->question_text, // এটি আপডেট করা হয়েছে
            'options' => $request->options ? json_encode($request->options) : null,
        ]);

        return redirect()->back()->with('success', 'Question added successfully!');
    }

    public function destroy(Question $question)
    {
        if ($question->company_id === Auth::user()->company_id) {
            $question->delete();
        }
        return redirect()->back()->with('success', 'Question deleted!');
    }
}