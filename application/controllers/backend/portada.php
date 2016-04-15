<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portada extends MY_BackendController {

    public function __construct() {
        parent::__construct();
        UsuarioBackendSesion::force_login();
    }

    public function index() {
        $usuario=UsuarioBackendSesion::usuario();

        if($usuario->rol=='super' || $usuario->rol=='gestion')
            redirect('backend/gestion');
        else if ($usuario->rol=='modelamiento')
            redirect('backend/procesos');
        else if($usuario->rol=='operacion' || $usuario->rol=='seguimiento')
            redirect('backend/seguimiento');
        else if($usuario->rol=='configuracion')
            redirect('backend/configuracion');
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
