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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 🟢 সরাসরি Base Controller থেকে ডাইনামিক কোম্পানি আইডি নেওয়া হলো
        $companyId = $this->getActiveCompanyId();

        // ১. যদি ইউজার অ্যাডমিন বা সুপার অ্যাডমিন হয় (Spatie এর hasAnyRole ব্যবহার করা হলো)
        if ($user->hasAnyRole(['Company Admin', 'Super Admin', 'super_admin', 'Admin'])) {
            
            $leaves = Leave::with('user')
                ->where('company_id', $companyId) // 🟢 ডাইনামিক কোম্পানি আইডি ব্যবহার
                ->latest()
                ->get();

            return Inertia::render('Admin/Leaves/Index', [
                'leaves' => $leaves,
            ]);

        } 
        // ২. যদি সাধারণ এমপ্লয়ি হয় (এমপ্লয়ি শুধুমাত্র নিজের লিভ দেখবে)
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

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // 🟢 ডাইনামিক কোম্পানি আইডি
        $companyId = $this->getActiveCompanyId(); 

        Leave::create([
            'company_id' => $companyId,
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

        // 🟢 ডাইনামিক কোম্পানি আইডি
        $companyId = $this->getActiveCompanyId();

        // 🟢 সিকিউরিটি চেক: অন্য কোম্পানির লিভ রিকোয়েস্ট যেন আপডেট করতে না পারে
        $leave = Leave::where('company_id', $companyId)->findOrFail($id);

        $leave->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Leave request updated successfully!');
    }
}