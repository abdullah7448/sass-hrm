<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 🟢 Spatie রিলেশন থেকে সরাসরি রোলের নাম বের করা (কখনো ফেইল করবে না)
        $userRole = $user->roles->pluck('name')->first();

        // ১. যদি ইউজার অ্যাডমিন বা সুপার অ্যাডমিন হয়
        if ($userRole === 'Company Admin' || $userRole === 'Super Admin' || $userRole === 'Admin') {
            
            $leaves = Leave::with('user')
                ->where('company_id', $user->company_id)
                ->latest()
                ->get();

            return Inertia::render('Admin/Leaves/Index', [
                'leaves' => $leaves,
            ]);

        } 
        // ২. যদি সাধারণ এমপ্লয়ি হয় (অথবা রোল ম্যাচ না করলে ডিফল্ট এমপ্লয়ি ভিউ দেখাবে)
        else {
            
            $leaves = Leave::where('user_id', $user->id)
                ->latest()
                ->get();

            return Inertia::render('Employee/Leaves/Index', [
                'leaves' => $leaves,
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        Leave::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Leave application submitted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $admin = Auth::user();
        $leave = Leave::where('company_id', $admin->company_id)->findOrFail($id);

        $leave->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Leave request updated successfully!');
    }
}