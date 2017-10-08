<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mintime extends CI_Controller
{
  public function index()
  {
    $this->load->view('mintime');
  }

  public function running()
  {
    $running_status = $this->mintime->running();
    echo json_encode($running_status);
  }

  public function stats()
  {
    $stats = $this->mintime->stats($this->input->post());
    echo json_encode($stats);
  }
}
