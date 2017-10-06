<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs extends CI_Controller 
{
  public function get_one($id = null)
  {
    $log = $this->logs->get_one($id);
    echo json_encode($log);
  }

  public function save($id = null)
  {
    $log = $this->logs->save($id, $this->input->post());
    echo json_encode($log);    
  }

  public function delete($id = null)
  {
    echo json_encode( $this->logs->delete($id) );
  }

  public function start($task_id = null)
  {
    $log = $this->logs->start($task_id);

    // TODO return full task so client doesn't have to make another request for this data?
    echo json_encode($log->id);
  }

  public function stop()
  {
    echo json_encode( $this->logs->stop() );
  }
}
