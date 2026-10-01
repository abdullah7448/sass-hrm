<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateFunnelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// 1. Super Admin Routes (Master Access)
// ==========================================
Route::prefix('companies')->middleware(['auth', 'verified'])->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/', [CompanyController::class, 'store'])->name('store');

    // 🟢 Notun add kora holo: Tenant Switching Routes
    Route::get('/{company}/manage', [CompanyController::class, 'manageCompany'])->name('manage');
    Route::post('/exit-management', [CompanyController::class, 'exitManagement'])->name('exit');
    
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
});

 
// ==========================================
// 2. Company Admin Routes (HR Management)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/applications', [CandidateController::class, 'index'])->name('applications.index');
    
   // Employee Management Routes
    Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{id}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('employees.show'); // <--- এটি নতুন
    Route::delete('/employees/{id}/terminate', [\App\Http\Controllers\EmployeeController::class, 'terminate'])->name('employees.terminate');

    // Approve রাউট
    Route::post('/applications/{candidate}/approve', [CandidateController::class, 'approve'])->name('applications.approve');
    // Status Update রাউট (নতুন অ্যাড করা হলো)
    Route::post('/applications/{candidate}/status', [CandidateController::class, 'updateStatus'])->name('applications.update_status');
    
    // Job Positions Routes
    Route::get('/positions', [\App\Http\Controllers\PositionController::class, 'index'])->name('positions.index');
    Route::post('/positions', [\App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
    Route::put('/positions/{position}', [\App\Http\Controllers\PositionController::class, 'update'])->name('positions.update');
    
    // Question Builder Routes
    Route::post('/questions', [\App\Http\Controllers\QuestionController::class, 'store'])->name('questions.store');
    Route::delete('/questions/{question}', [\App\Http\Controllers\QuestionController::class, 'destroy'])->name('questions.destroy');
});


// ==========================================
// 3. Candidate Funnel Routes (FIXED: Step-by-Step)
// ==========================================


// বাকি সব রাউট থেকে {company_name} সরিয়ে দেওয়া হলো (কারণ আমরা Session ব্যবহার করবো)
// Step 2: IQ Test
Route::get('/apply/iq', [CandidateFunnelController::class, 'showIQ'])->name('candidate.iq');
Route::post('/apply/iq/process', [CandidateFunnelController::class, 'processIQ'])->name('candidate.process_iq');

// Step 3: Departmental Test
Route::get('/apply/departmental', [CandidateFunnelController::class, 'showDepartmental'])->name('candidate.departmental');
Route::post('/apply/departmental/process', [CandidateFunnelController::class, 'processDepartmental'])->name('candidate.process_departmental');

// Step 4: Documents Upload
Route::get('/apply/documents', [CandidateFunnelController::class, 'showDocuments'])->name('candidate.documents');
Route::post('/apply/documents/process', [CandidateFunnelController::class, 'processDocuments'])->name('candidate.process_documents');

// Step 5: Office Rules & NDA
Route::get('/apply/rules', [CandidateFunnelController::class, 'showRules'])->name('candidate.rules');
Route::post('/apply/rules/process', [CandidateFunnelController::class, 'processRules'])->name('candidate.process_rules');

// Final Step: Success / Application Status
Route::get('/apply/success', [CandidateFunnelController::class, 'success'])->name('candidate.success');



// Step 1: Registration (শুধুমাত্র এখানে company_name থাকবে)
Route::get('/apply/{company_name}', [CandidateFunnelController::class, 'showRegister'])->name('candidate.apply.show');
Route::post('/apply/{company_name}', [CandidateFunnelController::class, 'processRegister'])->name('candidate.apply.process');

// 🛠️ Test Route (সেশন রিসেট করার জন্য)
Route::get('/apply-reset/{company_name}', function ($company_name) {
    session()->forget('candidate_id');
    return redirect()->route('candidate.apply.show', ['company_name' => $company_name]);
});


// ==========================================
// Profile Routes
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


//https://my-chat.app.n8n.cloud/workflow/ZLSAcw9ul1aYDLy6/e7259f 