<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // 🟢 এটি এখন সরাসরি Base Controller (Controller.php) থেকে কল হচ্ছে!
        $companyId = $this->getActiveCompanyId();
        
        // এমপ্লয়িদের জন্য ভেরিয়েবল
        $todayAttendance = null;
        $monthlyAttendances = [];
        $monthlySummary = null;

        // অ্যাডমিনদের জন্য ভেরিয়েবল
        $adminStats = null;

        // ==========================================
        // ১. যদি ইউজার Employee হয় (এমপ্লয়ি ড্যাশবোর্ড)
        // ==========================================
        if ($user->hasAnyRole(['Employee', 'employee'])) {
            
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', Carbon::today()->toDateString())
                ->first();

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $monthlyAttendances = Attendance::where('user_id', $user->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->orderBy('date', 'desc')
                ->get();

            $monthlySummary = [
                'month_name' => Carbon::now()->format('F Y'),
                'present' => $monthlyAttendances->where('status', 'present')->count(),
                'late' => $monthlyAttendances->where('status', 'late')->count(),
                'absent' => $monthlyAttendances->where('status', 'absent')->count(),
                'total_days' => $monthlyAttendances->count(),
            ];
        } 
        // ==========================================
        // ২. যদি ইউজার Company Admin বা Super Admin হয়
        // ==========================================
        else {
            $today = Carbon::today()->toDateString();

            // ওই নির্দিষ্ট কোম্পানির মোট এমপ্লয়ি
            $totalEmployees = User::where('company_id', $companyId)
                                  ->role(['Employee', 'employee'])
                                  ->count();

            // আজকের প্রেজেন্ট এবং লেট (নির্দিষ্ট কোম্পানির)
            $todayPresent = Attendance::where('company_id', $companyId)
                                      ->where('date', $today)
                                      ->where('status', 'present')
                                      ->count();

            $todayLate = Attendance::where('company_id', $companyId)
                                   ->where('date', $today)
                                   ->where('status', 'late')
                                   ->count();

            $adminStats = [
                'total_employees' => $totalEmployees,
                'today_present' => $todayPresent,
                'today_late' => $todayLate,
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'todayAttendance' => $todayAttendance,
            'monthlyAttendances' => $monthlyAttendances,
            'monthlySummary' => $monthlySummary,
            'adminStats' => $adminStats,
            'active_company_id' => session('active_company_id'), 
        ]);
    }
}