<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller 
{

  public function get_all($archived = 0)
  { 
    $tasks = $this->tasks->get_all($archived);
    echo json_encode($tasks);
  }

  public function get_one($id = null)
  {
    // get task
    $task = $this->tasks->get_one($id);

    // TODO maybe separate request for this
    $log = $this->logs->get_for_task($id);

    $return = ['data'=>$task, 'log'=>$log];

    echo json_encode($return);
  }

  public function new()
  {
    $task = $this->tasks->new();
  
    // TODO return full task so client doesn't have to make another request for this data?
    echo json_encode($task->id);
  }

  public function save($id = null)
  {
    $task = $this->tasks->save($id, $this->input->post());
    echo json_encode($task);    
  }

  public function archive_toggle($id = null)
  {
    echo json_encode( $this->tasks->archive_toggle($id) );
  }

  public function delete($id = null)
  {
    echo json_encode( $this->tasks->delete($id) );
  }

}
