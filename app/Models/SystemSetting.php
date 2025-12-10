<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'free_trial_hours',
        'otp_enabled',
        'sms_provider',
        'sms_api_key',
        'sms_from',
        'terms_content',
        'about_content',
    ];

    protected $casts = [
        'value' => 'string',
        'free_trial_hours' => 'integer',
        'otp_enabled' => 'string',
        'sms_provider' => 'string',
        'sms_api_key' => 'string',
        'sms_from' => 'string',
    ];
}
