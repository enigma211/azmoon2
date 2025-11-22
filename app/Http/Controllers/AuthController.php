<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login_otp');
    }

    public function login(Request $request, SmsService $smsService)
    {
        $validated = $request->validate([
            'mobile' => ['required','digits:10'],
        ]);

        $mobile = $validated['mobile'];

        // Ensure a user exists for this mobile (email/password not required for OTP flow)
        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'User '.$mobile,
                'email' => $mobile.'@example.local',
                'username' => $mobile, // Use mobile as username
                'password' => bcrypt(str()->random(16)),
                'mobile' => $mobile,
                'role' => 'student',
            ]);

            // Assign free plan to new user
            $freePlan = SubscriptionPlan::where('price_toman', 0)
                ->where('is_active', true)
                ->first();

            if ($freePlan) {
                // Get trial duration from settings (default 48 hours)
                $settings = \App\Models\SystemSetting::first();
                $trialHours = $settings ? ($settings->free_trial_hours ?? 48) : 48;

                // Gift Subscription
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $freePlan->id,
                    'starts_at' => now(),
                    'ends_at' => now()->addHours($trialHours),
                    'status' => 'active',
                ]);
            }
        }

        // Generate and send OTP
        $otp = (string) random_int(100000, 999999);
        $smsService->sendOtp($mobile, $otp);

        // Store OTP in session (for production, prefer cache/DB with TTL)
        session([
            'otp' => $otp,
            'mobile' => $mobile,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        return redirect()->route('verifyOtp');
    }

    public function verifyOtpForm()
    {
        return view('auth.verifyOtp');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'otp' => ['required','digits:6'],
        ]);

        $expected = session('otp');
        $mobile = session('mobile');
        $expires = session('otp_expires_at');

        if (!$expected || !$mobile) {
            return back()->withErrors(['otp' => 'Session expired. Please request a new OTP.']);
        }

        if ($expires && now()->greaterThan($expires)) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new OTP.']);
        }

        if ($validated['otp'] !== $expected) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            return back()->withErrors(['otp' => 'User not found for this mobile.']);
        }

        Auth::login($user);

        // Cleanup session OTP
        session()->forget(['otp','otp_expires_at']);

        return redirect()->route('dashboard');
    }
}
