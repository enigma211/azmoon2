<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'logo',
        'favicon',
    ];

    protected $casts = [
        'logo' => 'string',
        'favicon' => 'string',
    ];
}
