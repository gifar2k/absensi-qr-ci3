<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'security', 'form']);
        $this->load->database();
    }

    public function login() {
        // Kalau sudah login admin, lempar ke dashboard
        if ($this->session->userdata('admin_id')) {
            redirect('admin/dashboard');
            return;
        }

        $data = [
            'title' => 'Admin Login',
            'err'   => '',
        ];

        if ($this->input->method(TRUE) === 'POST') {
            $email = strtolower(trim((string)$this->input->post('email', TRUE)));
            $pass  = (string)$this->input->post('password', FALSE);

            if ($email === '' || $pass === '') {
                $data['err'] = 'Email dan password wajib diisi.';
            } else {
                // Ambil user admin
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
                    // Set session admin
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
}
