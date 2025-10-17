<?php

namespace App\Services\Providers;

use App\Services\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

class KavenegarSmsProvider implements SmsProviderInterface
{
    protected ?string $apiKey;
    protected ?string $smsFrom;

    public function __construct(?string $apiKey = null, ?string $smsFrom = null)
    {
        $this->apiKey = $apiKey;
        $this->smsFrom = $smsFrom;
    }

    public function sendOtp(string $mobile, string $otp): bool
    {
        // TODO: Integrate real Kavenegar API here with $this->apiKey and $this->smsFrom
        Log::info("[KavenegarSmsProvider] Sending OTP {$otp} to {$mobile}");
        return true;
    }
}
