<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianMobile implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^09\d{9}$/', $value)) {
            $fail('شماره موبایل باید با 09 شروع شود و 11 رقم باشد.');
        }
    }
}
