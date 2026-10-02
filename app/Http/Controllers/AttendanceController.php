<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // ১. Check In Method
   public function checkIn(Request $request)
    {
        $user = auth()->user();
        $today = \Carbon\Carbon::today()->toDateString();
        $currentTime = \Carbon\Carbon::now();

        // ডাবল চেক-ইন রোধ করা
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return back()->with('error', 'You have already checked in today.');
        }

        // শিফট টাইম নির্ধারণ (Morning: 9:00 AM, Evening: 2:00 PM)
        $shiftStartTime = $user->shift_type === 'evening' 
            ? \Carbon\Carbon::today()->setHour(14)->setMinute(0) 
            : \Carbon\Carbon::today()->setHour(9)->setMinute(0);

        // ১০ মিনিট গ্রেস পিরিয়ড (যেমন: সকাল ৯:১০ পর্যন্ত রাইট টাইম)
        $graceTime = (clone $shiftStartTime)->addMinutes(10);

        $status = 'present';
        $lateReason = $request->input('late_reason');

        // যদি ১০ মিনিট পার হয়ে যায়
        if ($currentTime->greaterThan($graceTime)) {
            $status = 'late';
            // যদি লেট হয় কিন্তু রিজন না দিয়ে থাকে, তবে ফ্রন্টএন্ড থেকে রিজন চাইতে হবে
        }

        Attendance::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $currentTime->toTimeString(),
            'status' => $status,
            'ip_address' => $request->ip(),
            'late_reason' => $lateReason,
            'is_late_approved' => false, // অ্যাডমিন চাইলে পরে এটি অ্যাপ্রুভ করতে পারবে
        ]);

        return back()->with('success', 'Checked In Successfully!');
    }

    // ২. Check Out Method
   public function checkOut(Request $request)
    {
        $user = auth()->user();
        $today = \Carbon\Carbon::today()->toDateString();
        $currentTime = \Carbon\Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No active check-in found for today.');
        }

        // চেক-ইন টাইম থেকে বর্তমান সময় পর্যন্ত কত মিনিট কাজ হলো তার হিসাব
        $checkInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->check_in);
        $workingMinutes = $checkInTime->diffInMinutes($currentTime);

        $attendance->update([
            'check_out' => $currentTime->toTimeString(),
            'working_minutes' => $workingMinutes,
        ]);

        return back()->with('success', 'Checked Out Successfully!');
    }

    // My Attendance Page (Dynamic Filter & Overview)
    public function myAttendance(Request $request)
    {
        $user = auth()->user();
        
        // ফ্রন্টএন্ড থেকে আসা YYYY-MM ফরম্যাট রিসিভ করা, না থাকলে বর্তমান মাস নেওয়া
        $filterDate = $request->input('month_year', Carbon::now()->format('Y-m'));
        
        $parsedDate = Carbon::parse($filterDate);
        $month = $parsedDate->month;
        $year = $parsedDate->year;

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        // Overview Summary Calculation
        $summary = [
            'month_name' => $parsedDate->format('F Y'), // e.g., October 2026
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'leave' => 0, // লিভ সিস্টেম হলে এখানে ডাইনামিক ডেটা আসবে
            'total_days' => $attendances->count(),
        ];

        return inertia('Employee/MyAttendance', [
            'attendances' => $attendances,
            'summary' => $summary,
            'filterDate' => $filterDate, // Vue-তে বাইন্ড করার জন্য
        ]);
    }

    // Company Admin: Today's Attendance List
    // public function companyTodayAttendance(Request $request)
    // {
    //     $admin = auth()->user();
    //     $today = \Carbon\Carbon::today()->toDateString();

    //     // অ্যাডমিনের কোম্পানির সকল এমপ্লয়িকে খুঁজে বের করা (অ্যাডমিন নিজে বাদে)
    //     $employees = \App\Models\User::where('company_id', $admin->company_id)
    //                      ->where('id', '!=', $admin->id) 
    //                      ->get();

    //     // আজকের অ্যাটেনডেন্সগুলো আনা
    //     $attendances = \App\Models\Attendance::where('company_id', $admin->company_id)
    //                              ->where('date', $today)
    //                              ->get()
    //                              ->keyBy('user_id');

    //     $summary = [
    //         'total' => $employees->count(),
    //         'present' => 0,
    //         'late' => 0,
    //         'absent' => 0,
    //     ];

    //     // এমপ্লয়িদের সাথে অ্যাটেনডেন্স মার্জ (Merge) করা
    //     $attendanceList = $employees->map(function($emp) use ($attendances, &$summary) {
    //         $att = $attendances->get($emp->id);
            
    //         if ($att) {
    //             if ($att->status === 'present') $summary['present']++;
    //             elseif ($att->status === 'late') $summary['late']++;
                
    //             return [
    //                 'id' => $emp->id,
    //                 'name' => $emp->name,
    //                 'email' => $emp->email,
    //                 'status' => $att->status,
    //                 'check_in' => $att->check_in,
    //                 'check_out' => $att->check_out,
    //             ];
    //         } else {
    //             $summary['absent']++;
    //             return [
    //                 'id' => $emp->id,
    //                 'name' => $emp->name,
    //                 'email' => $emp->email,
    //                 'status' => 'absent',
    //                 'check_in' => null,
    //                 'check_out' => null,
    //             ];
    //         }
    //     });

    //     return inertia('Admin/Attendance/Today', [
    //         'attendanceList' => $attendanceList,
    //         'summary' => $summary,
    //         'todayDate' => \Carbon\Carbon::today()->format('F j, Y') // Example: October 2, 2026
    //     ]);
    // }

    public function companyTodayAttendance(Request $request)
    {
        $admin = auth()->user();
        $today = \Carbon\Carbon::today()->toDateString();

        $employees = \App\Models\User::where('company_id', $admin->company_id)
                         ->where('id', '!=', $admin->id) 
                         ->get();

        $attendances = \App\Models\Attendance::where('company_id', $admin->company_id)
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
                    'attendance_id' => $att->id, // 🟢 খুব জরুরি (Approve করার জন্য)
                    'name' => $emp->name,
                    'email' => $emp->email,
                    'status' => $att->status,
                    'check_in' => $att->check_in,
                    'check_out' => $att->check_out,
                    'ip_address' => $att->ip_address,       // 🟢 IP Address
                    'late_reason' => $att->late_reason,     // 🟢 Late Reason
                    'is_late_approved' => $att->is_late_approved, // 🟢 Approval Status
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

    // Admin: Approve Late Entry
    public function approveLate($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // কোম্পানি ম্যাচ করে কিনা সিকিউরিটি চেক
        if ($attendance->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $attendance->update([
            'is_late_approved' => true
        ]);

        return back()->with('success', 'Late arrival approved successfully! Fine waived.');
    }
}