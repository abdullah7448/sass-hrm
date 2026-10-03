<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    
    
public function share(Request $request): array
{
    $activeCompany = null;
    if (session()->has('active_company_id')) {
        $activeCompany = \App\Models\Company::find(session('active_company_id'));
    }

    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'company_id' => $request->user()->company_id,
                // 🟢 এই লাইনটি যুক্ত করতে হবে
                'shift_type' => $request->user()->shift_type, 
                'roles' => $request->user()->getRoleNames(), 
            ] : null,
        ],
        'active_company' => $activeCompany ? [
            'id' => $activeCompany->id,
            'name' => $activeCompany->name,
        ] : null,
    ];
}
}


