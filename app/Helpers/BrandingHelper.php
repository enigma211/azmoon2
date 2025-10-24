<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class BrandingHelper
{
    public static function getLogo(): ?string
    {
        $settings = SiteSetting::first();
        if ($settings && $settings->logo) {
            return Storage::disk('public')->url($settings->logo);
        }
        return null;
    }

    public static function getFavicon(): ?string
    {
        $settings = SiteSetting::first();
        if ($settings && $settings->favicon) {
            return Storage::disk('public')->url($settings->favicon);
        }
        return null;
    }
}
