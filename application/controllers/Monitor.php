<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Monitor extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('Office_settings_model', 'office');
        $this->load->model('Attendance_model', 'att');

        $this->load->helper(['workday','url']);
    }

    public function index()
    {
        $settings = $this->office->get();
        if (empty($settings)) show_error('Office settings not found', 500);

        $workday = workday_date(null, $settings['workday_start'] ?? '06:00:00');

        $hariMap = [
        'Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
        'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];

        $dayEn = date('l', strtotime($workday));
        $dayId = $hariMap[$dayEn] ?? $dayEn;

        $data = [
        'title' => 'Monitor Absensi - iLog Computer',
        'office' => $settings,
        'refresh_seconds' => (int)($settings['qr_refresh_seconds'] ?? 15),
        'poll_seconds' => 3,
        'workday' => $workday,
        'workday_day' => $dayId,
        'workday_label' => workday_label($settings['workday_start'] ?? '06:00:00'),
        ];

        $this->load->view('monitor/index', $data);
    }

    public function feed()
    {
        $settings = $this->office->get();
        if (empty($settings)) $this->_json(false, 'Office settings not found');

        $workday = workday_date(null, $settings['workday_start'] ?? '06:00:00');
        $rows = $this->att->get_monitor_rows($workday);

        // anti-cache (real-time)
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');

        $this->_json(true, 'OK', [
            'server_time' => date('H:i:s'),
            'workday_date' => $workday,
            'rows' => $rows
        ]);
    }

    private function _json(bool $ok, string $message, array $data = [])
    {
        $payload = ['ok' => $ok, 'message' => $message, 'data' => $data];
        while (ob_get_level() > 0) { @ob_end_clean(); }
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload));
        $this->output->_display();
        exit;
    }
}
