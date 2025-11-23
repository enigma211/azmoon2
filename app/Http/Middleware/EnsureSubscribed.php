<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSubscription;

class EnsureSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins have unlimited access
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Use the model's relationship to ensure consistency (handles NULL ends_at correctly)
        $subscription = $user->activeSubscription()->first();

        if (!$subscription) {
            session()->flash('warning', 'فرصت استفاده رایگان شما به پایان رسیده است. برای ادامه استفاده از سامانه و شرکت در آزمون‌ها، لطفاً اشتراک ویژه تهیه کنید.');
            return redirect()->route('profile');
        }

        $plan = $subscription->subscriptionPlan;
        if ($plan) {
            if ($plan->access_scope === 'domain' && !$request->routeIs('domain.*')) {
                return redirect()->route('domains');
            }
            if ($plan->access_scope === 'batch' && !$request->routeIs('batch.*')) {
                return redirect()->route('domains');
            }
        }

        return $next($request);
    }
}
