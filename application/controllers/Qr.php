<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Qr extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Office_settings_model', 'office');
       
    }

    /**
     * Premium monitor page (QR + countdown)
     */
    public function index()
    {
        $settings = $this->office->get();
        if (empty($settings)) show_error('Office settings not found', 500);

        $data = [
            'title' => 'QR Absensi - iLog Computer',
            'office' => $settings,
            'refresh_seconds' => (int)($settings['qr_refresh_seconds'] ?? 15),
        ];

        $this->load->view('qr/display', $data);
    }

    /**
     * Real-time PNG QR generator
     * URL: /qr/png?ts=... (optional)
     */
   public function png()
{
    // matiin kompresi output biar PNG gak rusak
    @ini_set('zlib.output_compression', '0');
    @ini_set('output_buffering', '0');

    $settings = $this->office->get();
    if (empty($settings)) {
        header('Content-Type: text/plain'); echo "Office settings not found"; exit;
    }

    // init token lib with DB secret
   $window = (int)($settings['token_window_seconds'] ?? 90);

$this->load->library('QrToken', [
    'secret' => $settings['qr_secret'],
    'windowSeconds' => $window
]);


    $token = $this->qrtoken->generate(time());
    $url   = site_url('absen?token=' . rawurlencode($token));

    // ✅ pakai file phpqrcode.php (lebih aman daripada qrlib.php di beberapa distro)
    $path = APPPATH . 'third_party/phpqrcode/phpqrcode.php';
    if (!file_exists($path)) {
        // fallback kalau kamu cuma punya qrlib.php
        $path = APPPATH . 'third_party/phpqrcode/qrlib.php';
    }
    if (!file_exists($path)) {
        header('Content-Type: text/plain');
        echo "phpqrcode not found:\n" . APPPATH . "third_party/phpqrcode/(phpqrcode.php|qrlib.php)";
        exit;
    }
    require_once $path;

    // ✅ pastiin folder cache CI writable
    $cacheDir = APPPATH . 'cache/';
    if (!is_dir($cacheDir) || !is_writable($cacheDir)) {
        header('Content-Type: text/plain');
        echo "Folder tidak writable: {$cacheDir}\n(cek permission application/cache)";
        exit;
    }

    // generate ke file dulu
    $tmp = $cacheDir . 'qr_' . time() . '_' . bin2hex(random_bytes(3)) . '.png';

    // beberapa server butuh konstanta ini ada:
    // QR_ECLEVEL_M harus ada dari library
    QRcode::png($url, $tmp, QR_ECLEVEL_M, 10, 2);

    if (!file_exists($tmp) || filesize($tmp) < 100) {
        header('Content-Type: text/plain');
        echo "QR generate failed. tmp={$tmp}";
        exit;
    }

    // bersihin buffer output (kalau ada)
    while (ob_get_level() > 0) ob_end_clean();

    header('Content-Type: image/png');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Content-Length: ' . filesize($tmp));

    readfile($tmp);
    @unlink($tmp);
    exit;
}


}
