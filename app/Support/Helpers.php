<?php

use Carbon\Carbon;

if (!function_exists('date_display')) {
    function date_display($date)
    {
        if (!$date) return '';
        return Carbon::parse($date)->format('d/m/Y');
    }
}

if (!function_exists('date_db')) {
    function date_db($date)
    {
        if (!$date) return null;
        return Carbon::parse($date)->format('Y-m-d');
    }
}

if (!function_exists('date_input')) {
    function date_input($date)
    {
        if (!$date) return null;
        return Carbon::parse($date)->format('Y-m-d');
    }
}
