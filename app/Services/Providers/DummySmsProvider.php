<?php

namespace App\Services\Providers;

use App\Services\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

class DummySmsProvider implements SmsProviderInterface
{
    public function sendOtp(string $mobile, string $otp): bool
    {
        Log::info("[DummySmsProvider] Sending OTP {$otp} to {$mobile}");
        return true;
    }
}
