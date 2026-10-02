<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // 🟢 Auth ফ্যাসাড ইমপোর্ট করা হলো

class EmployeeController extends Controller
{
    // ১. Employee List (অ্যাডমিন ভিউ)
 public function index(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user(); // 🟢 ডাবল কোলন ব্যবহার করা হলো

    // ডাইনামিক কোম্পানি আইডি পিক করা (সুপার অ্যাডমিন ইমপারসনেশন সহ)
    $isSuperAdmin = $user->hasRole('super_admin');
    $companyId = ($isSuperAdmin && session()->has('active_company_id')) 
                    ? session('active_company_id') 
                    : $user->company_id;

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

public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // 🟢 ডাবল কোলন ব্যবহার করা হলো
        $companyId = $user->company_id;
        
        $employee = \App\Models\User::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'basic_salary' => 'nullable|numeric|min:0',
            'shift_type' => 'required|in:morning,evening',
        ]);

        

        $employee->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'basic_salary' => $request->basic_salary,
            'shift_type' => $request->shift_type,
        ]);

        return back()->with('success', 'Employee profile and salary updated successfully!');
    }

    // ২. Terminate / Fire Employee
    public function terminate($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // 🟢 ডাবল কোলন ব্যবহার করা হলো
        $companyId = $user->company_id;

        // role কলাম রিমুভ করা হলো
        $employee = User::where('company_id', $companyId)
                        ->findOrFail($id);

        // Terminate মানে হলো ইউজারকে রিমুভ করে দেওয়া, যাতে সে অন্য কোম্পানিতে যেতে পারে
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee terminated successfully. Email is now released.');
    }

   // ৩. Employee Profile View (Digital File)
    // public function show(\Illuminate\Http\Request $request, $id)
    // {
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user(); // 🟢 এখানেও কমেন্টের ভেতরে আপডেট করে রাখলাম
    //     $companyId = $user->company_id;

    //     // এমপ্লয়ির বেসিক ডাটা নিয়ে আসা
    //     $employee = \App\Models\User::where('company_id', $companyId)->findOrFail($id);

    //     // ক্যান্ডিডেট টেবিল থেকে তার বাকি ইনফরমেশন নিয়ে আসা
    //     $candidateData = \App\Models\Candidate::where('email', $employee->email)
    //                                          ->where('company_id', $companyId)
    //                                          ->first();

    //     // 🟢 অ্যাটেনডেন্স ফিল্টারিং লজিক
    //     $filterDate = $request->input('month_year', \Carbon\Carbon::now()->format('Y-m'));
    //     $parsedDate = \Carbon\Carbon::parse($filterDate);

    //     $attendances = \App\Models\Attendance::where('user_id', $employee->id)
    //         ->whereMonth('date', $parsedDate->month)
    //         ->whereYear('date', $parsedDate->year)
    //         ->orderBy('date', 'desc')
    //         ->get();

    //     $attSummary = [
    //         'month_name' => $parsedDate->format('F Y'),
    //         'present' => $attendances->where('status', 'present')->count(),
    //         'late' => $attendances->where('status', 'late')->count(),
    //         'absent' => $attendances->where('status', 'absent')->count(),
    //     ];

    //     return inertia('Admin/Employees/Show', [
    //         'employee' => $employee,
    //         'candidateData' => $candidateData,
    //         'attendances' => $attendances,
    //         'attSummary' => $attSummary,
    //         'filterDate' => $filterDate,
    //     ]);
    // }
    
    public function show(\Illuminate\Http\Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // 🟢 ডাবল কোলন ব্যবহার করা হলো
        $companyId = $user->company_id;

        $employee = \App\Models\User::where('company_id', $companyId)->findOrFail($id);

        $candidateData = \App\Models\Candidate::where('email', $employee->email)
                                              ->where('company_id', $companyId)
                                              ->first();

        // 🟢 মাস ও বছর ফিল্টার (ডিফল্ট বর্তমান মাস)
        $filterDate = $request->input('month_year', \Carbon\Carbon::now()->format('Y-m'));
        $parsedDate = \Carbon\Carbon::parse($filterDate);

        $attendances = \App\Models\Attendance::where('user_id', $employee->id)
            ->whereMonth('date', $parsedDate->month)
            ->whereYear('date', $parsedDate->year)
            ->orderBy('date', 'desc')
            ->get();

        $attSummary = [
            'month_name' => $parsedDate->format('F Y'),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];

        // 🟢 ডাইনামিক পে-স্লিপ ক্যালকুলেশন
        $totalWorkingDays = $attSummary['present'] + $attSummary['late'];
        $unapprovedLateCount = $attendances->filter(function($att) {
            return $att->status === 'late' && !$att->is_late_approved;
        })->count();

        $basicSalary = $employee->basic_salary ?? 0;
        $perDaySalary = $basicSalary > 0 ? ($basicSalary / 30) : 0;
        
        // উপস্থিতির ওপর ভিত্তি করে অর্জিত বেসিক
        $earnedBasic = $totalWorkingDays * $perDaySalary;

        // লেট পেনাল্টি ডিডাকশন (৩ দিন আন-অ্যাপ্রুভড লেট = ১ দিন কাটা)
        $deductionDays = floor($unapprovedLateCount / 3); 
        $lateDeduction = $deductionDays * $perDaySalary;

        // অ্যাটেনডেন্স বোনাস (কমপক্ষে ২০ দিন উপস্থিত থাকলে ২,০০০ টাকা)
        $attendanceBonus = ($totalWorkingDays >= 20) ? 2000 : 0; 

        // নিট পে
        $netSalary = ($earnedBasic + $attendanceBonus) - $lateDeduction;

        $payroll = [
            'month_name' => $parsedDate->format('F Y'),
            'basic_salary' => round($basicSalary, 2),
            'total_working_days' => $totalWorkingDays,
            'earned_basic' => round($earnedBasic, 2),
            'total_late' => $attSummary['late'],
            'unapproved_late' => $unapprovedLateCount,
            'late_deduction' => round($lateDeduction, 2),
            'attendance_bonus' => $attendanceBonus,
            'net_salary' => max(0, round($netSalary, 2)),
        ];

        return inertia('Admin/Employees/Show', [
            'employee' => $employee,
            'candidateData' => $candidateData,
            'attendances' => $attendances,
            'attSummary' => $attSummary,
            'filterDate' => $filterDate,
            'payroll' => $payroll, // 🟢 পে-স্লিপ ডেটা পাঠানো হলো
        ]);
    }
   
}