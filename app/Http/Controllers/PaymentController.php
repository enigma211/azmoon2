<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment as PaymentGateway;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;

class PaymentController extends Controller
{
    /**
     * نمایش صفحه پرداخت
     */
    public function show(SubscriptionPlan $plan)
    {
        return view('payment.checkout', [
            'plan' => $plan,
            'user' => Auth::user(),
        ]);
    }

    /**
     * ایجاد درخواست پرداخت
     */
    public function request(Request $request, SubscriptionPlan $plan)
    {
        $user = Auth::user();
        
        // تبدیل تومان به ریال (ضرب در 10)
        $amountInRials = $plan->price_toman * 10;
        
        // ایجاد صورتحساب
        $invoice = (new Invoice)->amount($amountInRials);
        
        // ذخیره اطلاعات پرداخت در دیتابیس (به تومان)
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price_toman,
            'status' => 'pending',
            'description' => "خرید اشتراک {$plan->title}",
        ]);

        try {
            // ایجاد درخواست پرداخت
            return PaymentGateway::callbackUrl(route('payment.verify'))
                ->purchase($invoice, function($driver, $transactionId) use ($payment) {
                    // ذخیره شماره تراکنش
                    $payment->update(['authority' => $transactionId]);
                })
                ->pay()
                ->render();
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            return back()->with('error', 'خطا در ایجاد درخواست پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * تایید پرداخت (Callback)
     */
    public function verify(Request $request)
    {
        // Log all callback parameters for debugging
        Log::info('Payment callback received', [
            'all_params' => $request->all(),
            'url' => $request->fullUrl(),
        ]);

        $authority = $request->get('trackId') ?? $request->get('Authority');
        $status = $request->get('success') ?? $request->get('Status');

        if (!$authority) {
            Log::error('Payment verification failed: No authority found');
            return redirect()->route('profile')
                ->with('error', 'اطلاعات پرداخت نامعتبر است');
        }

        // یافتن پرداخت در دیتابیس
        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            Log::error('Payment not found in database', ['authority' => $authority]);
            return redirect()->route('profile')
                ->with('error', 'پرداخت یافت نشد');
        }

        Log::info('Payment found', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'amount' => $payment->amount,
            'status' => $status,
        ]);

        // بررسی لغو پرداخت
        if ($status == 'NOK' || $status == '0' || $status === false) {
            Log::warning('Payment canceled by user', ['payment_id' => $payment->id]);
            $payment->update(['status' => 'canceled']);
            return redirect()->route('profile')
                ->with('error', 'پرداخت توسط کاربر لغو شد');
        }

        try {
            Log::info('Starting payment verification', [
                'payment_id' => $payment->id,
                'amount_rials' => $payment->amount * 10,
                'authority' => $authority,
            ]);

            // تایید پرداخت
            // Important: verify with the same currency unit used in purchase (rials)
            $receipt = PaymentGateway::amount($payment->amount * 10)
                ->transactionId($authority)
                ->verify();

            Log::info('Payment verified successfully', [
                'payment_id' => $payment->id,
                'reference_id' => $receipt->getReferenceId(),
            ]);

            // بروزرسانی وضعیت پرداخت
            $payment->update([
                'status' => 'success',
                'ref_id' => $receipt->getReferenceId(),
                'paid_at' => now(),
                'response_data' => [
                    'reference_id' => $receipt->getReferenceId(),
                    'driver' => $receipt->getDriver(),
                ]
            ]);

            // فعال‌سازی اشتراک کاربر
            $user = $payment->user;
            $plan = $payment->subscriptionPlan;
            
            // Deactivate any other active subscriptions for this user
            $user->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

            // Create a new subscription record
            $subscription = $user->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
                'status' => 'active',
            ]);

            Log::info('Subscription created successfully', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'ends_at' => $subscription->ends_at,
            ]);

            // لاگین کردن کاربر
            Auth::login($user);

            return redirect()->route('profile')
                ->with('subscription_success', "پرداخت با موفقیت انجام شد. کد پیگیری: {$receipt->getReferenceId()}");

        } catch (InvalidPaymentException $e) {
            Log::error('Payment verification failed - InvalidPaymentException', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'current_status' => $payment->status,
            ]);
            
            // اگر پرداخت قبلاً موفق بوده، وضعیت را تغییر نده
            // این حالت زمانی رخ می‌دهد که کاربر صفحه callback را refresh کند
            if ($payment->status === 'success') {
                Log::info('Payment was already successful, redirecting user', ['payment_id' => $payment->id]);
                return redirect()->route('profile')
                    ->with('subscription_success', 'پرداخت شما قبلاً با موفقیت انجام شده است.');
            }
            
            // اگر خطا "previously verified" باشد، یعنی پرداخت موفق بوده ولی ما ثبت نکردیم
            // این یک edge case است که نباید رخ دهد، ولی برای اطمینان چک می‌کنیم
            if (str_contains(strtolower($e->getMessage()), 'previously') || str_contains(strtolower($e->getMessage()), 'verified')) {
                Log::warning('Payment was previously verified but status is not success. Possible data inconsistency.', [
                    'payment_id' => $payment->id,
                    'current_status' => $payment->status,
                ]);
                // در این حالت وضعیت را failed نمی‌گذاریم، بلکه به ادمین اطلاع می‌دهیم
                return redirect()->route('profile')
                    ->with('error', 'وضعیت پرداخت نامشخص است. لطفاً با پشتیبانی تماس بگیرید.');
            }
            
            $payment->update(['status' => 'failed']);
            return redirect()->route('profile')
                ->with('error', 'پرداخت ناموفق بود: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Payment verification failed - Exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'current_status' => $payment->status,
            ]);
            
            // اگر پرداخت قبلاً موفق بوده، وضعیت را تغییر نده
            if ($payment->status === 'success') {
                Log::info('Payment was already successful, redirecting user', ['payment_id' => $payment->id]);
                return redirect()->route('profile')
                    ->with('subscription_success', 'پرداخت شما قبلاً با موفقیت انجام شده است.');
            }
            
            $payment->update(['status' => 'failed']);
            return redirect()->route('profile')
                ->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
        }
    }
}
