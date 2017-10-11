<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller 
{

  public function __construct()
  {
    parent::__construct();

    // ensures csrf check
    require_post();
  }

  public function get_all($archived = 0)
  { 
    $tasks = $this->tasks->get_all($archived);
    echo json_encode($tasks);
  }

  public function get_one($id = null)
  {
    $task = $this->tasks->get_one($id);
    echo json_encode($task);
  }

  public function new()
  {
    $task = $this->tasks->new();
    echo json_encode($task);
  }

  public function save($id = null)
  {
    $task = $this->tasks->save($id, $this->input->post());
    echo json_encode($task);    
  }

  public function delete($id = null)
  {
    $status = $this->tasks->delete($id);
    echo json_encode($status);
  }

}
