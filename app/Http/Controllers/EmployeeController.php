<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    // ১. Employee List (অ্যাডমিন ভিউ)
    public function index(Request $request)
    {
        // 🟢 সরাসরি Base Controller থেকে আইডি নিচ্ছে
        $companyId = $this->getActiveCompanyId(); 

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

    // ২. Update Employee
    public function update(Request $request, $id)
    {
        $companyId = $this->getActiveCompanyId();
        
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

    // ৩. Terminate / Fire Employee
    public function terminate($id)
    {
        $companyId = $this->getActiveCompanyId();

        $employee = User::where('company_id', $companyId)->findOrFail($id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee terminated successfully. Email is now released.');
    }

    // ৪. Employee Profile View (Digital File)
    public function show(\Illuminate\Http\Request $request, $id)
    {
        $companyId = $this->getActiveCompanyId();

        $employee = \App\Models\User::where('company_id', $companyId)->findOrFail($id);

        $candidateData = \App\Models\Candidate::where('email', $employee->email)
                                              ->where('company_id', $companyId)
                                              ->first();

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

        $totalWorkingDays = $attSummary['present'] + $attSummary['late'];
        $unapprovedLateCount = $attendances->filter(function($att) {
            return $att->status === 'late' && !$att->is_late_approved;
        })->count();

        $basicSalary = $employee->basic_salary ?? 0;
        $perDaySalary = $basicSalary > 0 ? ($basicSalary / 30) : 0;
        
        $earnedBasic = $totalWorkingDays * $perDaySalary;
        $deductionDays = floor($unapprovedLateCount / 3); 
        $lateDeduction = $deductionDays * $perDaySalary;
        $attendanceBonus = ($totalWorkingDays >= 20) ? 2000 : 0; 
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
            'payroll' => $payroll, 
        ]);
    }
}