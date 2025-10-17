<?php

namespace App\Services;

interface SmsProviderInterface
{
    public function sendOtp(string $mobile, string $otp): bool;
}
