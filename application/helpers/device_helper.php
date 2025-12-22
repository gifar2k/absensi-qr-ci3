<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Device helper (Mode 2)
 * - Build a stable device_hash from request fingerprint.
 * - Keep it simple, privacy-friendly, and consistent.
 *
 * Note:
 * - We DO NOT rely on IP only (changes).
 * - Use a combination: user-agent + accept-language + platform hints + (optional) client_id from localStorage.
 */

if (!function_exists('normalize_ua')) {
    function normalize_ua(string $ua): string
    {
        $ua = trim($ua);
        // prevent crazy-long UA
        if (strlen($ua) > 300) $ua = substr($ua, 0, 300);
        return $ua;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip(): string
    {
        // try common headers (if behind proxy, you may adjust)
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        // only take first if list
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        return $ip;
    }
}

if (!function_exists('device_fingerprint_string')) {
    /**
     * Build a fingerprint "string" (not hashed yet).
     * You can pass $client_id from JS (localStorage) to stabilize across sessions.
     */
    function device_fingerprint_string(?string $client_id = null): string
    {
        $ua = normalize_ua($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $lang = trim($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'unknown');
        if (strlen($lang) > 100) $lang = substr($lang, 0, 100);

        $client_id = $client_id ? trim($client_id) : '';
        if (strlen($client_id) > 80) $client_id = substr($client_id, 0, 80);

        // IP is optional; include lightly (can change). We keep it but hashed in final.
        $ip = get_client_ip();

        return implode('|', [
            'ua=' . $ua,
            'lang=' . $lang,
            'cid=' . $client_id,
            'ip=' . $ip
        ]);
    }
}

if (!function_exists('device_hash')) {
    /**
     * Hash the fingerprint with app salt.
     * Salt: use CI encryption_key OR a dedicated config.
     */
    function device_hash(?string $client_id = null): string
    {
        $CI =& get_instance();
        $salt = $CI->config->item('encryption_key');
        if (!$salt) {
            // fallback (not recommended). Better set encryption_key in config.php
            $salt = 'CHANGE_ME_SALT';
        }

        $raw = device_fingerprint_string($client_id);

        // sha256 stable
        return hash('sha256', $raw . '::' . $salt);
    }
}

if (!function_exists('device_label_guess')) {
    /**
     * Optional: store a human label based on UA (short).
     */
    function device_label_guess(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Device';
        $ua = normalize_ua($ua);

        // Keep short, just first 60 chars
        return substr($ua, 0, 60);
    }
}
