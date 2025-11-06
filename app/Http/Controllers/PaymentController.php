<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // ایجاد صورتحساب
        $invoice = (new Invoice)->amount($plan->price);
        
        // ذخیره اطلاعات پرداخت در دیتابیس
        $payment = Payment::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'pending',
            'description' => "خرید اشتراک {$plan->name}",
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
        $authority = $request->get('trackId') ?? $request->get('Authority');
        $status = $request->get('success') ?? $request->get('Status');

        if (!$authority) {
            return redirect()->route('profile')
                ->with('error', 'اطلاعات پرداخت نامعتبر است');
        }

        // یافتن پرداخت در دیتابیس
        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            return redirect()->route('profile')
                ->with('error', 'پرداخت یافت نشد');
        }

        // بررسی لغو پرداخت
        if ($status == 'NOK' || $status == '0' || $status === false) {
            $payment->update(['status' => 'canceled']);
            return redirect()->route('profile')
                ->with('error', 'پرداخت توسط کاربر لغو شد');
        }

        try {
            // تایید پرداخت
            $receipt = PaymentGateway::amount($payment->amount)
                ->transactionId($authority)
                ->verify();

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
            
            $user->update([
                'subscription_plan_id' => $plan->id,
                'subscription_start' => now(),
                'subscription_end' => now()->addDays($plan->duration_days),
            ]);

            return redirect()->route('profile')
                ->with('subscription_success', "پرداخت با موفقیت انجام شد. کد پیگیری: {$receipt->getReferenceId()}");

        } catch (InvalidPaymentException $e) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('profile')
                ->with('error', 'پرداخت ناموفق بود: ' . $e->getMessage());
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('profile')
                ->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
        }
    }
}
