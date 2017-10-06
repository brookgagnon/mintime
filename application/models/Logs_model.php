<?php

class Logs_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function get_for_task($task_id)
  {
    $this->db->where('task_id',$task_id);
    $this->db->order_by('start','DESC');
    $query = $this->db->get('logs');
    return $query->result();
  }

  public function delete_for_task($task_id)
  {
    $this->db->where('task_id',$task_id);
    $this->db->delete('logs');
    return true;
  }

  public function get_one($id)
  {
    $this->db->where('id',$id);
    $query = $this->db->get('logs');

    $log = current($query->result());
    if(!$log) return false;

    if($log->end) 
    {
      $log->time = number_format(($log->end - $log->start)/3600,2);
      
      $this->db->where('id',$log->task_id);
      $query = $this->db->get('tasks');
      $task = current($query->result());
      if(!$task) return false; // shouldn't happen.
      $log->amount = number_format($task->rate*$log->time,2);
    }
    else
    {
      $log->time = '...';
      $log->amount = '...';
    }

    return $log;
  }

  public function save($id, $data)
  {
    $log = [];

    if(isset($data['notes']) && trim($data['notes'])!='') $log['notes'] = trim($data['notes']);
    if(!empty($data['start'])) $log['start'] = round(trim($data['start']));
    if(!empty($data['end'])) $log['end'] = round(trim($data['end']));

    $this->db->where('id',$id);
    $this->db->update('logs', $log);

    return $this->get_one($id);
  }

  public function delete($id)
  {
    $this->db->where('id',$id);
    $this->db->delete('logs');
    return true;
  }

  public function start($task_id)
  {
    // make sure task exists
    $task = $this->tasks->get_one($task_id);
    if(!$task) return false;

    // stop existing entry
    $this->stop();

    // start new entry
    $data = [
      'task_id' => $task_id,
      'start' => time(),
      'end'=> null,
      'notes'=>'__ New Log Entry __'
    ];

    $this->db->insert('logs',$data);

    return $this->get_one( $this->db->insert_id() );
  }

  public function stop()
  {
    $this->db->where('end',null);
    $this->db->update('logs',['end'=>time()]);
    return true;
  }

}
