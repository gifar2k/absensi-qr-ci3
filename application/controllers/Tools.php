<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends CI_Controller {

  public function hash($plain = 'admin123'){
    header('Content-Type: text/plain; charset=utf-8');
    echo password_hash($plain, PASSWORD_BCRYPT);
  }
}
