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

        // Check if we are accessing an exam and if its batch is free
        $exam = $request->route('exam');
        if ($exam) {
            // If $exam is an ID string, resolve it (safety check)
            if (!($exam instanceof \App\Models\Exam)) {
                 $exam = \App\Models\Exam::find($exam);
            }

            if ($exam && $exam->batch && $exam->batch->is_free) {
                return $next($request);
            }
        }

        // Use the model's relationship to ensure consistency (handles NULL ends_at correctly)
        $subscription = $user->activeSubscription()->first();

        if (!$subscription) {
            session()->flash('warning', 'برای شرکت در این آزمون، لطفاً اشتراک ویژه تهیه کنید.');
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
