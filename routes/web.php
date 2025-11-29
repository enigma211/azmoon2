<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Livewire\HomePage;
use App\Livewire\DomainsPage;
use App\Livewire\ProfilePage;
use App\Livewire\BatchesPage;
use App\Livewire\ExamsPage;
use App\Livewire\ExamLanding;
use App\Livewire\ExamPlayer;
use App\Livewire\ExamResult;
use App\Livewire\ResourceDetail;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SitemapController;
use App\Livewire\AttemptsPage;
use App\Livewire\SupportTicketsPage;
use App\Livewire\Admin\LogsPage as AdminLogsPage;
use App\Livewire\EducationalResourcesPage;
use App\Livewire\ResourceCategoriesPage;
use App\Livewire\ResourcePostsPage;
use App\Livewire\ResourcePostDetailPage;

// SPA-style routes powered by Livewire v3 (wire:navigate handled in views)
Route::get('/', HomePage::class)->name('home');
Route::get('/domains', DomainsPage::class)->name('domains');
Route::get('/resources', EducationalResourcesPage::class)->name('resources'); // تغییر به سیستم جدید
Route::get('/profile', ProfilePage::class)->name('profile');
Route::get('/attempts', AttemptsPage::class)->middleware(['auth'])->name('attempts');
Route::get('/support-tickets', SupportTicketsPage::class)->middleware(['auth'])->name('support-tickets');

// PWA Offline page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Debug/Test routes (only in local environment)
if (App::environment('local')) {
    // PWA Test page (فقط در محیط development)
    Route::get('/pwa-test', function () {
        return view('pwa-test');
    })->name('pwa.test');

    // PWA Debug page (برای عیب‌یابی موبایل)
    Route::get('/pwa-debug', function () {
        return view('pwa-debug');
    })->name('pwa.debug');

    // Push Notifications Test page
    Route::get('/push-test', function () {
        return view('push-test');
    })->name('push.test');
}

// SEO Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Domain -> Batches -> Exams flow
Route::get('/domains/{domain}/batches', BatchesPage::class)->name('batches');
Route::get('/batches/{batch}/exams', ExamsPage::class)->name('exams');

// Exam journey
Route::get('/exam/{exam}', ExamLanding::class)->name('exam.landing');

Route::middleware(['auth', \App\Http\Middleware\EnsureSubscribed::class])->group(function () {
    Route::get('/exam/{exam}/play', ExamPlayer::class)->name('exam.play');
    Route::get('/exam/{exam}/result', ExamResult::class)->name('exam.result');
    Route::post('/exam/{exam}/finish', [ExamController::class, 'finish'])
        ->middleware('throttle:10,1')
        ->name('exam.finish');
});

// Resource detail
Route::get('/resource/{resource}', ResourceDetail::class)->name('resource.detail');

// Educational Resources Routes
Route::get('/educational-resources', EducationalResourcesPage::class)->name('educational-resources');
Route::get('/educational-resources/{slug}', ResourceCategoriesPage::class)->name('educational-resources.categories');
Route::get('/educational-resources/{examTypeSlug}/{categorySlug}', ResourcePostsPage::class)->name('educational-resources.posts');
Route::get('/educational-resources/{examTypeSlug}/{categorySlug}/{postSlug}', ResourcePostDetailPage::class)->name('educational-resources.post');

// Alias for Breeze/legacy links expecting a dashboard route
Route::get('/dashboard', HomePage::class)->name('dashboard');

// Payment Callback (no auth required for callback) - MUST be before auth.php
Route::get('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');

// Breeze auth routes
require __DIR__.'/auth.php';

// OTP Authentication Routes
Route::post('/login-otp', [AuthController::class, 'login'])->name('loginOtp.post');
Route::get('/verify-otp', [AuthController::class, 'verifyOtpForm'])->name('verifyOtp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verifyOtp.post');

// Admin Logs and SMS test
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/logs', AdminLogsPage::class)->name('admin.logs');
    Route::post('/admin/sms-test', [\App\Http\Controllers\AdminController::class, 'sendTestSms'])->name('admin.sms.test');
    
    // SMS Debug page
    Route::get('/admin/sms-debug', function () {
        return view('admin-sms-debug');
    })->name('admin.sms.debug');
});

// Pricing (public) and Checkout (requires auth)
Route::get('/pricing', function () {
    $plans = \App\Models\SubscriptionPlan::all();
    return view('pricing', compact('plans'));
})->name('pricing');

Route::post('/checkout/{plan}', [SubscriptionController::class, 'checkout'])
    ->middleware(['auth'])
    ->name('checkout');

// Push Notifications API
Route::prefix('push')->name('push.')->group(function () {
    Route::get('/vapid-public-key', [\App\Http\Controllers\PushNotificationController::class, 'getPublicKey'])
        ->name('vapid-key');
    
    Route::post('/subscribe', [\App\Http\Controllers\PushNotificationController::class, 'subscribe'])
        ->name('subscribe');
    
    Route::post('/unsubscribe', [\App\Http\Controllers\PushNotificationController::class, 'unsubscribe'])
        ->name('unsubscribe');
    
    Route::post('/send-test', [\App\Http\Controllers\PushNotificationController::class, 'sendTest'])
        ->middleware(['auth'])
        ->name('send-test');
});

// Show checkout page
Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkoutShow'])
    ->middleware(['auth'])
    ->name('checkout.show');

// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{plan}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{plan}/request', [PaymentController::class, 'request'])->name('payment.request');
});
