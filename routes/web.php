<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateFunnelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

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
    
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
});


// ==========================================
// 2. Company Admin Routes (HR Management)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/applications', [CandidateController::class, 'index'])->name('applications.index');
});


// ==========================================
// 3. Candidate Funnel Routes
// ==========================================

// Step 1: Registration (Public Route)
Route::get('/apply/{company_id}', [CandidateFunnelController::class, 'showRegister'])
    ->where('company_id', '[0-9]+')
    ->name('candidate.apply.show');
    
Route::post('/apply/{company_id}', [CandidateFunnelController::class, 'processRegister'])
    ->where('company_id', '[0-9]+')
    ->name('candidate.apply.process');

// Step 2 to 5: Assessment & Documents (এখান থেকে auth সরিয়ে দিয়েছি)
Route::get('/apply/assessment', [CandidateFunnelController::class, 'showAssessment'])->name('candidate.assessment');
Route::post('/apply/assessment', [CandidateFunnelController::class, 'processAssessment']);

Route::get('/apply/documents', [CandidateFunnelController::class, 'showDocuments'])->name('candidate.documents');
Route::post('/apply/documents', [CandidateFunnelController::class, 'processDocuments']);

Route::get('/apply/rules', [CandidateFunnelController::class, 'showRules'])->name('candidate.rules');
Route::post('/apply/rules', [CandidateFunnelController::class, 'processRules']);

Route::get('/apply/success', [CandidateFunnelController::class, 'success'])->name('candidate.success');


// ==========================================
// Profile Routes
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';