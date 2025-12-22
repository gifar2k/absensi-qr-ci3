<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Workday helper (06:00 - 05:59 next day)
 * Real-time: based on server time (PHP).
 */

if (!function_exists('now_jkt')) {
    function now_jkt(): DateTime {
        return new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    }
}

/**
 * Return "workday_date" (Y-m-d) based on given DateTime or current time.
 * Rule: if time < 06:00:00 => workday is previous day.
 */
if (!function_exists('workday_date')) {
    function workday_date(?DateTime $dt = null, string $start = '06:00:00'): string {
        $dt = $dt ?: now_jkt();

        // clone to avoid side-effects
        $x = clone $dt;

        [$h, $m, $s] = array_map('intval', explode(':', $start));
        $startToday = (clone $x)->setTime($h, $m, $s);

        if ($x < $startToday) {
            $x->modify('-1 day');
        }
        return $x->format('Y-m-d');
    }
}

/**
 * Useful for display label: "Hari Kerja: 2025-12-19 (06:00–05:59)"
 */
if (!function_exists('workday_label')) {
    function workday_label(string $start = '06:00:00'): string {
        $wd = workday_date(null, $start);
        return "Hari Kerja: {$wd} (06:00–05:59)";
    }
}
