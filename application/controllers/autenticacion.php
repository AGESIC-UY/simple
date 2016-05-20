<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Autenticacion extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function login_saml() {
      if($this->input->get('redirect')) {
        $int = 600;
        setcookie('redirect', $this->input->get('redirect'), time()+$int, '/', HOST_SISTEMA_DOMINIO);
      }

      $auth = new SimpleSAML_Auth_Simple(SIMPLE_SAML_AUTHSOURCE);
      $auth->requireAuth();
    }

    public function logout_saml() {
      UsuarioBackendSesion::logout();
      UsuarioManagerSesion::logout();
      UsuarioSesion::logout();

      $auth = new SimpleSAML_Auth_Simple(SIMPLE_SAML_AUTHSOURCE);
      $auth->logout();
    }

    // -- TODO Se debe verificar la firma del SAMLResponse para comprobar que el mensaje es válido.
    public function login_saml_respuesta() {
      try {
        // -- Verificamos que el origen de la respuesta es confiable. FIXME El origin llega como NULL.
        // if(isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], unserialize(ORIGENES_CONFIABLES))) {
        $post = file_get_contents("php://input");
        $data = array();
        parse_str($post, $data);
        $saml_response = base64_decode($data['SAMLResponse']);
        $xml = new SimpleXMLElement($saml_response);

        if($xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='PrimerNombre']/*[local-name() = 'AttributeValue']")) {
          $primer_nombre = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='PrimerNombre']/*[local-name() = 'AttributeValue']/text()");
          $primer_apellido = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='PrimerApellido']/*[local-name() = 'AttributeValue']/text()");
          $segundo_apellido = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='SegundoApellido']/*[local-name() = 'AttributeValue']/text()");
          $uid = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='uid']/*[local-name() = 'AttributeValue']/text()");
          $random_password = random_string('alnum', 32);
          $session_index = $xml->xpath("//*[local-name() = 'AuthnStatement']/@SessionIndex");
          $name_id = $xml->xpath("//*[local-name() = 'NameID']/@NameQualifier");

          $uid = $uid[0];
          $primer_nombre = $primer_nombre[0];
          $primer_apellido = $primer_apellido[0];
          $segundo_apellido = $segundo_apellido[0];

          if(isset($_COOKIE['simple_bpm_query'])) {
            switch(base64_decode($_COOKIE['simple_bpm_query'])) {
              case 'backend':
                if (UsuarioBackendSesion::login_saml($uid)) {
                  if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
                    setcookie('simple_bpm_saml_session_ref_k', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                  }

                  setcookie('simple_bpm_saml_session_ref_k', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                  if(isset($_COOKIE['simple_bpm_location'])) {
                    redirect(base64_decode($_COOKIE['simple_bpm_location']));
                  }
                  else {
                    redirect(site_url() . '/backend');
                  }
                }
                else {
                  if(isset($_COOKIE['simple_bpm_location'])) {
                    $url = base64_decode($_COOKIE['simple_bpm_location']);
                    $cuenta = explode('/backend', $url);
                    redirect($cuenta[0]);
                  }
                  else {
                    redirect(site_url());
                  }
                }
                break;
              case 'manager':
                if (UsuarioManagerSesion::login_saml($uid)) {
                  if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
                    setcookie('simple_bpm_saml_session_ref_k', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                  }

                  setcookie('simple_bpm_saml_session_ref_k', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                  if(isset($_COOKIE['simple_bpm_location'])) {
                    redirect(base64_decode($_COOKIE['simple_bpm_location']));
                  }
                  else {
                    redirect(site_url() . '/manager');
                  }
                }
                else {
                  if(isset($_COOKIE['simple_bpm_location'])) {
                    $url = base64_decode($_COOKIE['simple_bpm_location']);
                    $cuenta = explode('/manager', $url);
                    redirect($cuenta[0]);
                  }
                  else {
                    redirect(site_url());
                  }
                }
                break;
              default:
                if (!UsuarioSesion::login_saml($uid)) {
                  $usuario = new Usuario();
                  $usuario->usuario = $uid;
                  $usuario->setPasswordWithSalt($random_password);
                  $usuario->nombres = $primer_nombre;
                  $usuario->apellido_paterno = $primer_apellido;
                  $usuario->apellido_materno = $segundo_apellido;
                  $usuario->email = null;
                  $usuario->save();

                  UsuarioSesion::login_saml($uid);

                  if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
                    setcookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                  }

                  setcookie('simple_bpm_saml_session_ref', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                  if(isset($_COOKIE['simple_bpm_location'])) {
                    redirect(base64_decode($_COOKIE['simple_bpm_location']));
                  }
                  else {
                    redirect(site_url());
                  }
                }
                else {
                  if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
                    setcookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                  }

                  setcookie('simple_bpm_saml_session_ref', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                  if(isset($_COOKIE['simple_bpm_location'])) {
                    redirect(base64_decode($_COOKIE['simple_bpm_location']));
                  }
                  else {
                    redirect(site_url());
                  }
                }
            }
          }
          else {
            if (!UsuarioSesion::login_saml($uid)) {
              $usuario = new Usuario();
              $usuario->usuario = $uid;
              $usuario->setPasswordWithSalt($random_password);
              $usuario->nombres = $primer_nombre;
              $usuario->apellido_paterno = $primer_apellido;
              $usuario->apellido_materno = $segundo_apellido;
              $usuario->email = null;
              $usuario->save();

              UsuarioSesion::login_saml($uid);

              if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
                setcookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
              }

              setcookie('simple_bpm_saml_session_ref', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

              if(isset($_COOKIE['simple_bpm_location'])) {
                echo $_COOKIE['simple_bpm_location']; exit;
                redirect(base64_decode($_COOKIE['simple_bpm_location']));
              }
              else {
                redirect(site_url());
              }
            }
            else {
              redirect(site_url());
            }
          }
        }
        else {
          redirect(site_url());
        }
        // }
      }
      catch(Exception $error) {
        redirect(site_url());
      }
    }

    public function login_openid() {
        $this->load->library('LightOpenID');
        $redirect = $this->input->get('redirect') ? $this->input->get('redirect') : site_url();
        $this->lightopenid->returnUrl = $redirect;
        $this->lightopenid->required = array('person/guid');
        redirect($this->lightopenid->authUrl());
    }

    public function login_form() {
        $this->form_validation->set_rules('usuario', 'Usuario', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|callback_check_password');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            UsuarioSesion::login($this->input->post('usuario'), $this->input->post('password'));
            $respuesta->validacion = TRUE;
            $respuesta->redirect = $this->input->post('redirect') ? $this->input->post('redirect') : site_url();

            if(!$this->input->post('ajax')) {
              redirect($respuesta->redirect);
            }
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();

            if(!$this->input->post('ajax')) {
              $data['redirect'] = $this->input->post('redirect');
              $this->load->view('autenticacion/login', $data);
            }
        }

        if($this->input->post('ajax')) {
          echo json_encode($respuesta);
        }
    }

    public function login() {
      if(UsuarioSesion::registrado_saml()) {
        if(isset($_COOKIE['simple_bpm_location'])) {
          redirect(base64_decode($_COOKIE['simple_bpm_location']));
        }
        else {
          redirect(site_url());
        }
      }

      if(LOGIN_ADMIN_COESYS) {
        redirect(site_url('autenticacion/login_saml'));
      }
      else {
        if($this->input->get('redirect'))
            $data['redirect'] = $this->input->get('redirect');
        else
            $data['redirect'] = $this->session->flashdata('redirect');

        $data['title'] = 'Login';
        $this->load->view('autenticacion/login', $data);
      }
    }

    public function olvido() {
      redirect('/');
      $data['title']='Olvide mi contraseña';
      $this->load->view('autenticacion/olvido',$data);
    }

    public function olvido_form() {
      exit;
      $this->form_validation->set_rules('usuario', 'Usuario', 'required|callback_check_usuario_existe');

      $respuesta=new stdClass();
      if ($this->form_validation->run() == TRUE) {
          $random=random_string('alnum',16);

          $usuario = Doctrine::getTable('Usuario')->findOneByUsuarioAndOpenId($this->input->post('usuario'),0);
          if(!$usuario){
              $usuario = Doctrine::getTable('Usuario')->findOneByEmailAndOpenId($this->input->post('usuario'),0);
          }
          $usuario->reset_token=$random;
          $usuario->save();

          $cuenta=Cuenta::cuentaSegunDominio();
          if(is_a($cuenta, 'Cuenta'))
              $this->email->from($cuenta->nombre.'@'.$this->config->item('main_domain'), $cuenta->nombre_largo);
          else
              $this->email->from('simple@'.$this->config->item('main_domain'), 'Simple');
          $this->email->to($usuario->email);
          $this->email->subject('Reestablecer contraseña');
          $this->email->message('<p>Haga click en el siguiente link para reestablecer su contraseña:</p><p><a href="'.site_url('autenticacion/reestablecer?id='.$usuario->id.'&reset_token='.$random).'">'.site_url('autenticacion/reestablecer?id='.$usuario->id.'&reset_token='.$random).'</a></p>');
          $this->email->send();

          $this->session->set_flashdata('message','Se le ha enviado un correo con instrucciones de como reestablecer su contraseña.');

          $respuesta->validacion = TRUE;
          $respuesta->redirect = site_url('autenticacion/login');
      } else {
          $respuesta->validacion = FALSE;
          $respuesta->errores = validation_errors();
      }

      echo json_encode($respuesta);
    }

    public function reestablecer(){
      redirect('/');

      $id=$this->input->get('id');
      $reset_token=$this->input->get('reset_token');

      $usuario=Doctrine::getTable('Usuario')->find($id);

      if(!$usuario){
          echo 'Usuario no existe';
          exit;
      }
      if(!$reset_token){
          echo 'Faltan parametros';
          exit;
      }

      $usuario_input=new Usuario();
      $usuario_input->reset_token=$reset_token;

      if($usuario->reset_token!=$usuario_input->reset_token){
          echo 'Token incorrecto';
          exit;
      }

      $data['usuario']=$usuario;
      $data['title']='Reestablecer';
      $this->load->view('autenticacion/reestablecer',$data);
    }

    public function reestablecer_form(){
      exit;
      $id=$this->input->get('id');
      $reset_token=$this->input->get('reset_token');

      $usuario=Doctrine::getTable('Usuario')->find($id);

      if(!$usuario){
          echo 'Usuario no existe';
          exit;
      }
      if(!$reset_token){
          echo 'Faltan parametros';
          exit;
      }

      $usuario_input=new Usuario();
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
          $respuesta->redirect = site_url('autenticacion/login');
      } else {
          $respuesta->validacion = FALSE;
          $respuesta->errores = validation_errors();
      }

      echo json_encode($respuesta);
    }

    function logout() {
        UsuarioSesion::logout();
        redirect('');
    }

    function check_password($password) {
        $autorizacion = UsuarioSesion::validar_acceso($this->input->post('usuario'), $this->input->post('password'));

        if ($autorizacion)
            return TRUE;

        $this->form_validation->set_message('check_password', 'Usuario y/o contraseña incorrecta.');
        return FALSE;
    }

    function check_usuario($usuario) {
        $usuario = Doctrine::getTable('Usuario')->findOneByUsuario($usuario);

        if (!$usuario)
            return TRUE;

        $this->form_validation->set_message('check_usuario', 'Usuario ya existe.');
        return FALSE;
    }

    function check_email($email) {
        $usuario = Doctrine::getTable('Usuario')->findOneByEmailAndOpenId($email,0);

        if (!$usuario)
            return TRUE;

        $this->form_validation->set_message('check_email', 'Correo electrónico ya esta en uso por otro usuario.');
        return FALSE;
    }

    function check_usuario_existe($usuario_o_email) {
        $usuario = Doctrine::getTable('Usuario')->findOneByUsuarioAndOpenId($usuario_o_email,0);
        if(!$usuario){
            $usuario = Doctrine::getTable('Usuario')->findOneByEmailAndOpenId($usuario_o_email,0);
        }

        if ($usuario)
            return TRUE;

        $this->form_validation->set_message('check_usuario_existe', 'Usuario no existe.');
        return FALSE;
    }
}
