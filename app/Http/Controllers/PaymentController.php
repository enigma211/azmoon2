<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Services\ZarinpalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected ZarinpalService $zarinpalService;

    public function __construct(ZarinpalService $zarinpalService)
    {
        $this->middleware('auth');
        $this->zarinpalService = $zarinpalService;
    }

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
        $callbackUrl = route('payment.verify');

        $result = $this->zarinpalService->createPaymentRequest($user, $plan, $callbackUrl);

        if ($result['success']) {
            // هدایت به درگاه پرداخت
            return redirect($result['redirect_url']);
        }

        // خطا در ایجاد درخواست
        return back()->with('error', $result['message']);
    }

    /**
     * تایید پرداخت (Callback)
     */
    public function verify(Request $request)
    {
        $authority = $request->get('Authority');
        $status = $request->get('Status');

        if (!$authority) {
            return redirect()->route('profile')
                ->with('error', 'اطلاعات پرداخت نامعتبر است');
        }

        $result = $this->zarinpalService->verifyPayment($authority, $status);

        if ($result['success']) {
            return redirect()->route('profile')
                ->with('subscription_success', "پرداخت با موفقیت انجام شد. کد پیگیری: {$result['ref_id']}");
        }

        if (isset($result['canceled']) && $result['canceled']) {
            return redirect()->route('profile')
                ->with('error', 'پرداخت لغو شد');
        }

        return redirect()->route('profile')
            ->with('error', $result['message'] ?? 'پرداخت ناموفق بود');
    }
}
