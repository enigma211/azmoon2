<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitLog;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log GET requests to avoid tracking API calls or form submissions separately
        if (!$request->isMethod('get')) {
             return $next($request);
        }
        
        // Exclude admin routes and assets
        if ($request->is('admin*') || $request->is('livewire*') || $request->is('filament*')) {
            return $next($request);
        }

        try {
            $ip = $request->ip();
            // Log unique visitor per hour
            $key = 'visit_log_' . $ip . '_' . now()->format('Y-m-d_H');

            if (!Cache::has($key)) {
                VisitLog::create([
                    'ip' => $ip,
                    'user_agent' => substr($request->userAgent() ?? '', 0, 255), // limit length just in case
                ]);
                
                Cache::put($key, true, 3600); // Cache for 1 hour
            }
        } catch (\Exception $e) {
            // Fail silently so we don't break the site if DB is down or something
        }

        return $next($request);
    }
}
