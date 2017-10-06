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
    $id = (int) $id;
    if(!$id) show_404();

    // get task
    $task = $this->tasks->get_one($id);
    if(!$task) show_404();

    // TODO maybe separate request for this
    $log = $this->logs->get_all($id);

    $return = ['data'=>$task, 'log'=>$log];

    echo json_encode($return);
  }

  public function new()
  {
    $task = $this->tasks->new();
  
    // TODO, return full task so client doesn't have to request data from ID
    echo json_encode($task->id);
  }

  public function save($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    $task = $this->tasks->save($id, $this->input->post());

    echo json_encode($task);    
  }

  // TODO change name to archive_toggle
  public function archive($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    echo json_encode( $this->tasks->archive_toggle($id) );
  }

  public function delete($id = null)
  {
    $id = (int) $id;
    if(!$id) show_404();

    echo json_encode( $this->tasks->delete($id) );
  }

}
