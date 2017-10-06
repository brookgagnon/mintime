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
    $this->db->where('end',null);
    $query = $this->db->get('logs');
    $running = current($query->result());

    if($running) echo json_encode(['task'=>$running->taskid,'log'=>$running->id]);
    else echo json_encode(false);
  }

  public function stats()
  {
    $stats = $this->input->post();
    if(!is_array($stats)) { echo json_encode(false); return; }

    $return = [];

    foreach($stats as $index=>$time)
    {
      $time = round($time);

      $this->db->where('end >',$time);
      $this->db->or_where('end',null);
      $query = $this->db->get('logs');

      $total = 0;

      foreach($query->result() as $row)
      {
        if(!$row->end) $end = time();
        else $end = $row->end;

        $start = max($row->start, $time);

        $total += $end - $start;
      }
      $total = number_format($total/3600,2);

      $return[$index] = $total;
    }

    echo json_encode($return);
  }
}
