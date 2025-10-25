<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'otp_enabled',
        'sms_provider',
        'sms_api_key',
        'sms_from',
        'sms_username',
        'sms_password',
    ];

    protected $casts = [
        'value' => 'string',
        'otp_enabled' => 'string',
        'sms_provider' => 'string',
        'sms_api_key' => 'string',
        'sms_from' => 'string',
        'sms_username' => 'string',
        'sms_password' => 'string',
    ];
}
