<?php

use Ariaieboy\Jalali\Jalali;
use Carbon\Carbon;

if (!function_exists('jdate')) {
    /**
     * Format a date to Jalali (Persian) calendar.
     * Uses ariaieboy/jalali package.
     * 
     * @param mixed $date Date to convert (Carbon, DateTime, string, or timestamp)
     * @param string|null $format Optional format string (e.g., 'Y/m/d' or '%d %B %Y')
     * @return \Ariaieboy\Jalali\Jalali|string
     */
    function jdate($date = null, $format = null)
    {
        try {
            if ($date === null) {
                $jalali = Jalali::now();
            } elseif ($date instanceof \DateTimeInterface) {
                $jalali = Jalali::fromCarbon(Carbon::instance($date));
            } elseif (is_numeric($date)) {
                $jalali = Jalali::forge($date);
            } else {
                $jalali = Jalali::forge($date);
            }
            
            // If format is provided, return formatted string
            if ($format !== null) {
                return $jalali->format($format);
            }
            
            // Otherwise return the Jalali object for chaining
            return $jalali;
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }
}

if (!function_exists('jdate_time')) {
    /**
     * Format a Jalali datetime, Y/m/d H:i
     */
    function jdate_time($date = null)
    {
        return jdate($date, 'Y/m/d H:i');
    }
}
