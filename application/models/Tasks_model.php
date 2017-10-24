<?php

class Tasks_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function get_all($archived)
  {
    $archived = empty($archived) ? 0 : 1;

    $this->db->where('archived',$archived);
    $this->db->order_by('name');
    $query = $this->db->get('tasks');

    return $query->result();
  }

  public function get_one($id)
  {
    // get task data
    $this->db->where('id',$id);
    $query = $this->db->get('tasks');
    $task = current($query->result());

    if(!$task) return false;

    // get log data to figure out time and billable amount
    $log = $this->logs->get_for_task($id);

    $task->time = 0;
    foreach($log as $entry)
    {
      $task->time += ($entry->end ? $entry->end : time()) - $entry->start;
    }
    $task->time = round($task->time/3600,2);
    $task->amount = round(min($task->budget,$task->time * $task->rate),2);

    $functional_currency = $this->currencies->get_functional();

    if($task->currency && $task->currency!=$functional_currency)
    {
      $task->functional_currency = $functional_currency;
      $task->functional_amount = round($this->currencies->exchange($task->amount, $task->currency), 2);
    }

    // convert tinyint to bool
    $task->archived = (bool) $task->archived;
    $task->invoiced = (bool) $task->invoiced;

    // make numbers nicer for display
    if(isset($task->functional_amount)) $task->functional_amount = number_format($task->functional_amount, 2);
    $task->amount = number_format($task->amount, 2);
    $task->time = number_format($task->time, 2);

    return ['data'=>$task, 'log'=>$log];
  }

  public function new()
  {
    $data = [
      'name' => '__ New Task __',
      'currency' => 'CAD'
    ];

    $this->db->insert('tasks',$data);

    return $this->get_one( $this->db->insert_id() );
  }

  public function save($id, $data)
  {
    $task = [];

    if(isset($data['name']) && trim($data['name'])!='') $task['name'] = trim($data['name']);
    if(isset($data['budget'])) $task['budget'] = (float) trim($data['budget']);
    if(isset($data['rate'])) $task['rate'] = (float) trim($data['rate']);
    if(isset($data['currency'])) $task['currency'] = trim($data['currency']); // TODO check against allowed values
    if(isset($data['invoiced'])) $task['invoiced'] = empty($data['invoiced']) ? 0 : 1;
    if(isset($data['archived'])) $task['archived'] = empty($data['archived']) ? 0 : 1;

    $this->db->where('id',$id);
    $this->db->update('tasks', $task);
    
    return $this->get_one($id);
  }

  public function delete($id)
  {
    $this->logs->delete_for_task($id);

    $this->db->where('id',$id);
    $this->db->delete('tasks');

    return (bool) $this->db->affected_rows();
  }

}
