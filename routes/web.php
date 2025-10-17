<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\DomainsPage;
use App\Livewire\ResourcesPage;
use App\Livewire\ProfilePage;
use App\Livewire\BatchesPage;
use App\Livewire\ExamsPage;
use App\Livewire\ExamLanding;
use App\Livewire\ExamPlayer;
use App\Livewire\ExamResult;
use App\Livewire\ResourceDetail;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Livewire\AttemptsPage;
use App\Livewire\Admin\LogsPage as AdminLogsPage;

// SPA-style routes powered by Livewire v3 (wire:navigate handled in views)
Route::get('/', HomePage::class)->name('home');
Route::get('/domains', DomainsPage::class)->name('domains');
Route::get('/resources', ResourcesPage::class)->name('resources');
Route::get('/profile', ProfilePage::class)->middleware(['auth'])->name('profile');
Route::get('/attempts', AttemptsPage::class)->middleware(['auth'])->name('attempts');

// Domain -> Batches -> Exams flow
Route::get('/domains/{domain}/batches', BatchesPage::class)->name('batches');
Route::get('/batches/{batch}/exams', ExamsPage::class)->name('exams');

// Exam journey
Route::get('/exam/{exam}', ExamLanding::class)->name('exam.landing');
Route::get('/exam/{exam}/play', ExamPlayer::class)->name('exam.play');
Route::get('/exam/{exam}/result', ExamResult::class)->name('exam.result');
Route::post('/exam/{exam}/finish', [ExamController::class, 'finish'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('exam.finish');

// Resource detail
Route::get('/resource/{resource}', ResourceDetail::class)->name('resource.detail');

// Alias for Breeze/legacy links expecting a dashboard route
Route::get('/dashboard', HomePage::class)->name('dashboard');

// Breeze auth routes
require __DIR__.'/auth.php';

// OTP Authentication Routes
Route::post('/login-otp', [AuthController::class, 'login'])->name('loginOtp.post');
Route::get('/verify-otp', [AuthController::class, 'verifyOtpForm'])->name('verifyOtp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verifyOtp.post');

// Admin Settings (OTP & SMS) - protect with auth
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/admin/logs', AdminLogsPage::class)->name('admin.logs');
});

// Pricing (public) and Checkout (requires auth)
Route::get('/pricing', function () {
    $plans = \App\Models\SubscriptionPlan::all();
    return view('pricing', compact('plans'));
})->name('pricing');

Route::post('/checkout/{plan}', [SubscriptionController::class, 'checkout'])
    ->middleware(['auth'])
    ->name('checkout');

// Show checkout page
Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkoutShow'])
    ->middleware(['auth'])
    ->name('checkout.show');

// Payment Routes (Zarinpal)
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{plan}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{plan}/request', [PaymentController::class, 'request'])->name('payment.request');
});

// Payment Callback (no auth required for callback)
Route::get('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
