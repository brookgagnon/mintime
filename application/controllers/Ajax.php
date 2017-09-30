<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller 
{

  public function __construct()
  {
    parent::__construct();
    date_default_timezone_set('America/Vancouver');
  }

	public function tasks($archived = 0)
	{
    $archived = ($archived==1 ? 1 : 0);

    $this->db->where('archived',$archived);
    $this->db->order_by('name');
    $query = $this->db->get('tasks');

    echo json_encode($query->result());
	}

  public function task($id = null)
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

  public function task_new()
  {
    $data = [
      'name' => '__ New Task __',
      'currency' => 'CAD'
    ];

    $this->db->insert('tasks',$data);

    echo json_encode($this->db->insert_id());
  }

  public function task_edit($id = null)
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

    $this->db->where('id',$id);
    $this->db->update('tasks', $data);

    echo json_encode(true);    
  }

  public function task_archive($id = null)
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

  public function task_delete($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $this->db->delete('tasks');

    $this->db->where('taskid',$id);
    $this->db->delete('logs');

    echo json_encode(true);
  }

  public function task_start($id = null)
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
      'taskid' => $id,
      'start' => time(),
      'end'=> null,
      'notes'=>'__ New Log Entry __'
    ];

    $this->db->insert('logs',$data);

    echo json_encode($this->db->insert_id());
  }

  // will stop all tasks; we don't allow running more than one task at a time.
  public function task_stop()
  {
    $this->db->where('end',null);
    $this->db->update('logs',['end'=>time()]);

    echo json_encode(true);
  }

  public function task_running()
  {
    $this->db->where('end',null);
    $query = $this->db->get('logs');
    $running = current($query->result());

    if($running) echo json_encode(['task'=>$running->taskid,'log'=>$running->id]);
    else echo json_encode(false);
  }


  public function log($id = null)
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
      
      $this->db->where('id',$log->taskid);
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

  public function log_edit($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $data = [
      'notes' => trim($this->input->post('notes')),
      'start' => round(trim($this->input->post('start'))),
    ];

    // only set end if it's specified; want to keep 'null' for running tasks.
    $end = trim($this->input->post('end'));
    if($end!='') $data['end'] = round($end);

    $this->db->where('id',$id);
    $this->db->update('logs', $data);

    echo json_encode(true);    
  }

  public function log_delete($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $this->db->where('id',$id);
    $this->db->delete('logs');

    echo json_encode(true);
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
