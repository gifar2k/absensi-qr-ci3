<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Geo helper (strict radius)
 * - haversine distance (meters)
 * - simple validators
 */

if (!function_exists('haversine_m')) {
    /**
     * Calculate distance in meters between 2 coordinates.
     */
    function haversine_m(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $earthRadius = 6371000; // meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2)
           + cos($lat1Rad) * cos($lat2Rad)
           * sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = (int) round($earthRadius * $c);

        return $d;
    }
}

if (!function_exists('is_valid_latlng')) {
    function is_valid_latlng($lat, $lng): bool
    {
        if (!is_numeric($lat) || !is_numeric($lng)) return false;
        $lat = (float)$lat;
        $lng = (float)$lng;
        return ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180);
    }
}

if (!function_exists('within_radius')) {
    /**
     * Return array with ok + distance_m.
     */
    function within_radius(
        float $userLat,
        float $userLng,
        float $officeLat,
        float $officeLng,
        int $radiusM
    ): array {
        $distance = haversine_m($userLat, $userLng, $officeLat, $officeLng);
        return [
            'ok' => ($distance <= $radiusM),
            'distance_m' => $distance
        ];
    }
}
