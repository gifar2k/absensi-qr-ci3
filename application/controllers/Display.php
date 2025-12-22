<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Display extends CI_Controller {

  public function __construct(){
    parent::__construct();
    $this->config->load('qr', TRUE);
  }

  public function index(){
    $ttl = (int)$this->config->item('qr_ttl_seconds','qr');
    $exp = time() + $ttl;

    $payload = 'MONITOR|'.$exp;
    $key = $this->config->item('qr_hmac_key','qr');
    $sig = hash_hmac('sha256', $payload, $key);

    // link yang akan discan pegawai
    $url = site_url('absen/gate?exp='.$exp.'&sig='.$sig);

    $data = [
      'url' => $url,
      'exp' => $exp,
      'ttl' => $ttl
    ];
    $this->load->view('display/monitor', $data);
  }
}
