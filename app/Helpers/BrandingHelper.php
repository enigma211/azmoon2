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
            // Return the synchronized root file which is SEO friendly
            // Append timestamp to bust cache if possible, or just the file
            return asset('favicon.png') . '?v=' . ($settings->updated_at?->timestamp ?? time());
        }

        // If no favicon set, check if we have a default one manually placed
        if (file_exists(public_path('favicon.png'))) {
            return asset('favicon.png');
        }

        return null;
    }
}
