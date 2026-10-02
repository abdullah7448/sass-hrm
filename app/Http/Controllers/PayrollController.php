<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // 🟢 Auth ফ্যাসাড ইমপোর্ট করা হলো

class PayrollController extends Controller
{
    public function generatePaySlip(Request $request, $employeeId)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user(); // 🟢 ডাবল কোলন এবং টাইপ হিন্টিং ব্যবহার করা হলো
        
        $employee = User::where('company_id', $admin->company_id)->findOrFail($employeeId);

        $filterMonth = $request->input('month', Carbon::now()->month);
        $filterYear = $request->input('year', Carbon::now()->year);

        // ১. ওই মাসের সব অ্যাটেনডেন্স রেকর্ড আনা
        $attendances = Attendance::where('user_id', $employeeId)
            ->whereMonth('date', $filterMonth)
            ->whereYear('date', $filterYear)
            ->get();

        $totalPresent = $attendances->where('status', 'present')->count();
        $totalLate = $attendances->where('status', 'late')->count();
        
        // নোট: লেট হলেও সে প্রেজেন্ট, তাই মোট কাজের দিন = Present + Late
        $totalWorkingDays = $totalPresent + $totalLate;

        // ২. আন-অ্যাপ্রুভড লেট হিসাব (৩ দিন লেট = ১ দিন কাটা)
        $unapprovedLateCount = $attendances->filter(function($att) {
            return $att->status === 'late' && !$att->is_late_approved;
        })->count();

        $basicSalary = $employee->basic_salary ?? 0;
        $perDaySalary = $basicSalary > 0 ? ($basicSalary / 30) : 0;

        // 🟢 ৩. মূল কারেকশন: যত দিন এসেছে, শুধু তত দিনের বেসিক স্যালারি পাবে!
        $earnedBasicSalary = $totalWorkingDays * $perDaySalary;

        // ৪. ফাইন ডিডাকশন (লেটজনিত কারণে কাটা)
        $deductionDays = floor($unapprovedLateCount / 3); 
        $lateDeduction = $deductionDays * $perDaySalary;

        // ৫. অ্যাটেনডেন্স বোনাস (পুরো মাসে ফুল প্রেজেন্ট বা একটি নির্দিষ্ট লেভেলের বেশি থাকলে দিতে পারেন, আপাতত ডাইনামিক রাখছি)
        // যদি সে অন্তত ২০ দিন আসে তবেই বোনাস পাবে, না হলে জিরো
        $attendanceBonus = ($totalWorkingDays >= 20) ? 2000 : 0; 

        // ৬. নিট পে (Net Payable Salary)
        $netSalary = ($earnedBasicSalary + $attendanceBonus) - $lateDeduction;

        $payrollData = [
            'month_name' => Carbon::createFromDate($filterYear, $filterMonth, 1)->format('F Y'),
            'basic_salary' => round($basicSalary, 2),
            'total_working_days' => $totalWorkingDays, // কত দিন এসেছে
            'earned_basic' => round($earnedBasicSalary, 2), // উপস্থিতির ওপর ভিত্তি করে বেসিক
            'total_late' => $totalLate,
            'late_deduction' => round($lateDeduction, 2),
            'attendance_bonus' => $attendanceBonus,
            'net_salary' => max(0, round($netSalary, 2)),
        ];

        return back()->with([
            'payroll' => $payrollData,
        ]);
    }
}