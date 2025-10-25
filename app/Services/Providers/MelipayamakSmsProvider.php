<?php

namespace App\Services\Providers;

use App\Services\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

class MelipayamakSmsProvider implements SmsProviderInterface
{
    protected ?string $username;
    protected ?string $password;
    protected ?string $from;

    public function __construct(?string $username = null, ?string $password = null, ?string $from = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->from = $from;
    }

    public function sendOtp(string $mobile, string $otp): bool
    {
        try {
            if (!class_exists(\Melipayamak\Laravel\Facade::class)) {
                Log::warning('[MelipayamakSmsProvider] melipayamak/laravel package not installed.');
                return false;
            }

            // Configure credentials at runtime from DB settings
            config()->set('melipayamak.username', $this->username);
            config()->set('melipayamak.password', $this->password);

            $sms = \Melipayamak\Laravel\Facade::sms();
            $text = "کد ورود شما: {$otp}";
            $from = $this->from ?: config('melipayamak.from');

            $response = $sms->send($mobile, $from, $text);
            Log::info('[MelipayamakSmsProvider] send response: ' . (string) $response);
            return true;
        } catch (\Throwable $e) {
            Log::error('[MelipayamakSmsProvider] error sending OTP: ' . $e->getMessage());
            return false;
        }
    }
}
