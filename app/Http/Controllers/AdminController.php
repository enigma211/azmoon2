<?php

namespace App\Http\Controllers;

use App\Rules\IranianMobile;
use App\Services\SmsService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Send test SMS
     */
    public function sendTestSms(Request $request, SmsService $sms)
    {
        $data = $request->validate([
            'mobile' => ['required', new IranianMobile],
            'text' => ['required', 'string', 'max:500'],
        ]);

        $success = $sms->sendText($data['mobile'], $data['text']);

        return back()->with(
            $success ? 'success' : 'error',
            $success ? 'پیامک با موفقیت ارسال شد.' : 'ارسال پیامک ناموفق بود.'
        );
    }
}
