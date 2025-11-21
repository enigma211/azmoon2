<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    public function checkoutShow(Request $request, SubscriptionPlan $plan)
    {
        return view('checkout', compact('plan'));
    }

    public function checkout(Request $request, SubscriptionPlan $plan)
    {
        $user = $request->user();

        // Deactivate existing active subscriptions that overlap
        UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>', now());
            })
            ->update(['status' => 'inactive']);

        // Activate from purchase day, inclusive. Only date is important in UI, but we store datetime for DB.
        $startsAt = Carbon::now()->startOfDay();
        $durationDays = (int) ($plan->duration_days ?? 0);
        // If plan has no duration limit, set to null (unlimited)
        $endsAt = $durationDays > 0
            ? $startsAt->copy()->addDays($durationDays)->endOfDay()
            : null;  // Unlimited = null (no expiration)

        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')->with('status', 'اشتراک شما فعال شد.');
    }
}
