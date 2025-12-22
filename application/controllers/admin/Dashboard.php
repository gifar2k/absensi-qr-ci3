<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

    public function index() {
        $data = [
            'title' => 'Admin Dashboard',
            'admin' => $this->admin,
        ];

        // nanti isi widget: total user, hari ini masuk/pulang, device locked, dsb
        $this->load->view('admin/layout/header', $data);
        $this->load->view('admin/layout/sidebar', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('admin/layout/footer', $data);
    }
}
