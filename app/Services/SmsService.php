<?php

namespace App\Services;

use Melipayamak\Laravel\Facade as Melipayamak;

class SmsService
{
    public function isOtpEnabled(): bool
    {
        $val = config('melipayamak.otp_enabled', env('OTP_ENABLED', true));
        if (is_bool($val)) return $val;
        return in_array(strtolower((string) $val), ['1', 'true', 'yes'], true);
    }

    public function sendOtp(string $mobile, string $otp): bool
    {
        $from = config('melipayamak.from', env('MELIPAYAMAK_FROM'));
        $template = config('melipayamak.otp_template', 'کد ورود شما: {code}');
        $text = str_replace('{code}', $otp, $template);

        try {
            $sms = Melipayamak::sms();
            $response = $sms->send($mobile, (string) $from, (string) $text);
            
            // Parse response to check if successful
            $result = json_decode($response, true);
            if (isset($result['RetStatus']) && $result['RetStatus'] == 1) {
                return true;
            }
            
            \Log::warning('OTP SMS: API returned non-success status', [
                'mobile' => $mobile,
                'response' => $response,
            ]);
            
            return false;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    public function sendText(string $mobile, string $text): bool
    {
        $from = config('melipayamak.from', env('MELIPAYAMAK_FROM'));
        
        \Log::info('SMS Test: Attempting to send', [
            'mobile' => $mobile,
            'from' => $from,
            'text_length' => strlen($text),
        ]);
        
        try {
            $sms = Melipayamak::sms();
            $response = $sms->send($mobile, (string) $from, (string) $text);
            
            \Log::info('SMS Test: Response received', [
                'response' => $response,
            ]);
            
            // Parse response to check if successful
            $result = json_decode($response, true);
            if (isset($result['RetStatus']) && $result['RetStatus'] == 1) {
                return true;
            }
            
            \Log::warning('SMS Test: API returned non-success status', [
                'response' => $response,
            ]);
            
            return false;
        } catch (\Throwable $e) {
            \Log::error('SMS Test: Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
            return false;
        }
    }
}
