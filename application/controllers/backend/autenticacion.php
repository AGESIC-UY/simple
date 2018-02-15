<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Autenticacion extends MY_BackendController {
    public function  __construct() {
        parent::__construct();
    }

    public function login() {
      $cuenta = Cuenta::cuentaSegunDominio();
      UsuarioBackendSesion::registrar_acceso($cuenta->id);

      if((!UsuarioBackendSesion::usuario()) && (strtoupper(TIPO_DE_AUTENTICACION) == 'CDA')) {
        redirect(site_url('autenticacion/login_saml'));
      }
      elseif((!UsuarioBackendSesion::usuario()) && (strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP')) {
        $data['redirect']=$this->session->flashdata('redirect');
        $this->load->view('backend/autenticacion/login_ldap', $data);
      }
      else {
        $data['redirect']=$this->session->flashdata('redirect');
        $this->load->view('backend/autenticacion/login', $data);
      }
    }

    public function login_form() {
      /*if(strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
        $this->form_validation->set_rules('usuario', 'Usuario', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_check_password');
      }*/
      /*else {
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_check_password');
      }*/

      $respuesta=new stdClass();

      if(strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
        $cuenta = Cuenta::cuentaSegunDominio();
        if(UsuarioBackendSesion::login_ldap($this->input->post('usuario'),$this->input->post('password'), $cuenta->id)) {
          $respuesta->validacion=TRUE;
          $respuesta->redirect=$this->input->post('redirect')?$this->input->post('redirect'):site_url('backend');
        }
        else {
          $respuesta->validacion=FALSE;
          $respuesta->errores=['Usuario y/o contraseña incorrecta.'];
        }
      }
      else {
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_check_password');
        if ($this->form_validation->run() == TRUE) {
          $cuenta = Cuenta::cuentaSegunDominio();
          if(UsuarioBackendSesion::login($this->input->post('email'),$this->input->post('password'), $cuenta->id)) {
            $respuesta->validacion=TRUE;
            $respuesta->redirect=$this->input->post('redirect')?$this->input->post('redirect'):site_url('backend');
          }
        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }
      }

      echo json_encode($respuesta);
    }

    public function olvido() {
        $data['title']='Olvide mi contraseña';
        $this->load->view('backend/autenticacion/olvido',$data);
    }

    public function olvido_form() {
        $this->form_validation->set_rules('email', 'E-Mail', 'required|callback_check_usuario_existe');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $random=random_string('alnum',16);

            $usuario = Doctrine::getTable('UsuarioBackend')->findOneByEmail($this->input->post('email'));
            $usuario->reset_token=$random;
            $usuario->save();

            $cuenta=Cuenta::cuentaSegunDominio();
            if(is_a($cuenta, 'Cuenta'))
                $this->email->from($cuenta->nombre.'@agesic.gub.uy', $cuenta->nombre_largo);
            else
                $this->email->from('simple@agesic.gub.uy', 'Simple');
            $this->email->to($usuario->email);
            $this->email->subject('Reestablecer contraseña');
            $this->email->message('<p>Haga click en el siguiente link para reestablecer su contraseña:</p><p><a href="'.site_url('backend/autenticacion/reestablecer?id='.$usuario->id.'&reset_token='.$random).'">'.site_url('autenticacion/reestablecer?id='.$usuario->id.'&reset_token='.$random).'</a></p>');
            $this->email->send();

            $this->session->set_flashdata('message','Se le ha enviado un correo con instrucciones de como reestablecer su contraseña.');

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/autenticacion/login');
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function reestablecer(){
        $id=$this->input->get('id');
        $reset_token=$this->input->get('reset_token');

        $usuario=Doctrine::getTable('UsuarioBackend')->find($id);

        if(!$usuario){
            echo 'Usuario no existe';
            exit;
        }
        if(!$reset_token){
            echo 'Faltan parametros';
            exit;
        }

        $usuario_input=new UsuarioBackend();
        $usuario_input->reset_token=$reset_token;

        if($usuario->reset_token!=$usuario_input->reset_token){
            echo 'Token incorrecto';
            exit;
        }

        $data['usuario']=$usuario;
        $data['title']='Reestablecer';
        $this->load->view('backend/autenticacion/reestablecer',$data);
    }

    public function reestablecer_form(){
        $id=$this->input->get('id');
        $reset_token=$this->input->get('reset_token');

        $usuario=Doctrine::getTable('UsuarioBackend')->find($id);

        if(!$usuario){
            echo 'Usuario no existe';
            exit;
        }
        if(!$reset_token){
            echo 'Faltan parametros';
            exit;
        }

        $usuario_input=new UsuarioBackend();
        $usuario_input->reset_token=$reset_token;

        if($usuario->reset_token!=$usuario_input->reset_token){
            echo 'Token incorrecto';
            exit;
        }

        $this->form_validation->set_rules('password','Contraseña','required|min_length[6]');
        $this->form_validation->set_rules('password_confirm','Confirmar contraseña','required|matches[password]');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $usuario->password=$this->input->post('password');
            $usuario->reset_token=null;
            $usuario->save();

            $this->session->set_flashdata('message','Su contraseña se ha reestablecido.');

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/autenticacion/login');
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }


    function logout() {
        UsuarioBackendSesion::logout();
        redirect($this->input->server('HTTP_REFERER'));
    }

    public function logout_ldap() {
        UsuarioBackendSesion::logout_ldap();
        redirect($this->input->server('HTTP_REFERER'));
     }

    function check_password($password) {
        $cuenta = Cuenta::cuentaSegunDominio();
        $autorizacion=UsuarioBackendSesion::validar_acceso($this->input->post('email'),$this->input->post('password'), $cuenta->id);

        if($autorizacion)
            return TRUE;

        $this->form_validation->set_message('check_password','E-Mail y/o contraseña incorrecta.');
        return FALSE;
    }

    function check_usuario_existe($usuario) {
        $usuario = Doctrine::getTable('UsuarioBackend')->findOneByEmail($usuario);

        if ($usuario){
            $cuenta = Cuenta::cuentaSegunDominio();

            if($usuario->cuenta->id == $cuenta->id)
                return TRUE;
        }

        $this->form_validation->set_message('check_usuario_existe', 'Usuario no existe.');
        return FALSE;
    }
}
