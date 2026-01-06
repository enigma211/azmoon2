<?php

namespace App\Services;

use Melipayamak\Laravel\Facade as Melipayamak;

class SmsService
{
    public function isOtpEnabled(): bool
    {
        $val = config('melipayamak.otp_enabled', true);
        if (is_bool($val)) return $val;
        return in_array(strtolower((string) $val), ['1', 'true', 'yes'], true);
    }

    public function sendOtp(string $mobile, string $otp): bool
    {
        $bodyId = config('melipayamak.otp_body_id');
        
        // اگر bodyId تنظیم شده باشد، از خط خدماتی (الگو) استفاده می‌کنیم
        if (!empty($bodyId)) {
            \Log::info('OTP SMS: Using shared service line (pattern)', [
                'mobile' => $mobile,
                'bodyId' => $bodyId,
            ]);
            return $this->sendOtpByPattern($mobile, $otp, $bodyId);
        }
        
        // اگر bodyId تنظیم نشده، از روش مستقیم استفاده می‌کنیم (نیاز به شماره اختصاصی)
        \Log::warning('OTP SMS: bodyId not configured, falling back to direct send', [
            'mobile' => $mobile,
        ]);
        return $this->sendOtpDirect($mobile, $otp);
    }

    /**
     * ارسال OTP از طریق خط خدماتی (الگو) - SendByBaseNumber
     */
    protected function sendOtpByPattern(string $mobile, string $otp, $bodyId): bool
    {
        try {
            $sms = Melipayamak::sms('soap');
            
            // پارامترهای الگو - در مورد شما فقط یک پارامتر {0} برای کد OTP
            $text = [$otp];
            
            \Log::info('OTP SMS: Sending via pattern (SendByBaseNumber)', [
                'mobile' => $mobile,
                'bodyId' => $bodyId,
                'otp' => $otp,
            ]);
            
            $response = $sms->sendByBaseNumber($text, $mobile, (int) $bodyId);
            
            \Log::info('OTP SMS: Pattern response received', [
                'mobile' => $mobile,
                'response' => $response,
            ]);
            
            // بررسی موفقیت - معمولاً یک RecId برمی‌گرداند
            if (!empty($response) && is_numeric($response) && $response > 0) {
                return true;
            }
            
            \Log::warning('OTP SMS: Pattern API returned non-success status', [
                'mobile' => $mobile,
                'response' => $response,
            ]);
            
            return false;
        } catch (\Throwable $e) {
            \Log::error('OTP SMS: Pattern sending failed', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
            return false;
        }
    }

    /**
     * ارسال OTP به روش مستقیم (روش قدیمی)
     */
    protected function sendOtpDirect(string $mobile, string $otp): bool
    {
        $from = config('melipayamak.from');
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
            
            \Log::warning('OTP SMS: Direct API returned non-success status', [
                'mobile' => $mobile,
                'response' => $response,
            ]);
            
            return false;
        } catch (\Throwable $e) {
            \Log::error('OTP SMS: Direct sending failed', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return false;
        }
    }

    public function sendText(string $mobile, string $text): bool
    {
        $from = config('melipayamak.from');
        
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
