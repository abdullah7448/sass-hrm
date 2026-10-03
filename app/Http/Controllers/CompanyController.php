<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // 🟢 Auth ফ্যাসাড ইমপোর্ট করা হলো

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->get();
        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies
        ]);
    }

    // নতুন কোম্পানি যুক্ত করার ফর্ম দেখাবে
    public function create()
    {
        return Inertia::render('Admin/Companies/Create');
    }

    // নতুন কোম্পানির ডাটা সেভ করবে
    public function store(Request $request)
    {
        // ভ্যালিডেশন: ইমেইলটি কোম্পানি এবং ইউজার দুই টেবিলেই ইউনিক হতে হবে
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        // ১. নতুন কোম্পানি তৈরি করা
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        // ২. কোম্পানির জন্য ডিফল্ট Company Admin ইউজার তৈরি করা
        $user = \App\Models\User::create([
            'company_id' => $company->id,
            'name' => $request->name . ' Admin',
            'email' => $request->email,
            'password' => bcrypt('password123'), // ডিফল্ট পাসওয়ার্ড
            'is_active' => true,
        ]);

        // ৩. ইউজারকে রোল অ্যাসাইন করা
        $user->assignRole('Company Admin');

        return redirect()->route('companies.index')->with('success', 'Company and Admin user created successfully!');
    }

    // এডিট পেজ দেখাবে
    public function edit(Company $company)
    {
        return Inertia::render('Admin/Companies/Edit', [
            'company' => $company
        ]);
    }

    // ডাটা আপডেট করবে
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully!');
    }

    // কোম্পানি ডিলিট করবে
    public function destroy(Company $company)
    {
        // ঐ কোম্পানির আন্ডারে থাকা ইউজারদেরও ডিলিট বা ইনঅ্যাক্টিভ করা উচিত (আপাতত শুধু কোম্পানি ডিলিট করছি)
        \App\Models\User::where('company_id', $company->id)->delete();
        $company->delete();

        return redirect()->back()->with('success', 'Company deleted successfully!');
    }

    // ==========================================
    // Super Admin: Manage Specific Company
    // ==========================================
 // ==========================================
// Super Admin: Manage Specific Company (Tenant Switching)
// ==========================================
public function manageCompany($id)
{
    /** @var \App\Models\User $user */
    $user = Auth::user(); // 🟢 ডাবল কোলন এবং টাইপ হিন্টিং ব্যবহার করা হলো

    // ১. চেক করা হচ্ছে ইউজার সুপার অ্যাডমিন কি না
    if ($user->hasRole('Super Admin')) {
        
        // ২. 🟢 এখানেই সেশনে active_company_id সেট হয়ে যাচ্ছে!
        session(['active_company_id' => $id]);
        
        // ৩. ড্যাশবোর্ডে রিডাইরেক্ট করা হচ্ছে
        return redirect()->route('dashboard'); 
    }
    
    return redirect()->back()->with('error', 'Unauthorized access.');
}

    // ==========================================
    // Super Admin: Exit Company Management
    // ==========================================
   // মেথড:
public function exitManagement()
{
    if (session()->has('active_company_id')) {
        session()->forget('active_company_id'); // সেশন থেকে মুছে ফেলা
    }
    return redirect()->route('companies.index');
}
}