<?php

class Mintime_model extends CI_Model
{

  public function running()
  {
    $this->db->where('end',null);
    $query = $this->db->get('logs');
    $running = current($query->result());

    if($running) return ['task_id'=>$running->task_id,'log_id'=>$running->id];
    else return false;
  }

  public function stats($requested_stats)
  {
    if(!is_array($requested_stats)) return false;

    $stats = [];

    foreach($requested_stats as $index=>$time)
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

      $stats[$index] = $total;
    }

    return $stats;
  }

}
