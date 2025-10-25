<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        // Redirect to Filament page for unified admin UI (with sidebar)
        return redirect()->to('/admin/system-settings');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'otp_enabled' => 'nullable', // checkbox
            'sms_provider' => 'required|in:dummy,kavenegar,ghasedak,melipayamak',
            'sms_api_key' => 'nullable|string',
            'sms_from' => 'nullable|string',
            'sms_username' => 'nullable|string',
            'sms_password' => 'nullable|string',
        ]);

        $settings = SystemSetting::first();
        if (!$settings) {
            $settings = new SystemSetting();
        }

        // Normalize checkbox to 'true'/'false' strings for compatibility with SmsService
        $otpEnabled = $request->boolean('otp_enabled') ? 'true' : 'false';

        $settings->otp_enabled = $otpEnabled;
        $settings->sms_provider = $validated['sms_provider'];
        $settings->sms_api_key = $validated['sms_api_key'] ?? null;
        $settings->sms_from = $validated['sms_from'] ?? null;
        $settings->sms_username = $validated['sms_username'] ?? null;
        $settings->sms_password = $validated['sms_password'] ?? null;
        $settings->save();

        return back()->with('success', 'Settings updated successfully.');
    }
}
