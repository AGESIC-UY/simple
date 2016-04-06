<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cuentas extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

    }


    public function index(){
        $data['usuario']= UsuarioBackendSesion::usuario();
        
        $data['title'] = 'Configuración de Cuenta';
        $data['content'] = 'backend/cuentas/index';

        $this->load->view('backend/template', $data);
    }
    
    public function cuenta_form(){
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', 'Confirmar contraseña');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $usuario=UsuarioBackendSesion::usuario();
            $usuario->password=$this->input->post('password');
            $usuario->save();
            
            $this->session->set_flashdata('message','Cuenta actualizada con éxito.');

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/cuentas/index');
        }else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */