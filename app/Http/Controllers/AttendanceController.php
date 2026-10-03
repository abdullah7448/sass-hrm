<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // ১. Check In Method (Employee)
    public function checkIn(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); 
        
        $today = \Carbon\Carbon::today()->toDateString();
        $currentTime = \Carbon\Carbon::now();

        // ডাবল চেক-ইন রোধ করা
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return back()->with('error', 'You have already checked in today.');
        }

        $shiftStartTime = $user->shift_type === 'evening' 
            ? \Carbon\Carbon::today()->setHour(14)->setMinute(0) 
            : \Carbon\Carbon::today()->setHour(9)->setMinute(0);

        // ১০ মিনিট গ্রেস পিরিয়ড
        $graceTime = (clone $shiftStartTime)->addMinutes(10);

        $status = 'present';
        $lateReason = $request->input('late_reason');

        if ($currentTime->greaterThan($graceTime)) {
            $status = 'late';
        }

        Attendance::create([
            'company_id' => $this->getActiveCompanyId(), // 🟢 ডাইনামিক কোম্পানি আইডি
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $currentTime->toTimeString(),
            'status' => $status,
            'ip_address' => $request->ip(),
            'late_reason' => $lateReason,
            'is_late_approved' => false,
        ]);

        return back()->with('success', 'Checked In Successfully!');
    }

    // ২. Check Out Method (Employee)
    public function checkOut(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); 
        
        $today = \Carbon\Carbon::today()->toDateString();
        $currentTime = \Carbon\Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No active check-in found for today.');
        }

        $checkInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->check_in);
        $workingMinutes = $checkInTime->diffInMinutes($currentTime);

        $attendance->update([
            'check_out' => $currentTime->toTimeString(),
            'working_minutes' => $workingMinutes,
        ]);

        return back()->with('success', 'Checked Out Successfully!');
    }

    // ৩. My Attendance Page (Employee)
    public function myAttendance(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); 
        
        $filterDate = $request->input('month_year', Carbon::now()->format('Y-m'));
        
        $parsedDate = Carbon::parse($filterDate);
        $month = $parsedDate->month;
        $year = $parsedDate->year;

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'month_name' => $parsedDate->format('F Y'),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'leave' => 0, 
            'total_days' => $attendances->count(),
        ];

        return inertia('Employee/MyAttendance', [
            'attendances' => $attendances,
            'summary' => $summary,
            'filterDate' => $filterDate,
        ]);
    }

    // ৪. Company Admin / Super Admin: Today's Attendance List
    public function companyTodayAttendance(Request $request)
    {
        $admin = Auth::user(); 
        $companyId = $this->getActiveCompanyId(); // 🟢 ডাইনামিক কোম্পানি আইডি
        
        $today = \Carbon\Carbon::today()->toDateString();

        // 🟢 শুধু নির্দিষ্ট কোম্পানির এমপ্লয়ি টানা হচ্ছে
        $employees = \App\Models\User::where('company_id', $companyId)
                         ->where('id', '!=', $admin->id) 
                         ->get();

        // 🟢 শুধু নির্দিষ্ট কোম্পানির অ্যাটেনডেন্স টানা হচ্ছে
        $attendances = \App\Models\Attendance::where('company_id', $companyId)
                                 ->where('date', $today)
                                 ->get()
                                 ->keyBy('user_id');

        $summary = [
            'total' => $employees->count(),
            'present' => 0,
            'late' => 0,
            'absent' => 0,
        ];

        $attendanceList = $employees->map(function($emp) use ($attendances, &$summary) {
            $att = $attendances->get($emp->id);
            
            if ($att) {
                if ($att->status === 'present') $summary['present']++;
                elseif ($att->status === 'late') $summary['late']++;
                
                return [
                    'id' => $emp->id,
                    'attendance_id' => $att->id,
                    'name' => $emp->name,
                    'email' => $emp->email,
                    'status' => $att->status,
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                    'ip_address' => $att->ip_address,
                    'late_reason' => $att->late_reason,
                    'is_late_approved' => $att->is_late_approved,
                ];
            } else {
                $summary['absent']++;
                return [
                    'id' => $emp->id,
                    'attendance_id' => null,
                    'name' => $emp->name,
                    'email' => $emp->email,
                    'status' => 'absent',
                    'check_in' => null,
                    'check_out' => null,
                    'ip_address' => null,
                    'late_reason' => null,
                    'is_late_approved' => false,
                ];
            }
        });

        return inertia('Admin/Attendance/Today', [
            'attendanceList' => $attendanceList,
            'summary' => $summary,
            'todayDate' => \Carbon\Carbon::today()->format('F j, Y')
        ]);
    }

    // ৫. Admin: Approve Late Entry
    public function approveLate($id)
    {
        $companyId = $this->getActiveCompanyId(); // 🟢 ডাইনামিক কোম্পানি আইডি
        
        $attendance = Attendance::findOrFail($id);
        
        // 🟢 কোম্পানি ম্যাচ করে কিনা সিকিউরিটি চেক
        if ($attendance->company_id !== $companyId) {
            abort(403);
        }

        $attendance->update([
            'is_late_approved' => true
        ]);

        return back()->with('success', 'Late arrival approved successfully! Fine waived.');
    }
}