<?php

use Carbon\Carbon;

if (!function_exists('jdate')) {
    /**
     * Format a Gregorian date using the given PHP date format.
     * This is a compatibility shim after removing Jalali dependency.
     */
    function jdate($date = null, $format = 'Y/m/d')
    {
        try {
            $dt = $date ? Carbon::parse($date) : Carbon::now();
            return $dt->format($format);
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}

if (!function_exists('jdate_time')) {
    /**
     * Format a Gregorian datetime (compat shim), Y/m/d H:i
     */
    function jdate_time($date = null)
    {
        return jdate($date, 'Y/m/d H:i');
    }
}
