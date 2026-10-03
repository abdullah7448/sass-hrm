<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    // 🟢 গ্লোবাল হেল্পার মেথড: সুপার অ্যাডমিন ইমপারসনেশন এবং রেগুলার অ্যাডমিনের জন্য
    protected function getActiveCompanyId()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) return null;

        return ($user->hasAnyRole(['super_admin', 'Super Admin', 'Super-Admin']) && session()->has('active_company_id')) 
                    ? session('active_company_id') 
                    : $user->company_id;
    }
}