<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OtpLogin extends Component
{
    public $step = 'mobile'; // mobile, otp, register
    public $mobile = '';
    public $otp = '';
    public $firstName = '';
    public $lastName = '';
    public $otpSent = false;
    public $userExists = false;
    public $countdown = 0;

    protected $rules = [
        'mobile' => 'required|regex:/^09\d{9}$/',
        'otp' => 'required|digits:6',
        'firstName' => 'required|string|min:2|max:50',
        'lastName' => 'required|string|min:2|max:50',
    ];

    protected $messages = [
        'mobile.required' => 'شماره موبایل الزامی است',
        'mobile.regex' => 'شماره موبایل باید با فرمت 09xxxxxxxxx باشد',
        'otp.required' => 'کد تایید الزامی است',
        'otp.digits' => 'کد تایید باید 6 رقم باشد',
        'firstName.required' => 'نام الزامی است',
        'firstName.min' => 'نام باید حداقل 2 کاراکتر باشد',
        'lastName.required' => 'نام خانوادگی الزامی است',
        'lastName.min' => 'نام خانوادگی باید حداقل 2 کاراکتر باشد',
    ];

    public function sendOtp(SmsService $smsService)
    {
        $this->validate(['mobile' => $this->rules['mobile']], $this->messages);

        // Check if user exists
        $user = User::where('mobile', $this->mobile)->first();
        $this->userExists = $user !== null;

        // Generate OTP
        $otp = (string) random_int(100000, 999999);

        // Send SMS
        $sent = $smsService->sendOtp($this->mobile, $otp);

        if (!$sent) {
            $this->addError('mobile', 'خطا در ارسال پیامک. لطفا دوباره تلاش کنید.');
            return;
        }

        // Store OTP in session
        session([
            'otp' => $otp,
            'mobile' => $this->mobile,
            'otp_expires_at' => now()->addMinutes(5),
            'user_exists' => $this->userExists,
        ]);

        $this->otpSent = true;
        $this->step = 'otp';
        $this->countdown = 120; // 2 minutes countdown

        session()->flash('success', 'کد تایید به شماره ' . $this->mobile . ' ارسال شد.');
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => $this->rules['otp']], $this->messages);

        $expectedOtp = session('otp');
        $sessionMobile = session('mobile');
        $expires = session('otp_expires_at');
        $userExists = session('user_exists', false);

        if (!$expectedOtp || !$sessionMobile) {
            $this->addError('otp', 'جلسه منقضی شده است. لطفا دوباره تلاش کنید.');
            $this->reset(['step', 'mobile', 'otp', 'otpSent']);
            return;
        }

        if ($expires && now()->greaterThan($expires)) {
            $this->addError('otp', 'کد تایید منقضی شده است. لطفا دوباره درخواست دهید.');
            return;
        }

        if ($this->otp !== $expectedOtp) {
            $this->addError('otp', 'کد تایید نادرست است.');
            return;
        }

        // OTP is valid
        if ($userExists) {
            // Login existing user
            $user = User::where('mobile', $sessionMobile)->first();
            Auth::login($user);

            session()->forget(['otp', 'mobile', 'otp_expires_at', 'user_exists']);
            
            return redirect()->route('profile');
        } else {
            // New user - ask for name
            $this->step = 'register';
        }
    }

    public function completeRegistration()
    {
        $this->validate([
            'firstName' => $this->rules['firstName'],
            'lastName' => $this->rules['lastName'],
        ], $this->messages);

        $sessionMobile = session('mobile');

        if (!$sessionMobile) {
            $this->addError('firstName', 'جلسه منقضی شده است. لطفا دوباره تلاش کنید.');
            return;
        }

        // Create new user
        $user = User::create([
            'name' => trim($this->firstName . ' ' . $this->lastName),
            'email' => $sessionMobile . '@example.local',
            'password' => bcrypt(str()->random(16)),
            'mobile' => $sessionMobile,
            'role' => 'student',
        ]);

        // Assign free plan to new user
        $freePlan = SubscriptionPlan::where('price_toman', 0)
            ->where('is_active', true)
            ->first();

        if ($freePlan) {
            $endsAt = $freePlan->duration_days == 0 
                ? null  // Unlimited
                : now()->addDays($freePlan->duration_days);

            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $freePlan->id,
                'starts_at' => now(),
                'ends_at' => $endsAt,
                'status' => 'active',
            ]);
        }

        // Login the new user
        Auth::login($user);

        session()->forget(['otp', 'mobile', 'otp_expires_at', 'user_exists']);

        session()->flash('success', 'ثبت‌نام با موفقیت انجام شد. خوش آمدید!');

        return redirect()->route('profile');
    }

    public function resetForm()
    {
        $this->reset(['step', 'mobile', 'otp', 'firstName', 'lastName', 'otpSent', 'userExists', 'countdown']);
        session()->forget(['otp', 'mobile', 'otp_expires_at', 'user_exists']);
    }

    public function render()
    {
        return view('livewire.auth.otp-login');
    }
}
