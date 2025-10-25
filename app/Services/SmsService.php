<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Services\Providers\DummySmsProvider;
use App\Services\Providers\KavenegarSmsProvider;

class SmsService
{
    protected function getSetting(string $key, ?string $default = null): ?string
    {
        $row = SystemSetting::where('key', $key)->first();
        if ($row && $row->value !== null) {
            return (string) $row->value;
        }
        // Fallback to column-style storage if present
        $first = SystemSetting::first();
        if ($first && isset($first->{$key}) && $first->{$key} !== null) {
            return (string) $first->{$key};
        }
        return $default;
    }

    public function isOtpEnabled(): bool
    {
        $val = $this->getSetting('otp_enabled', 'false');
        return in_array(strtolower((string) $val), ['1', 'true', 'yes'], true);
    }

    public function sendOtp(string $mobile, string $otp): bool
    {
        $provider = strtolower($this->getSetting('sms_provider', 'dummy'));
        $apiKey = $this->getSetting('sms_api_key');
        $from = $this->getSetting('sms_from');

        switch ($provider) {
            case 'kavenegar':
                $smsProvider = new KavenegarSmsProvider($apiKey, $from);
                break;
            case 'ghasedak':
            case 'melipayamak':
                // TODO: Implement real providers; for now behave like Kavenegar stub
                $smsProvider = new KavenegarSmsProvider($apiKey, $from);
                break;
            case 'dummy':
            default:
                $smsProvider = new DummySmsProvider();
        }

        return $smsProvider->sendOtp($mobile, $otp);
    }
}
