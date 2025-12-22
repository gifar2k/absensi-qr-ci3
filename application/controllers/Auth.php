<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

  public function __construct(){
    parent::__construct();
    $this->load->model('User_model','user');
  }

  public function login(){
    // kalau admin sudah login, lempar ke admin
    if($this->session->userdata('admin_logged')) redirect('admin/pegawai');

    // next untuk pegawai (abis scan QR monitor)
    $data['next'] = $this->input->get('next') ?: '';
    $data['token'] = $this->input->get('token') ?: '';

    $this->load->view('auth/login', $data);
  }

  public function do_login(){
    $email = trim($this->input->post('email'));
    $password = $this->input->post('password');
    $next = $this->input->post('next');
    $token = $this->input->post('token');

    if($email==='' || $password===''){
      $this->session->set_flashdata('err','Email & password wajib diisi.');
      redirect('auth/login');
    }

    $u = $this->user->get_by_email($email);
    if(!$u){
      $this->session->set_flashdata('err','Email tidak terdaftar / nonaktif.');
      redirect('auth/login');
    }

    if(!$u->password || !password_verify($password, $u->password)){
      $this->session->set_flashdata('err','Password salah.');
      redirect('auth/login');
    }

    // set session untuk semua role (pegawai & admin)
    $this->session->set_userdata([
      'logged'     => true,
      'user_id'    => (int)$u->id,
      'user_name'  => $u->nama,
      'user_email' => $u->email,
      'user_role'  => $u->role
    ]);

    // kalau admin, set flag admin juga
    if($u->role === 'admin'){
      $this->session->set_userdata([
        'admin_logged' => true,
        'admin_id'     => (int)$u->id,
        'admin_name'   => $u->nama
      ]);
    }

    // kalau ada next (pegawai), balik ke submit
    if($next && $token){
      redirect($next.'?token='.urlencode($token));
    }

    // default
    if($u->role === 'admin') redirect('admin/pegawai');
    redirect('display');
  }

  public function logout(){
    $this->session->sess_destroy();
    redirect('auth/login');
  }
}
