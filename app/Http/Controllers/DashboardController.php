<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $todayAttendance = null;
        $monthlyAttendances = [];
        $monthlySummary = null;

        // যদি ইউজার Employee হয়
        if ($user->hasRole('Employee') || $user->hasRole('employee')) {
            
            // ১. আজকের অ্যাটেনডেন্স
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', Carbon::today()->toDateString())
                ->first();

            // ২. চলতি মাসের সব অ্যাটেনডেন্স
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $monthlyAttendances = Attendance::where('user_id', $user->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->orderBy('date', 'desc')
                ->get();

            // ৩. চলতি মাসের সামারি (হিসাব-নিকাশ)
            $monthlySummary = [
                'month_name' => Carbon::now()->format('F Y'), // যেমন: October 2026
                'present' => $monthlyAttendances->where('status', 'present')->count(),
                'late' => $monthlyAttendances->where('status', 'late')->count(),
                'absent' => $monthlyAttendances->where('status', 'absent')->count(),
                'total_days' => $monthlyAttendances->count(),
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'todayAttendance' => $todayAttendance,
            'monthlyAttendances' => $monthlyAttendances,
            'monthlySummary' => $monthlySummary,
        ]);
    }
}