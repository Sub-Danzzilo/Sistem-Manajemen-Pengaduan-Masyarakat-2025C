<?php

use App\Http\Controllers\AssignmentFollowUpController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Admin\DebugController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:'.User::ROLE_MASYARAKAT])->group(function () {
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/my', [ComplaintController::class, 'myIndex'])->name('complaints.my');
    Route::get('/notifications', [NotificationController::class, 'indexForCitizen'])->name('notifications.citizen');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
});

Route::middleware(['auth', 'verified', 'role:'.User::ROLE_ADMIN])->group(function () {
    Route::get('/admin/complaints', [VerificationController::class, 'index'])->name('admin.complaints.index');
    Route::post('/admin/complaints/{complaint}/decision', [VerificationController::class, 'decision'])->name('admin.complaints.decision');
    Route::post('/admin/debug/delete-all-complaints', [DebugController::class, 'deleteAllComplaints'])->name('admin.debug.delete-all-complaints');
});

Route::middleware(['auth', 'verified', 'role:'.User::ROLE_INSTANSI])->group(function () {
    Route::get('/instansi/complaints', [AssignmentFollowUpController::class, 'index'])->name('instansi.complaints.index');
    Route::post('/instansi/complaints/{complaint}/progress', [AssignmentFollowUpController::class, 'progress'])->name('instansi.complaints.progress');
    Route::post('/instansi/complaints/{complaint}/resolve', [AssignmentFollowUpController::class, 'resolve'])->name('instansi.complaints.resolve');
    Route::get('/instansi/notifications', [NotificationController::class, 'indexForUnit'])->name('notifications.unit');
});

require __DIR__.'/auth.php';
