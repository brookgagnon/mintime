<?php

class Logs_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  // slightly confusing name; get all entries for a task.
  public function get_all($task_id)
  {
    $this->db->where('task_id',$task_id);
    $this->db->order_by('start','DESC');
    $query = $this->db->get('logs');
    return $query->result();
  }

}
