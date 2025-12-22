<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {

  public function already($user_id, $tanggal, $status){
    return $this->db
      ->where('user_id', (int)$user_id)
      ->where('tanggal', $tanggal)
      ->where('status', $status)
      ->count_all_results('absensi') > 0;
  }

  public function insert($data){
    return $this->db->insert('absensi', $data);
  }
}
