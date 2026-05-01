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

// Redirection helper for authenticated users to get into the /{account}/{role}/ format
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    return redirect()->route('dashboard', [
        'account' => \Illuminate\Support\Str::slug($request->user()->name),
        'role' => strtolower($request->user()->role)
    ]);
})->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified', 'account_role'])->prefix('{account}/{role}')->group(function () {
    
    // Shared Routes
    Route::get('/beranda', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Masyarakat Routes
    Route::middleware(['role:'.User::ROLE_MASYARAKAT])->group(function () {
        Route::get('/laporan/buat', [ComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/laporan', [ComplaintController::class, 'store'])->name('complaints.store');
        Route::get('/laporan-saya', [ComplaintController::class, 'myIndex'])->name('complaints.my');
        Route::get('/notifikasi', [NotificationController::class, 'indexForCitizen'])->name('notifications.citizen');
    });

    // Admin Routes
    Route::middleware(['role:'.User::ROLE_ADMIN])->group(function () {
        Route::get('/verifikasi-laporan', [VerificationController::class, 'index'])->name('admin.complaints.index');
        Route::post('/laporan/{complaint}/keputusan', [VerificationController::class, 'decision'])->name('admin.complaints.decision');
        Route::post('/debug/hapus-semua', [DebugController::class, 'deleteAllComplaints'])->name('admin.debug.delete-all-complaints');
    });

    // Instansi Routes
    Route::middleware(['role:'.User::ROLE_INSTANSI])->group(function () {
        Route::get('/tindak-lanjut', [AssignmentFollowUpController::class, 'index'])->name('instansi.complaints.index');
        Route::post('/laporan/{complaint}/progres', [AssignmentFollowUpController::class, 'progress'])->name('instansi.complaints.progress');
        Route::post('/laporan/{complaint}/selesai', [AssignmentFollowUpController::class, 'resolve'])->name('instansi.complaints.resolve');
        Route::get('/notifikasi-instansi', [NotificationController::class, 'indexForUnit'])->name('notifications.unit');
    });

    Route::get('/laporan/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/lampiran/{attachment}/preview', [ComplaintController::class, 'previewAttachment'])->name('attachments.preview');
});

require __DIR__.'/auth.php';
