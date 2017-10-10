<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs extends CI_Controller 
{

  public function get_for_task($task_id = null)
  {
    $log = $this->logs->get_for_task($task_id);
    echo json_encode($log);
  }

  public function get_one($id = null)
  {
    $entry = $this->logs->get_one($id);
    echo json_encode($entry);
  }

  public function save($id = null)
  {
    $entry = $this->logs->save($id, $this->input->post());
    echo json_encode($entry);    
  }

  public function delete($id = null)
  {
    $status = $this->logs->delete($id);
    echo json_encode($status);
  }

  public function start($task_id = null)
  {
    $entry = $this->logs->start($task_id);
    echo json_encode($entry);
  }

  public function stop()
  {
    $status = $this->logs->stop();
    echo json_encode($status);
  }

}
