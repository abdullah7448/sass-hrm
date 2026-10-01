<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    // ১. Employee List (অ্যাডমিন ভিউ)
 public function index(Request $request)
{
    // ডাইনামিক কোম্পানি আইডি পিক করা (সুপার অ্যাডমিন ইমপারসনেশন সহ)
    $isSuperAdmin = auth()->user()->hasRole('super_admin');
    $companyId = ($isSuperAdmin && session()->has('active_company_id')) 
                    ? session('active_company_id') 
                    : auth()->user()->company_id;

    $query = User::where('company_id', $companyId);

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    $employees = $query->latest()->get();

    return Inertia::render('Admin/Employees/Index', [
        'employees' => $employees,
        'filters' => $request->only(['search'])
    ]);
}

    // ২. Terminate / Fire Employee
    public function terminate($id)
    {
        $companyId = auth()->user()->company_id;

        // role কলাম রিমুভ করা হলো
        $employee = User::where('company_id', $companyId)
                        ->findOrFail($id);

        // Terminate মানে হলো ইউজারকে রিমুভ করে দেওয়া, যাতে সে অন্য কোম্পানিতে যেতে পারে
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee terminated successfully. Email is now released.');
    }

    // ৩. Employee Profile View (Digital File)
    public function show($id)
    {
        $companyId = auth()->user()->company_id;

        // এমপ্লয়ির বেসিক ডাটা নিয়ে আসা
        $employee = User::where('company_id', $companyId)->findOrFail($id);

        // ক্যান্ডিডেট টেবিল থেকে তার বাকি ইনফরমেশন (CV, NID, Department) নিয়ে আসা
        // যেহেতু ইমেইল ইউনিক, তাই ইমেইল দিয়েই আমরা তাকে খুঁজে বের করবো
        $candidateData = \App\Models\Candidate::where('email', $employee->email)
                                              ->where('company_id', $companyId)
                                              ->first();

        return Inertia::render('Admin/Employees/Show', [
            'employee' => $employee,
            'candidateData' => $candidateData // CV, NID, Portfolio Link ইত্যাদি এখানে থাকবে
        ]);
    }
}