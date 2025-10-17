<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Auto-assign free subscription plan (ID = 1) on registration
        try {
            $freePlan = SubscriptionPlan::query()
                ->where('id', 1)
                ->where('is_active', true)
                ->first();

            if ($freePlan) {
                $startsAt = Carbon::now()->startOfDay();
                $durationDays = (int) ($freePlan->duration_days ?? 0);
                $endsAt = $durationDays > 0
                    ? $startsAt->copy()->addDays($durationDays)->endOfDay()
                    : null; // unlimited

                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $freePlan->id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => 'active',
                ]);
            }
        } catch (\Throwable $e) {
            // Silently ignore to avoid blocking registration, but could be logged
        }

        return redirect(route('dashboard', absolute: false));
    }
}
