<?php defined('BASEPATH') OR exit('No direct script access allowed');

function b64url_encode(string $bin): string {
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function b64url_decode(string $str): string {
    $pad = strlen($str) % 4;
    if ($pad) $str .= str_repeat('=', 4 - $pad);
    return base64_decode(strtr($str, '-_', '+/'));
}

function make_qr_token(string $secret, int $ts, string $nonce): string {
    $payload = $ts . '|' . $nonce;
    $sig = hash_hmac('sha256', $payload, $secret, true);
    return b64url_encode($payload) . '.' . b64url_encode($sig);
}

function verify_qr_token(string $token, string $secret, int $window_seconds): array {
    $parts = explode('.', $token, 2);
    if (count($parts) !== 2) return ['ok' => false, 'msg' => 'format'];

    $payload = b64url_decode($parts[0]);
    $sig = b64url_decode($parts[1]);

    $calc = hash_hmac('sha256', $payload, $secret, true);
    if (!hash_equals($calc, $sig)) return ['ok' => false, 'msg' => 'bad_sig'];

    $pp = explode('|', $payload, 2);
    if (count($pp) !== 2) return ['ok' => false, 'msg' => 'bad_payload'];

    $ts = (int)$pp[0];
    $now = time();
    if ($ts <= 0) return ['ok' => false, 'msg' => 'bad_ts'];

    // valid if within window (past)
    if (($now - $ts) > $window_seconds) return ['ok' => false, 'msg' => 'expired'];
    if (($ts - $now) > 15) return ['ok' => false, 'msg' => 'future']; // toleransi jam server beda

    return ['ok' => true, 'ts' => $ts, 'nonce' => $pp[1]];
}
