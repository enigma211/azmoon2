<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'logo',
        'favicon',
        'footer_column_1',
        'footer_column_2',
        'footer_column_3',
    ];

    protected $casts = [
        'logo' => 'string',
        'favicon' => 'string',
    ];
}
