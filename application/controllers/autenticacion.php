<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Autenticacion extends MY_Controller {
    public function __construct() {
      parent::__construct();
      $this->load->helper('cookies_helper');
    }

    public function login_ldap() {

      if(strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
        if(!UsuarioSesion::usuario()->registrado) {
            if($this->input->get('redirect')) {
              set_cookie('redirect', $this->input->get('redirect'), 0, '/', HOST_SISTEMA_DOMINIO);
            }

            $data['title'] = 'Login';
            $data['redirect']=$this->session->flashdata('redirect');
            $this->load->view('autenticacion/login_ldap', $data);
          }
          else {
              redirect('tramites/disponibles');
          }
        }
        else {
          redirect('tramites/disponibles');
        }
    }

   public function logout_ldap() {
       UsuarioBackendSesion::logout_ldap();
       UsuarioManagerSesion::logout_ldap();
       UsuarioSesion::logout_ldap();
    }

    public function login_saml() {
      //version 1.1 - p1 en tramites gub uy estan usando login_saml como la URL
      //por lo tanto se tiene considerar la redireccion
      if($this->input->get('redirect')){
        $this->session->set_userdata('simple_bpm_login_redirect', $this->input->get('redirect'));
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

    //metodo que almacena el nivel de confianza en la sesion para luego ser utilizada como
    //una regla en las varibales globales modesl/regla.php
    private function nivel_confianza($saml_response,$xml){
      $nivel_confianza = '';
      //nivel de confianza
      if (strpos($saml_response,"urn:oasis:names:tc:SAML:2.0:ac:classes:SmartcardPKI") === FALSE){
        //no inhreso con cedula
        //buscamos certificado o presencial para definir nivel de confianza
        $certificado_nivel = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='Certificado']/*[local-name() = 'AttributeValue']/text()");
        $presencial_nivel = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='Presencial']/*[local-name() = 'AttributeValue']/text()");
        $certificado_nivel = filter_var($certificado_nivel, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
        $presencial_nivel = filter_var($presencial_nivel, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
        if (!$certificado_nivel && !$presencial_nivel){
          //autogestion
          $nivel_confianza = NIVEL_CONFIANZA_AG;
        }elseif ($certificado_nivel && !$presencial_nivel){
            //verificado ci
            $nivel_confianza = NIVEL_CONFIANZA_VCI;
        }elseif (!$certificado_nivel && $presencial_nivel){
            //verificado presencial
            $nivel_confianza = NIVEL_CONFIANZA_VP;
        }elseif ($certificado_nivel && $presencial_nivel){
            //verificado CI
            $nivel_confianza = NIVEL_CONFIANZA_VCI;
        }
      }else{
        $nivel_confianza = NIVEL_CONFIANZA_CI;
      }

      UsuarioSesion::nivel_confianza($nivel_confianza);
      return $nivel_confianza;
    }

    public function login_saml_respuesta() {
      try {
        $post = file_get_contents("php://input");
        $data = array();
        parse_str($post, $data);

        $out = '';

        if(isset($data['SAMLResponse'])) {
          $saml_response_raw = $data['SAMLResponse'];
          $saml_response = base64_decode($saml_response_raw);

          //show_error("java -jar ". JAR_VALIDACION ." ". CERTIFICADO_CDA_PUBLICO ." $saml_response_raw 2>&1");
          // -- Verifica la validez de la firma devuelta por CDA
          $out = exec("java -jar ". JAR_VALIDACION ." ". CERTIFICADO_CDA_PUBLICO ." $saml_response_raw 2>&1");
          //$out = 'OK';
        }

        if(!(strpos($out, 'OK')  !== FALSE)) {
          log_message('error', $out);
          redirect(site_url());
        }
        else {
          $xml = new SimpleXMLElement($saml_response);
          $usuario_pass = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='PrimerNombre']/*[local-name() = 'AttributeValue']");
          $cedula = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='NombreCompleto']/*[local-name() = 'AttributeValue']");

          if(!empty($cedula) || !empty($usuario_pass)) {
            if(!empty($cedula)) {
              $nombre_completo = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='NombreCompleto']/*[local-name() = 'AttributeValue']/text()");

              $primer_nombre = $nombre_completo[0][0];
              $primer_apellido = "";
              $segundo_apellido = "";

              $nivel_confianza = $this->nivel_confianza($saml_response,$xml);

            }
            else {
              $primer_nombre = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='PrimerNombre']/*[local-name() = 'AttributeValue']/text()");
              $primer_apellido = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='PrimerApellido']/*[local-name() = 'AttributeValue']/text()");
              $segundo_apellido = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='SegundoApellido']/*[local-name() = 'AttributeValue']/text()");

              $primer_nombre = $primer_nombre[0];
              $primer_apellido = $primer_apellido[0];
              $segundo_apellido = $segundo_apellido[0];

              $nivel_confianza = $this->nivel_confianza($saml_response,$xml);
            }

            $uid = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='uid']/*[local-name() = 'AttributeValue']/text()");
            $random_password = random_string('alnum', 32);
            $session_index = $xml->xpath("//*[local-name() = 'AuthnStatement']/@SessionIndex");
            $name_id = $xml->xpath("//*[local-name() = 'NameID']/@NameQualifier");

            //si ingresa con cedula retorna dni por lo tanto se transforma a ci
            $uid = str_replace('dni-', 'ci-', $uid[0]);

            if(isset($_COOKIE['simple_bpm_query'])) {
              switch(base64_decode($_COOKIE['simple_bpm_query'])) {
                case 'backend':
                  if (UsuarioBackendSesion::login_saml($uid)) {
                    if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
                      set_cookie('simple_bpm_saml_session_ref_k', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                    }


                    set_cookie('simple_bpm_saml_session_ref_k', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                    $to_url = $this->session->userdata('simple_bpm_login_redirect');
                    if (!empty($to_url)){
                       $this->session->set_userdata('simple_bpm_login_redirect','');
                       redirect($to_url);
                     }

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
                      set_cookie('simple_bpm_saml_session_ref_k', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                    }

                    set_cookie('simple_bpm_saml_session_ref_k', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                    $to_url = $this->session->userdata('simple_bpm_login_redirect');
                    if (!empty($to_url)){
                       $this->session->set_userdata('simple_bpm_login_redirect','');
                       redirect($to_url);
                     }

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
                    //$usuario->nivel_confianza = $nivel_confianza;
                    $usuario->save();

                    UsuarioSesion::login_saml($uid);

                    if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
                      set_cookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                    }

                    set_cookie('simple_bpm_saml_session_ref', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                    $to_url = $this->session->userdata('simple_bpm_login_redirect');
                    if (!empty($to_url)){
                       $this->session->set_userdata('simple_bpm_login_redirect','');
                       redirect($to_url);
                     }

                    if(isset($_COOKIE['simple_bpm_location'])) {
                      redirect(base64_decode($_COOKIE['simple_bpm_location']));
                    }
                    else {
                      redirect(site_url());
                    }
                  }
                  else {
                    if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
                      set_cookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                    }

                    set_cookie('simple_bpm_saml_session_ref', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                    $to_url = $this->session->userdata('simple_bpm_login_redirect');
                    if (!empty($to_url)){
                       $this->session->set_userdata('simple_bpm_login_redirect','');
                       redirect($to_url);
                     }


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
                //$usuario->nivel_confianza = $nivel_confianza;
                $usuario->save();

                UsuarioSesion::login_saml($uid);

                if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
                  set_cookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
                }

                $to_url = $this->session->userdata('simple_bpm_login_redirect');
                if (!empty($to_url)){
                   $this->session->set_userdata('simple_bpm_login_redirect','');
                   redirect($to_url);
                 }

                set_cookie('simple_bpm_saml_session_ref', base64_encode($session_index[0].'/'.$uid.'/'.$name_id[0]), 0, '/', HOST_SISTEMA_DOMINIO);

                if(isset($_COOKIE['simple_bpm_location'])) {
                  echo $_COOKIE['simple_bpm_location']; exit;
                  redirect(base64_decode($_COOKIE['simple_bpm_location']));
                }
                else {
                  redirect(site_url());
                }
              }
              else {

                $to_url = $this->session->userdata('simple_bpm_login_redirect');
                if (!empty($to_url)){
                   $this->session->set_userdata('simple_bpm_login_redirect','');
                   redirect($to_url);
                 }

                redirect(site_url());
              }
            }
          }
          else {
            redirect(site_url());
          }
        }
      }
      catch(Exception $error) {
        redirect(site_url());
      }
    }

    /*
    public function login_openid() {
        $this->load->library('LightOpenID');
        $redirect = $this->input->get('redirect') ? $this->input->get('redirect') : site_url();
        $this->lightopenid->returnUrl = $redirect;
        $this->lightopenid->required = array('person/guid');
        redirect($this->lightopenid->authUrl());
    }
    */

    public function login_form() {
      /*  $this->form_validation->set_rules('usuario', 'Usuario', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        */
        $respuesta = new stdClass();

        if(strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
          if(UsuarioSesion::login_ldap($this->input->post('usuario'), $this->input->post('password'))) {
            $respuesta->validacion = TRUE;
            $respuesta->redirect = $this->input->post('redirect') ? $this->input->post('redirect') : site_url();
          }
          else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=['Usuario y/o contraseña incorrecta.'];
          }
        }
        else {
          $this->form_validation->set_rules('usuario', 'Usuario', 'required');
          $this->form_validation->set_rules('password', 'Password', 'required|callback_check_password');
          if ($this->form_validation->run() == TRUE) {
            if(UsuarioSesion::login($this->input->post('usuario'), $this->input->post('password'))) {
              $respuesta->validacion = TRUE;
              $respuesta->redirect = $this->input->post('redirect') ? $this->input->post('redirect') : site_url();
            }
          }
          else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
          }
        }

        echo json_encode($respuesta);
    }

    public function login() {
    /*if(UsuarioSesion::registrado_saml()) {
      if(isset($_COOKIE['simple_bpm_location'])) {
        //si viene un redirect, entonces relogin por si estaba deslogueado y se pasa
        if($this->input->get('redirect')){
             redirect(site_url('autenticacion/login_saml?redirect='.$this->input->get('redirect')));
          }else{
             redirect(base64_decode($_COOKIE['simple_bpm_location']));
          }
      }
      else {
        redirect(site_url());
      }
    }*/

    if(strtoupper(TIPO_DE_AUTENTICACION) == 'CDA' || strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
      //si se invoca al login con un redirect se alamcena en la sesion para luego
      //del login exitoso redirigir
      if($this->input->get('redirect')){
        $this->session->set_userdata('simple_bpm_login_redirect', $this->input->get('redirect'));
      }
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

    /*
    if(strtoupper(TIPO_DE_AUTENTICACION) == 'CDA') {
      redirect(site_url('autenticacion/login_saml'));
    }
    elseif(strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
      redirect(site_url('autenticacion/login_ldap'));
    }
    else {
      if($this->input->get('redirect'))
          $data['redirect'] = $this->input->get('redirect');
      else
          $data['redirect'] = $this->session->flashdata('redirect');

      $data['title'] = 'Login';
      $this->load->view('autenticacion/login', $data);
    }
    */
  }

    public function olvido() {
      redirect('/');
      $data['title']='Olvide mi contraseña';
      $this->load->view('autenticacion/olvido',$data);
    }

    /*
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
    */

    /*
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
    */

    /*
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
    */

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
