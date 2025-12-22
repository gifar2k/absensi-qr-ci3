<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url','security','form']);
        $this->load->database();
    }

    public function index() {
        redirect('admin/login');
    }

    public function login() {
        if ($this->session->userdata('admin_id')) {
            redirect('admin/dashboard');
            return;
        }

        $data = ['title' => 'Admin Login', 'err' => ''];

        if ($this->input->method(TRUE) === 'POST') {
            $email = strtolower(trim((string)$this->input->post('email', TRUE)));
            $pass  = (string)$this->input->post('password', FALSE);

            if ($email === '' || $pass === '') {
                $data['err'] = 'Email dan password wajib diisi.';
            } else {
                $user = $this->db->select('id,name,email,password_hash,role,is_active')
                    ->from('users')
                    ->where('email', $email)
                    ->where_in('role', ['admin','superadmin'])
                    ->limit(1)->get()->row_array();

                if (!$user || (int)$user['is_active'] !== 1) {
                    $data['err'] = 'Akun tidak ditemukan / nonaktif.';
                } elseif (!password_verify($pass, (string)$user['password_hash'])) {
                    $data['err'] = 'Password salah.';
                } else {
                    $this->session->set_userdata([
                        'admin_id'    => (int)$user['id'],
                        'admin_name'  => (string)$user['name'],
                        'admin_email' => (string)$user['email'],
                        'admin_role'  => (string)$user['role'],
                    ]);
                    redirect('admin/dashboard');
                    return;
                }
            }
        }

        $this->load->view('admin/auth_login', $data);
    }

    public function logout() {
        $this->session->unset_userdata(['admin_id','admin_name','admin_email','admin_role']);
        $this->session->sess_regenerate(TRUE);
        redirect('admin/login');
    }

    private function guard_admin(): void {
        if (!$this->session->userdata('admin_id')) {
            redirect('admin/login');
            exit;
        }
    }

    public function dashboard() {
        $this->guard_admin();

        $data = [
            'title' => 'Admin Dashboard',
            'admin' => [
                'id'    => (int)$this->session->userdata('admin_id'),
                'name'  => (string)$this->session->userdata('admin_name'),
                'email' => (string)$this->session->userdata('admin_email'),
                'role'  => (string)$this->session->userdata('admin_role'),
            ],
        ];

        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('admin/layout/footer', $data);
    }

    private function admin_data(): array {
    return [
        'id'    => (int)$this->session->userdata('admin_id'),
        'name'  => (string)$this->session->userdata('admin_name'),
        'email' => (string)$this->session->userdata('admin_email'),
        'role'  => (string)$this->session->userdata('admin_role'),
    ];
}

public function users() {
    $this->guard_admin();
    $this->load->model('User_model');

    $rows = $this->User_model->get_staff_rows();
    $device_map = $this->User_model->get_active_device_map();


    $data = [
        'title' => 'Kelola Users',
        'admin' => $this->admin_data(),
        'rows'  => $rows,
        'device_map' => $device_map,
    ];

    $this->load->view('admin/layout/header', $data);
    $this->load->view('admin/layout/sidebar', $data);
    $this->load->view('admin/users_index', $data);
    $this->load->view('admin/layout/footer', $data);
}

public function users_reset_device($id) {
    $this->guard_admin();
    $this->load->model('User_model');

    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $admin_id = (int)$this->session->userdata('admin_id');
    $ok = $this->User_model->reset_device_soft((int)$id, $admin_id);

    $this->session->set_flashdata('msg', $ok ? 'Device berhasil di-reset (soft reset).' : 'Tidak ada device aktif untuk user ini.');
    redirect('admin/users');
}

public function users_toggle($id) {
    $this->guard_admin();
    $this->load->model('User_model');

    // superadmin only (opsional)
    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $ok = $this->User_model->toggle_active((int)$id);
    $this->session->set_flashdata('msg', $ok ? 'Status user diubah.' : 'User tidak ditemukan.');
    redirect('admin/users');
}

public function users_create() {
    $this->guard_admin();
    $this->load->model('User_model');

    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $name  = (string)$this->input->post('name', TRUE);
    $email = (string)$this->input->post('email', TRUE);
    $is_active = (int)$this->input->post('is_active', TRUE);

    $res = $this->User_model->create_staff($name, $email, $is_active);

    $this->session->set_flashdata('msg', $res['msg']);
    redirect('admin/users');
}

public function users_update($id)
{
    $this->guard_admin();
    $this->load->model('User_model');

    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $name = trim((string)$this->input->post('name', TRUE));
    $email = strtolower(trim((string)$this->input->post('email', TRUE)));
    $is_active = (int)$this->input->post('is_active', TRUE);

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->session->set_flashdata('msg', 'Nama atau email tidak valid.');
        redirect('admin/users'); return;
    }

    $res = $this->User_model->update_staff((int)$id, $name, $email, $is_active);
