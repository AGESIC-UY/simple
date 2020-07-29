<?php

class Agenda extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper('cookies_helper');
  }

  // -- Metodo para ir a la agenda externa
  public function ir_agenda_externa() {
    $url_vuelta = $this->session->userdata('url_agenda');
    if ($url_vuelta){
      //la variable usada para bloquear el camio de URL
      $this->session->set_userdata('ejecutar_form', 'true');
    }
    echo $url_vuelta;
  }
}
