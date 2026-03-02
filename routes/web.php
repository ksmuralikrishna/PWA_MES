<?php
// ── routes/web.php ────────────────────────────────────────────────

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;

// Guest — login page
Route::get('/',      [AuthWebController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login.get');

// Authenticated pages
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/logout',   [AuthWebController::class, 'logout'])->name('logout');