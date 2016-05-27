<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Autenticacion extends CI_Controller {
    public function  __construct() {
        parent::__construct();
    }

    public function login() {
      UsuarioManagerSesion::registrar_acceso();

      if((!UsuarioManagerSesion::usuario()) && (LOGIN_ADMIN_COESYS)) {
        redirect(site_url('autenticacion/login_saml'));
      }
      else {
        $data['redirect']=$this->session->flashdata('redirect');
        $this->load->view('manager/autenticacion/login', $data);
      }
    }

    public function login_form() {
        $this->form_validation->set_rules('usuario', 'Usuario', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_check_password');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            UsuarioManagerSesion::login($this->input->post('usuario'),$this->input->post('password'));
            $respuesta->validacion=TRUE;
            $respuesta->redirect=$this->input->post('redirect')?$this->input->post('redirect'):site_url('manager');

        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    function logout() {
        UsuarioManagerSesion::logout();
        redirect($this->input->server('HTTP_REFERER'));
    }

    function check_password($password){
        $autorizacion=UsuarioManagerSesion::validar_acceso($this->input->post('usuario'),$this->input->post('password'));

        if($autorizacion)
            return TRUE;

        $this->form_validation->set_message('check_password','Usuario y/o contraseña incorrecta.');
        return FALSE;

    }
}
