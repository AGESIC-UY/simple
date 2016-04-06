<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portada extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        UsuarioManagerSesion::force_login();

    }
    
    public function index() {

        $data['title']='Portada';
        $data['content']='manager/portada/index';
        
        $this->load->view('manager/template',$data);
    }
    
   
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */