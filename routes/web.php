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
    
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
});


// ==========================================
// 2. Company Admin Routes (HR Management)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/applications', [CandidateController::class, 'index'])->name('applications.index');
    
    // Employees রাউট
    Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    
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

// Step 1: Registration (Updated with company_name instead of ID)
Route::get('/apply/{company_name}', [CandidateFunnelController::class, 'showRegister'])
    ->name('candidate.apply.show');
    
Route::post('/apply/{company_name}', [CandidateFunnelController::class, 'processRegister'])
    ->name('candidate.apply.process');

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

// 🛠️ Test Route (সেশন রিসেট করার জন্য)
Route::get('/apply-reset/{company_id}', function ($company_id) {
    session()->forget('candidate_id');
    return redirect()->route('candidate.apply.show', ['company_id' => $company_id]);
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