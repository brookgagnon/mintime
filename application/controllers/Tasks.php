<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller 
{

	public function get_all($archived = 0)
	{
    $archived = ($archived==1 ? 1 : 0);

    $this->db->where('archived',$archived);
    $this->db->order_by('name');
    $query = $this->db->get('tasks');

    echo json_encode($query->result());
	}

  public function get_one($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $query = $this->db->get('tasks');

    $task = current($query->result());
    if(!$task) show_404();

    $this->db->where('taskid',$id);
    $this->db->order_by('start','DESC');
    $query = $this->db->get('logs');
    $log = $query->result();

    $task->time = 0;
    foreach($log as $entry)
    {
      $task->time += ($entry->end ? $entry->end : time()) - $entry->start;
    }
    $task->time = number_format($task->time/3600,2);
    $task->amount = number_format(min($task->budget,$task->time * $task->rate),2);

    $task->archived = (bool) $task->archived;
    $task->invoiced = (bool) $task->invoiced;

    $return = [];
    $return['data'] = $task;
    $return['log'] = $log;

    echo json_encode($return);
  }

  public function new()
  {
    $data = [
      'name' => '__ New Task __',
      'currency' => 'CAD'
    ];

    $this->db->insert('tasks',$data);

    echo json_encode($this->db->insert_id());
  }

  public function save($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $data = [
      'name' => trim($this->input->post('name')),
      'budget' => (float) trim($this->input->post('budget')),
      'rate' => (float) trim($this->input->post('rate')),
      'currency' => $this->input->post('currency'),
      'invoiced'=> $this->input->post('invoiced')==1 ? 1 : 0
    ];

    // don't edit name if blank
    if($data['name']=='') unset($data['name']);

    $this->db->where('id',$id);
    $this->db->update('tasks', $data);

    $this->db->where('id',$id);
    $query = $this->db->get('tasks');

    echo json_encode(current($query->result()));    
  }

  public function archive($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $query = $this->db->get('tasks');
    $task = current($query->result());
    if(!$task) show_404();

    $this->db->where('id',$id);
    $this->db->update('tasks',['archived'=> ($task->archived == 0 ? 1 : 0)]);

    echo json_encode(true);
  }

  public function delete($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $this->db->delete('tasks');

    $this->db->where('taskid',$id);
    $this->db->delete('logs');

    echo json_encode(true);
  }

}
