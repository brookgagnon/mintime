<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller 
{

  public function __construct()
  {
    parent::__construct();

    // ensures csrf check
    require_post();
  }

  public function get()
  {
    $return = [];

    $return['currencies'] = $this->currencies->list();
    $return['functional_currency'] = $this->currencies->get_functional();
    $return['available_currencies'] = $this->currencies->get_available();
    $return['exchange_fee'] = $this->currencies->get_fee();

    echo json_encode($return);
  }

  public function save()
  {
    $functional_currency = $this->input->post('functional_currency');
    $available_currencies = $this->input->post('available_currencies');
    $exchange_fee = $this->input->post('exchange_fee');

    $this->currencies->set_functional($functional_currency);
    $this->currencies->set_available($available_currencies);
    $this->currencies->set_fee($exchange_fee);

    // return/output settings as saved
    $this->get();
  }

}
