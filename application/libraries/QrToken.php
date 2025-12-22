<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QrToken
 * - Generate signed token for monitor QR
 * - Validate token for submit
 *
 * Token format (base64url):
 *   payload = {"ts":1700000000,"nonce":"abcd...","v":1}
 *   token   = base64url(json(payload)) . "." . base64url(hmac_sha256(payload_json, secret))
 *
 * Real-time:
 * - validates ts within window_seconds (e.g. 90 seconds)
 */
class QrToken {

    private string $secret;
    private int $windowSeconds;

    public function __construct(array $params = [])
    {
        $CI =& get_instance();
        $secret = $params['secret'] ?? $CI->config->item('qr_secret');
        if (!$secret) {
            // fallback: read from office_settings later in controller if needed
            $secret = 'CHANGE_ME_QR_SECRET';
        }
        $this->secret = (string)$secret;
        $this->windowSeconds = (int)($params['windowSeconds'] ?? 90);
    }

    public function generate(int $ts = null): string
    {
        $ts = $ts ?? time();
        $payload = [
            'ts' => $ts,
            'nonce' => bin2hex(random_bytes(8)),
            'v' => 1
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sigBin = hash_hmac('sha256', $payloadJson, $this->secret, true);

        return $this->b64url($payloadJson) . '.' . $this->b64url($sigBin);
    }

    public function validate(string $token): array
    {
        $token = trim($token);
        if ($token === '' || strpos($token, '.') === false) {
            return ['ok' => false, 'error' => 'TOKEN_INVALID_FORMAT'];
        }

        [$p1, $p2] = explode('.', $token, 2);

        $payloadJson = $this->b64url_decode($p1, true);
        $sigBin = $this->b64url_decode($p2, false);

        if ($payloadJson === null || $sigBin === null) {
            return ['ok' => false, 'error' => 'TOKEN_DECODE_FAIL'];
        }

        $calcSig = hash_hmac('sha256', $payloadJson, $this->secret, true);
        if (!hash_equals($calcSig, $sigBin)) {
            return ['ok' => false, 'error' => 'TOKEN_BAD_SIGNATURE'];
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload) || !isset($payload['ts'])) {
            return ['ok' => false, 'error' => 'TOKEN_BAD_PAYLOAD'];
        }

        $ts = (int)$payload['ts'];
        $now = time();
        if (abs($now - $ts) > $this->windowSeconds) {
            return ['ok' => false, 'error' => 'TOKEN_EXPIRED', 'ts' => $ts, 'now' => $now];
        }

        return ['ok' => true, 'payload' => $payload];
    }

    private function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64url_decode(string $data, bool $asString): mixed
    {
        $data = strtr($data, '-_', '+/');
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);

        $decoded = base64_decode($data, true);
        if ($decoded === false) return null;

        return $asString ? $decoded : $decoded; // binary ok
    }
}
