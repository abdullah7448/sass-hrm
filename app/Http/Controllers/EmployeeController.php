<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        // Spatie ব্যবহার করে শুধুমাত্র 'Employee' রোল পাওয়া ইউজারদের আনা হচ্ছে
        $employees = User::role('Employee')
                        ->where('company_id', $companyId)
                        ->latest()
                        ->get();

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees
        ]);
    }
}