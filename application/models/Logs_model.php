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
    return (bool) $this->db->affected_rows();
  }

  public function get_one($id)
  {
    $this->db->where('id',$id);
    $query = $this->db->get('logs');

    $entry = current($query->result());
    if(!$entry) return false;

    if($entry->end) 
    {
      $entry->time = number_format(($entry->end - $entry->start)/3600,2);
      
      $this->db->where('id',$entry->task_id);
      $query = $this->db->get('tasks');
      $task = current($query->result());
      if(!$task) return false; // shouldn't happen.
      $entry->amount = number_format($task->rate*$entry->time,2);
    }
    else
    {
      $entry->time = '...';
      $entry->amount = '...';
    }

    return $entry;
  }

  public function save($id, $data)
  {
    $entry = [];

    if(isset($data['notes']) && trim($data['notes'])!='') $entry['notes'] = trim($data['notes']);
    if(!empty($data['start'])) $entry['start'] = round(trim($data['start']));
    if(!empty($data['end'])) $entry['end'] = round(trim($data['end']));

    $this->db->where('id',$id);
    $this->db->update('logs', $entry);

    return $this->get_one($id);
  }

  public function delete($id)
  {
    $this->db->where('id',$id);
    $this->db->delete('logs');
    return (bool) $this->db->affected_rows();
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
    return (bool) $this->db->affected_rows();
  }

}
