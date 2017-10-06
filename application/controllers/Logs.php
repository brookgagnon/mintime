<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs extends CI_Controller 
{

  public function get_one($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $query = $this->db->get('logs');

    $log = current($query->result());
    if(!$log) show_404();

    if($log->end) 
    {
      $log->time = number_format(($log->end - $log->start)/3600,2);
      
      $this->db->where('id',$log->task_id);
      $query = $this->db->get('tasks');
      $task = current($query->result());
      if(!$task) show_404(); // shouldn't happen.
      $log->amount = number_format($task->rate*$log->time,2);
    }
    else
    {
      $log->time = '...';
      $log->amount = '...';
    }

    echo json_encode($log);
  }

  public function save($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $data = [
      'notes' => trim($this->input->post('notes')),
      'start' => round(trim($this->input->post('start'))),
      'end' => round(trim($this->input->post('end')))
    ];

    // don't edit times if zero/empty
    if(empty($data['start'])) unset($data['start']);
    if(empty($data['end'])) unset($data['end']);

    // don't update notes if blank.
    if($data['notes']=='') unset($data['notes']);

    $this->db->where('id',$id);
    $this->db->update('logs', $data);

    $this->db->where('id',$id);
    $query = $this->db->get('logs');
    $log = current($query->result());

    // TODO code duplication bad, start using models.
    if($log->end) 
    {
      $log->time = number_format(($log->end - $log->start)/3600,2);
      
      $this->db->where('id',$log->task_id);
      $query = $this->db->get('tasks');
      $task = current($query->result());
      if(!$task) show_404(); // shouldn't happen.
      $log->amount = number_format($task->rate*$log->time,2);
    }
    else
    {
      $log->time = '...';
      $log->amount = '...';
    }

    echo json_encode($log);    
  }

  public function delete($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $this->db->delete('logs');

    echo json_encode(true);
  }

  public function start($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $query = $this->db->get('tasks');

    $task = current($query->result());
    if(!$task) show_404();

    $this->db->where('end',null);
    $this->db->update('logs',['end'=>time()]);

    $data = [
      'task_id' => $id,
      'start' => time(),
      'end'=> null,
      'notes'=>'__ New Log Entry __'
    ];

    $this->db->insert('logs',$data);

    echo json_encode($this->db->insert_id());
  }

  // will stop all tasks; we don't allow running more than one task at a time.
  public function stop()
  {
    $this->db->where('end',null);
    $this->db->update('logs',['end'=>time()]);

    echo json_encode(true);
  }

}
