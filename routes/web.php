<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateFunnelController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveController;
use Inertia\Inertia;

// ==========================================
// Public Routes
// ==========================================
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================
// 1. Super Admin Routes (Master Access)
// ==========================================
Route::prefix('companies')->middleware(['auth', 'verified', 'role:Super Admin'])->name('companies.')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('index');
    Route::get('/create', [CompanyController::class, 'create'])->name('create');
    Route::post('/', [CompanyController::class, 'store'])->name('store');

    Route::get('/{company}/manage', [CompanyController::class, 'manageCompany'])->name('manage');
    Route::post('/exit-management', [CompanyController::class, 'exitManagement'])->name('exit');
    
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
});


// ==========================================
// 2. Company Admin & Super Admin Routes (HR Management)
// ==========================================
Route::middleware(['auth', 'verified', 'role:Company Admin|Super Admin'])->group(function () {
    
    Route::get('/applications', [CandidateController::class, 'index'])->name('applications.index');
    Route::post('/applications/{candidate}/approve', [CandidateController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{candidate}/status', [CandidateController::class, 'updateStatus'])->name('applications.update_status');
    
    Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/{id}', [\App\Http\Controllers\EmployeeController::class, 'show'])->name('employees.show'); 
    Route::delete('/employees/{id}/terminate', [\App\Http\Controllers\EmployeeController::class, 'terminate'])->name('employees.terminate');
    Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
    Route::get('/employees/{id}/payroll', [PayrollController::class, 'generatePaySlip'])->name('employees.payroll');

    Route::get('/positions', [\App\Http\Controllers\PositionController::class, 'index'])->name('positions.index');
    Route::post('/positions', [\App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
    Route::put('/positions/{position}', [\App\Http\Controllers\PositionController::class, 'update'])->name('positions.update');
    
    Route::post('/questions', [\App\Http\Controllers\QuestionController::class, 'store'])->name('questions.store');
    Route::delete('/questions/{question}', [\App\Http\Controllers\QuestionController::class, 'destroy'])->name('questions.destroy');

    // Admin Attendance & Leave Approvals
    Route::get('/company/attendance/today', [AttendanceController::class, 'companyTodayAttendance'])->name('company.attendance.today');
    Route::post('/attendance/{id}/approve-late', [AttendanceController::class, 'approveLate'])->name('attendance.approve-late');
    Route::patch('/leaves/{id}/status', [LeaveController::class, 'updateStatus'])->name('leaves.update-status');
});


// ==========================================
// 3. Employee & Common Routes (For All Logged in users)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/my-attendance', [AttendanceController::class, 'myAttendance'])->name('my-attendance');

    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
});


// ==========================================
// 4. Candidate Funnel Routes (Public)
// ==========================================
Route::get('/apply/iq', [CandidateFunnelController::class, 'showIQ'])->name('candidate.iq');
Route::post('/apply/iq/process', [CandidateFunnelController::class, 'processIQ'])->name('candidate.process_iq');

Route::get('/apply/departmental', [CandidateFunnelController::class, 'showDepartmental'])->name('candidate.departmental');
Route::post('/apply/departmental/process', [CandidateFunnelController::class, 'processDepartmental'])->name('candidate.process_departmental');

Route::get('/apply/documents', [CandidateFunnelController::class, 'showDocuments'])->name('candidate.documents');
Route::post('/apply/documents/process', [CandidateFunnelController::class, 'processDocuments'])->name('candidate.process_documents');

Route::get('/apply/rules', [CandidateFunnelController::class, 'showRules'])->name('candidate.rules');
Route::post('/apply/rules/process', [CandidateFunnelController::class, 'processRules'])->name('candidate.process_rules');

Route::get('/apply/success', [CandidateFunnelController::class, 'success'])->name('candidate.success');

Route::get('/apply/{company_name}', [CandidateFunnelController::class, 'showRegister'])->name('candidate.apply.show');
Route::post('/apply/{company_name}', [CandidateFunnelController::class, 'processRegister'])->name('candidate.apply.process');

Route::get('/apply-reset/{company_name}', function ($company_name) {
    session()->forget('candidate_id');
    return redirect()->route('candidate.apply.show', ['company_name' => $company_name]);
});

require __DIR__.'/auth.php';


//https://my-chat.app.n8n.cloud/workflow/ZLSAcw9ul1aYDLy6/e7259f 