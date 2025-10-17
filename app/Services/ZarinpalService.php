<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Zarinpal\Laravel\Facade\Zarinpal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZarinpalService
{
    /**
     * ایجاد درخواست پرداخت
     */
    public function createPaymentRequest(User $user, SubscriptionPlan $plan, string $callbackUrl): array
    {
        try {
            $amount = $plan->price_toman;
            $description = "خرید اشتراک {$plan->title} برای {$user->name}";

            // ایجاد رکورد پرداخت
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'authority' => '', // موقتاً خالی، بعداً پر می‌شود
                'amount' => $amount,
                'status' => 'pending',
                'description' => $description,
            ]);

            // درخواست به زرین‌پال
            $result = Zarinpal::request(
                $callbackUrl,
                $amount,
                $description,
                $user->email,
                $user->mobile
            );

            // ذخیره Authority
            if (isset($result['Authority'])) {
                $payment->update([
                    'authority' => $result['Authority'],
                    'response_data' => $result,
                ]);

                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'authority' => $result['Authority'],
                    'redirect_url' => $this->getPaymentUrl($result['Authority']),
                ];
            }

            // خطا در دریافت Authority
            $payment->update([
                'status' => 'failed',
                'response_data' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['Message'] ?? 'خطا در ایجاد درخواست پرداخت',
                'errors' => $result['errors'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('Zarinpal Payment Request Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'خطا در برقراری ارتباط با درگاه پرداخت',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * تایید پرداخت
     */
    public function verifyPayment(string $authority, string $status): array
    {
        try {
            // پیدا کردن پرداخت
            $payment = Payment::where('authority', $authority)->firstOrFail();

            // اگر قبلاً تایید شده
            if ($payment->isSuccess()) {
                return [
                    'success' => true,
                    'already_verified' => true,
                    'ref_id' => $payment->ref_id,
                    'message' => 'این تراکنش قبلاً تایید شده است',
                ];
            }

            // اگر کاربر لغو کرده
            if ($status === 'NOK') {
                $payment->update(['status' => 'canceled']);
                
                return [
                    'success' => false,
                    'canceled' => true,
                    'message' => 'پرداخت توسط کاربر لغو شد',
                ];
            }

            // تایید با زرین‌پال
            $result = Zarinpal::verify($status, $payment->amount, $authority);

            if (isset($result['Status']) && $result['Status'] === 'success') {
                return DB::transaction(function () use ($payment, $result) {
                    // به‌روزرسانی پرداخت
                    $payment->update([
                        'status' => 'success',
                        'ref_id' => $result['RefID'] ?? null,
                        'paid_at' => now(),
                        'response_data' => array_merge($payment->response_data ?? [], $result),
                    ]);

                    // ایجاد یا به‌روزرسانی اشتراک کاربر
                    $this->createOrUpdateSubscription($payment);

                    return [
                        'success' => true,
                        'ref_id' => $payment->ref_id,
                        'message' => 'پرداخت با موفقیت انجام شد',
                        'payment' => $payment,
                    ];
                });
            }

            // پرداخت ناموفق
            $payment->update([
                'status' => 'failed',
                'response_data' => array_merge($payment->response_data ?? [], $result),
            ]);

            return [
                'success' => false,
                'message' => $result['Message'] ?? 'پرداخت ناموفق بود',
                'errors' => $result['errors'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('Zarinpal Payment Verify Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'خطا در تایید پرداخت',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * ایجاد یا به‌روزرسانی اشتراک کاربر
     */
    protected function createOrUpdateSubscription(Payment $payment): void
    {
        $plan = $payment->subscriptionPlan;
        $user = $payment->user;

        // محاسبه تاریخ شروع و پایان
        $startsAt = now();
        $endsAt = $plan->duration_days > 0 
            ? $startsAt->copy()->addDays($plan->duration_days) 
            : null;

        // پیدا کردن اشتراک فعال
        $activeSubscription = $user->activeSubscription;

        if ($activeSubscription) {
            // به‌روزرسانی اشتراک فعلی
            $activeSubscription->update([
                'subscription_plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
            ]);
        } else {
            // ایجاد اشتراک جدید
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
            ]);
        }
    }

    /**
     * دریافت URL پرداخت
     */
    protected function getPaymentUrl(string $authority): string
    {
        $isSandbox = config('services.zarinpal.sandbox', false);
        $isZarinGate = config('services.zarinpal.zarinGate', false);

        if ($isSandbox) {
            return "https://sandbox.zarinpal.com/pg/StartPay/{$authority}";
        }

        if ($isZarinGate) {
            return "https://www.zarinpal.com/pg/StartPay/{$authority}/ZarinGate";
        }

        return "https://www.zarinpal.com/pg/StartPay/{$authority}";
    }
}
