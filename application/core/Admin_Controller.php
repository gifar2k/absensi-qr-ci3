<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller {

    protected $admin;

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper(['url', 'security', 'form']);

        // Wajib login admin
        if (!$this->session->userdata('admin_id')) {
            redirect('admin/login');
            exit;
        }

        // optional: simpan info admin buat view
        $this->admin = [
            'id'    => (int)$this->session->userdata('admin_id'),
            'name'  => (string)$this->session->userdata('admin_name'),
            'email' => (string)$this->session->userdata('admin_email'),
            'role'  => (string)$this->session->userdata('admin_role'),
        ];
    }

    protected function require_superadmin(): void {
        if (($this->session->userdata('admin_role') ?? '') !== 'superadmin') {
            show_error('Forbidden', 403);
        }
    }
}
