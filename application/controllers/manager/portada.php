<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portada extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if(UsuarioSesion::registrado_saml()) {
          redirect(site_url());
        }
        else {
          setcookie('simple_bpm_query', base64_encode('manager'), 0, '/', HOST_SISTEMA_DOMINIO);
          UsuarioManagerSesion::force_login();
        }
    }

    public function index() {

        $data['title']='Portada';
        $data['content']='manager/portada/index';

        $this->load->view('manager/template',$data);
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