$this->session->set_flashdata('toast', $res);
redirect('admin/users');

}

public function users_deactivate($id)
{
    $this->guard_admin();
    $this->load->model('User_model');

    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $res = $this->User_model->deactivate_staff((int)$id);
$this->session->set_flashdata('toast', $res);
redirect('admin/users');

}


public function settings() {
    $this->guard_admin();
    $this->load->model('Office_settings_model');

    $row = $this->Office_settings_model->get();

    $data = [
        'title' => 'Settings Kantor',
        'admin' => $this->admin_data(),
        'row'   => $row,
    ];

    $this->load->view('admin/layout/header', $data);
    $this->load->view('admin/layout/sidebar', $data);
    $this->load->view('admin/settings_index', $data);
    $this->load->view('admin/layout/footer', $data);
}

public function settings_save() {
    $this->guard_admin();
    $this->load->model('Office_settings_model');

    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $office_name = trim((string)$this->input->post('office_name', TRUE));
    $lat = (string)$this->input->post('lat', TRUE);
    $lng = (string)$this->input->post('lng', TRUE);
    $radius_m = (int)$this->input->post('radius_m', TRUE);
    $workday_start = (string)$this->input->post('workday_start', TRUE);
    $qr_refresh_seconds = (int)$this->input->post('qr_refresh_seconds', TRUE);

    if ($office_name === '') {
        $this->session->set_flashdata('msg', 'Nama kantor wajib diisi.');
        redirect('admin/settings'); return;
    }

    $lat_f = (float)$lat;
    $lng_f = (float)$lng;
    if ($lat_f < -90 || $lat_f > 90 || $lng_f < -180 || $lng_f > 180) {
        $this->session->set_flashdata('msg', 'Lat/Lng tidak valid.');
        redirect('admin/settings'); return;
    }

    if ($radius_m < 20 || $radius_m > 5000) {
        $this->session->set_flashdata('msg', 'Radius harus antara 20–5000 meter.');
        redirect('admin/settings'); return;
    }

    if (!preg_match('/^\d{2}:\d{2}$/', $workday_start)) {
        $this->session->set_flashdata('msg', 'Workday start harus format HH:MM.');
        redirect('admin/settings'); return;
    }
    $workday_start .= ':00';

    if ($qr_refresh_seconds < 5 || $qr_refresh_seconds > 120) {
        $this->session->set_flashdata('msg', 'QR refresh harus antara 5–120 detik.');
        redirect('admin/settings'); return;
    }
    $token_window_seconds = (int)$this->input->post('token_window_seconds', TRUE);
    if ($token_window_seconds < 30 || $token_window_seconds > 600) {
    $this->session->set_flashdata('msg', 'Token window harus antara 30–600 detik.');
    redirect('admin/settings'); return;
    }
    if ($token_window_seconds < $qr_refresh_seconds) {
    $this->session->set_flashdata('msg', 'Token window harus >= QR refresh.');
    redirect('admin/settings'); return;
}



   $ok = $this->Office_settings_model->update_settings([
    'office_name' => $office_name,
    'lat' => number_format($lat_f, 7, '.', ''),
    'lng' => number_format($lng_f, 7, '.', ''),
    'radius_m' => $radius_m,
    'workday_start' => $workday_start,
    'qr_refresh_seconds' => $qr_refresh_seconds,
    'token_window_seconds' => $token_window_seconds,
]);


    $this->session->set_flashdata('msg', $ok ? 'Settings berhasil disimpan.' : 'Gagal menyimpan settings.');
    redirect('admin/settings');
}

public function settings_regenerate_secret() {
    $this->guard_admin();
    $this->load->model('Office_settings_model');

    if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
        show_error('Forbidden', 403);
    }

    $secret = $this->Office_settings_model->regenerate_secret(32);

    $this->Office_settings_model->update_settings([
        'qr_secret' => $secret,
    ]);

    $this->session->set_flashdata('msg', 'QR Secret berhasil digenerate ulang.');
    redirect('admin/settings');
}

public function logs()
{
    $this->guard_admin();
    $this->load->model('Attendance_model', 'att');
    $this->load->model('User_model', 'user');

    $date_from = (string)$this->input->get('date_from', TRUE);
    $date_to   = (string)$this->input->get('date_to', TRUE);
    $user_id   = (int)$this->input->get('user_id', TRUE);
    $status    = (string)$this->input->get('status', TRUE);
    $q         = (string)$this->input->get('q', TRUE);

    // default date: hari kerja hari ini (biar langsung kepakai)
    if ($date_from === '' && $date_to === '') {
        $date_from = date('Y-m-d');
        $date_to   = date('Y-m-d');
    }

    $f = [
        'date_from' => $date_from,
        'date_to'   => $date_to,
        'user_id'   => $user_id,
        'status'    => $status,
        'q'         => $q,
        'limit'     => 300,
        'offset'    => 0,
    ];

    $rows  = $this->att->get_logs($f);
    $total = $this->att->count_logs($f);

    // summary sederhana
    $inCount = 0; $outCount = 0;
    foreach ($rows as $r) {
        if (($r['status'] ?? '') === 'IN') $inCount++;
        if (($r['status'] ?? '') === 'OUT') $outCount++;
    }

    $data = [
        'title' => 'Attendance Logs',
        'admin' => $this->admin_data(),
        'rows'  => $rows,
        'total' => $total,
        'summary' => ['in' => $inCount, 'out' => $outCount],
        'staff' => $this->user->list_staff_active(),
        'filter' => [
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'user_id'   => $user_id,
            'status'    => $status,
            'q'         => $q,
        ],
    ];

    $this->load->view('admin/layout/header', $data);
    $this->load->view('admin/layout/sidebar', $data);
    $this->load->view('admin/logs_index', $data);
    $this->load->view('admin/layout/footer', $data);
}

public function logs_export()
{
    $this->guard_admin();
    $this->load->model('Attendance_model', 'att');

    $date_from = (string)$this->input->get('date_from', TRUE);
    $date_to   = (string)$this->input->get('date_to', TRUE);
    $user_id   = (int)$this->input->get('user_id', TRUE);
    $status    = (string)$this->input->get('status', TRUE);
    $q         = (string)$this->input->get('q', TRUE);

    $f = [
        'date_from' => $date_from,
        'date_to'   => $date_to,
        'user_id'   => $user_id,
        'status'    => $status,
        'q'         => $q,
        'limit'     => 20000,
        'offset'    => 0,
    ];

    $rows = $this->att->get_logs($f);

    $fname = 'attendance_logs_' . ($date_from ?: 'all') . '_to_' . ($date_to ?: 'all') . '.csv';

    // output CSV
    while (ob_get_level() > 0) { @ob_end_clean(); }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.$fname.'"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');

    // header row
    fputcsv($out, [
        'workday_date','taken_at','name','email','status','distance_m','accuracy_m','lat','lng','source','note'
    ]);

    foreach ($rows as $r) {
        fputcsv($out, [
            $r['workday_date'] ?? '',
            $r['taken_at'] ?? '',
            $r['name'] ?? '',
            $r['email'] ?? '',
            $r['status'] ?? '',
            $r['distance_m'] ?? '',
            $r['accuracy_m'] ?? '',
            $r['lat'] ?? '',
            $r['lng'] ?? '',
            $r['source'] ?? '',
            $r['note'] ?? '',
        ]);
    }

    fclose($out);
    exit;
}

public function rekap()
{
    $this->guard_admin();
    $this->load->model('Attendance_model', 'att');

    $date_from = (string)$this->input->get('date_from', TRUE);
    $date_to   = (string)$this->input->get('date_to', TRUE);

    // default: 14 hari terakhir
    if ($date_from === '' || $date_to === '') {
        $date_to = date('Y-m-d');
        $date_from = date('Y-m-d', strtotime('-13 days'));
    }

    $rows = $this->att->rekap_harian($date_from, $date_to);

    $data = [
        'title' => 'Rekap Harian',
        'admin' => $this->admin_data(),
        'rows'  => $rows,
        'filter' => [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]
    ];

    $this->load->view('admin/layout/header', $data);
    $this->load->view('admin/layout/sidebar', $data);
    $this->load->view('admin/rekap_harian', $data);
    $this->load->view('admin/layout/footer', $data);
}

public function rekap_bulanan()
{
    $this->guard_admin();
    $this->load->model('Attendance_model', 'att');

    $month = (string)$this->input->get('month', TRUE);
    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        $month = date('Y-m');
    }

    $paket = $this->att->rekap_bulanan($month);

    $data = [
        'title' => 'Rekap Bulanan',
        'admin' => $this->admin_data(),
        'month' => $paket['month'],
        'date_from' => $paket['date_from'],
        'date_to' => $paket['date_to'],
        'rows' => $paket['rows'],
    ];

    $this->load->view('admin/layout/header', $data);
    $this->load->view('admin/layout/sidebar', $data);
    $this->load->view('admin/rekap_bulanan', $data);
    $this->load->view('admin/layout/footer', $data);
}



}
