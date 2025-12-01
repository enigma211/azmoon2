<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\SystemSetting;
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
    public $email = '';
    public $otpSent = false;
    public $userExists = false;
    public $countdown = 0;

    protected $rules = [
        'mobile' => ['required', 'regex:/^09\d{9}$/'],
        'otp' => 'required|digits:6',
        'firstName' => 'required|string|min:2|max:50',
        'lastName' => 'required|string|min:2|max:50',
        'email' => 'required|email|max:255',
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
        'email.required' => 'ایمیل الزامی است',
        'email.email' => 'فرمت ایمیل صحیح نیست',
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

        // Store OTP in Cache (5 minutes expiration)
        \Illuminate\Support\Facades\Cache::put("otp_{$this->mobile}", $otp, now()->addMinutes(5));

        $this->otpSent = true;
        $this->step = 'otp';
        $this->countdown = 120; // 2 minutes countdown

        session()->flash('success', 'کد تایید به شماره ' . $this->mobile . ' ارسال شد.');
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => $this->rules['otp']], $this->messages);

        // Retrieve OTP from Cache
        $cachedOtp = \Illuminate\Support\Facades\Cache::get("otp_{$this->mobile}");

        if (!$cachedOtp) {
            $this->addError('otp', 'کد تایید منقضی شده است. لطفا دوباره درخواست دهید.');
            $this->reset(['step', 'otp', 'otpSent']);
            return;
        }

        if ($this->otp !== $cachedOtp) {
            $this->addError('otp', 'کد تایید نادرست است.');
            return;
        }

        // OTP is valid - Clear it
        \Illuminate\Support\Facades\Cache::forget("otp_{$this->mobile}");

        if ($this->userExists) {
            // Login existing user
            $user = User::where('mobile', $this->mobile)->first();
            Auth::login($user);
            
            return redirect()->route('profile');
        } else {
            // New user - Mark mobile as verified in Cache for 10 minutes to allow registration
            \Illuminate\Support\Facades\Cache::put("verified_{$this->mobile}", true, now()->addMinutes(10));
            
            // Ask for name
            $this->step = 'register';
        }
    }

    public function completeRegistration()
    {
        $this->validate([
            'firstName' => $this->rules['firstName'],
            'lastName' => $this->rules['lastName'],
            'email' => $this->rules['email'],
        ], $this->messages);

        // Verify that this mobile number was actually verified recently
        if (!\Illuminate\Support\Facades\Cache::get("verified_{$this->mobile}")) {
            $this->addError('firstName', 'زمان مجاز ثبت‌نام تمام شده است. لطفا مجددا احراز هویت کنید.');
            $this->resetForm();
            return;
        }

        try {
            // Check if email already exists
            $existingEmail = User::where('email', $this->email)->first();
            if ($existingEmail) {
                $this->addError('email', 'این ایمیل قبلاً ثبت شده است.');
                return;
            }

            // Create new user
            $user = User::create([
                'name' => trim($this->firstName . ' ' . $this->lastName),
                'email' => $this->email,
                'username' => $this->mobile, // Use mobile as username
                'password' => bcrypt(str()->random(16)),
                'mobile' => $this->mobile,
                // Role is handled by default or logic elsewhere
            ]);

            // Assign free plan to new user
            $freePlan = SubscriptionPlan::where('price_toman', 0)
                ->where('is_active', true)
                ->first();

            if ($freePlan) {
                // Get trial duration from system settings (default 48 hours)
                $settings = SystemSetting::first();
                $trialHours = $settings ? ($settings->free_trial_hours ?? 48) : 48;

                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $freePlan->id,
                    'starts_at' => now(),
                    'ends_at' => now()->addHours($trialHours),
                    'status' => 'active',
                ]);
            }

            // Login the new user
            Auth::login($user);

            // Cleanup
            \Illuminate\Support\Facades\Cache::forget("verified_{$this->mobile}");

            session()->flash('success', 'ثبت‌نام با موفقیت انجام شد. خوش آمدید!');

            return redirect()->route('profile');
        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            $this->addError('firstName', 'خطا در ثبت‌نام: ' . $e->getMessage());
            return;
        }
    }

    public function resetForm()
    {
        $this->reset(['step', 'mobile', 'otp', 'firstName', 'lastName', 'email', 'otpSent', 'userExists', 'countdown']);
        // No session to clear
    }

    public function render()
    {
        return view('livewire.auth.otp-login');
    }
}
