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

        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest('ends_at')
            ->first();

        if (!$subscription) {
            return redirect()->route('pricing');
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
