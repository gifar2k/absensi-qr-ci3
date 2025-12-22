<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Absen extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('Office_settings_model', 'office');
        $this->load->model('User_model', 'user');
        $this->load->model('Device_model', 'device');
        $this->load->model('Attendance_model', 'att');


        
        $this->load->helper(['workday','geo','device','url']);
    }

    public function index()
    {
        $settings = $this->office->get();
        if (empty($settings)) show_error('Office settings not found', 500);

        $token = $this->input->get('token', true);
        $data = [
            'title' => 'Absensi Pegawai',
            'token' => $token,
            'office' => $settings,
            'workday_label' => workday_label($settings['workday_start'] ?? '06:00:00'),
        ];

        $this->load->view('absen/form', $data);
    }

    public function submit()
    {
        
        // only POST
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            show_404();
        }

        $settings = $this->office->get();
        if (empty($settings)) $this->_json(false, 'Office settings not found');

        // 1) validate token QR (real-time)
        $token = trim((string)$this->input->post('token', true));
        if ($token === '') {
            $this->_json(false, 'Token QR kosong. Scan ulang ya.', ['code' => 'TOKEN_EMPTY']);
        }
          $window = (int)($settings['token_window_seconds'] ?? 90);

        $this->load->library('QrToken', [
            'secret' => $settings['qr_secret'],
            'windowSeconds' => $window
        ]);

        $ver = $this->qrtoken->validate($token);
        if (!$ver['ok']) {
            $this->_json(false, 'QR tidak valid / kedaluwarsa. Scan ulang ya.', ['code' => $ver['error'] ?? 'TOKEN_FAIL']);
        }


        // 2) input
        $email  = strtolower(trim((string)$this->input->post('email', true)));
        $action = strtoupper(trim((string)$this->input->post('action', true))); // IN/OUT
        $lat    = $this->input->post('lat', true);
        $lng    = $this->input->post('lng', true);
        $acc    = $this->input->post('accuracy', true);
        $client_id = trim((string)$this->input->post('client_id', true));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_json(false, 'Email belum valid.');
        }

        if (!in_array($action, ['IN','OUT'], true)) {
            $this->_json(false, 'Pilih MASUK atau PULANG dulu ya.');
        }

        // 3) user
        $user = $this->user->get_by_email($email);
        if (!$user) $this->_json(false, 'Email tidak terdaftar / akun nonaktif.');

        // 3.1) rate limit (anti spam server-side) — 1 request / 3 detik per user+device
        $key  = 'absen_rl_' . md5(($client_id ?: 'no_client') . '_' . $user['id']);
        $last = (int)$this->session->userdata($key);
        $now  = time();

if ($last && ($now - $last) < 3) {
    $this->_json(false, 'Terlalu cepat. Tunggu 3 detik lalu coba lagi.');
}

$this->session->set_userdata($key, $now);

       // 4) lokasi wajib + radius strict (user)
if (!is_valid_latlng($lat, $lng)) {
    $this->_json(false, 'Lokasi belum terbaca. Pastikan izin lokasi ON lalu coba lagi.');
}

// ambil dari DB (office_settings)
$officeLat = (float)($settings['lat'] ?? 0);
$officeLng = (float)($settings['lng'] ?? 0);
$radiusM   = (int)($settings['radius_m'] ?? 0);

// guard: office settings wajib valid
if ($officeLat == 0.0 || $officeLng == 0.0 || $radiusM <= 0) {
    $this->_json(false, 'Lokasi kantor belum disetel. Hubungi admin untuk setting lokasi & radius.');
}

// guard: radius masuk akal (anti salah set)
if ($radiusM < 20 || $radiusM > 3000) {
    $this->_json(false, 'Radius kantor tidak valid. Hubungi admin.');
}

$geo = within_radius((float)$lat, (float)$lng, $officeLat, $officeLng, $radiusM);
if (!$geo['ok']) {

    // ✅ debug lengkap biar ketahuan kantor salah / lat-lng kebalik / akurasi jelek
    $this->_json(false, 'Di luar radius kantor. Dekatkan ke lokasi kantor lalu coba lagi.', [
        'code' => 'OUTSIDE_RADIUS',
        'distance_m' => (int)$geo['distance_m'],
        'radius_m'   => $radiusM,

        'user_lat'   => (float)$lat,
        'user_lng'   => (float)$lng,
        'accuracy_m' => is_numeric($acc) ? (int)round($acc) : null,

        'office_lat' => $officeLat,
        'office_lng' => $officeLng,
    ]);
}


        // 5) workday (06:00–05:59)
        $workday = workday_date(null, $settings['workday_start'] ?? '06:00:00');

        // 6) Mode 2 device lock
        $dhash = device_hash($client_id ?: null);
        $dlabel = device_label_guess();

        $lock = $this->device->register_or_touch((int)$user['id'], $dhash, $dlabel);
        if (!$lock['ok']) {
            $this->_json(false, 'Device terkunci untuk akun ini. Minta admin reset dulu ya.', [
                'code' => 'DEVICE_LOCKED'
            ]);
        }

        // 7) rules explicit IN/OUT
        if ($action === 'IN') {
            if ($this->att->has_in((int)$user['id'], $workday)) {
                $this->_json(false, 'Kamu sudah absen MASUK untuk hari kerja ini.');
            }
        } else { // OUT
            if (!$this->att->has_in((int)$user['id'], $workday)) {
                $this->_json(false, 'Belum ada absen MASUK. Tidak bisa PULANG dulu.');
            }
            if ($this->att->has_out((int)$user['id'], $workday)) {
                $this->_json(false, 'Kamu sudah absen PULANG untuk hari kerja ini.');
            }
        }

        // 8) insert log (real-time taken_at by DB)
        $ok = $this->att->insert_log([
            'workday_date' => $workday,
            'user_id' => (int)$user['id'],
            'status' => $action,
            'lat' => (float)$lat,
            'lng' => (float)$lng,
            'accuracy_m' => is_numeric($acc) ? (int)round($acc) : null,
            'distance_m' => (int)$geo['distance_m'],
            'source' => 'qr',
            'note' => null,
        ]);

        if (!$ok) $this->_json(false, 'Gagal simpan absensi. Coba lagi.');

        $this->_json(true, ($action === 'IN' ? 'Absensi MASUK berhasil' : 'Absensi PULANG berhasil'), [
            'name' => $user['name'],
            'email' => $user['email'],
            'status' => $action,
            'workday_date' => $workday,
            'time' => date('H:i:s'),
            'distance_m' => (int)$geo['distance_m'],
            'accuracy_m' => is_numeric($acc) ? (int)round($acc) : null,
        ]);
    }

   private function _json(bool $ok, string $message, array $data = [])
{
    $payload = ['ok' => $ok, 'message' => $message, 'data' => $data];

    // bersihin output buffer biar gak ada sampah
    while (ob_get_level() > 0) { @ob_end_clean(); }

    $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($payload));

    $this->output->_display();
    exit;
}

}
