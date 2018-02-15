<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Autenticacion extends CI_Controller {
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

      UsuarioSesion::borrar_todos_los_datos_sesion();

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
        if (is_array($certificado_nivel)){
          $certificado_nivel = (string)$certificado_nivel[0];
        }
        $presencial_nivel = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='Presencial']/*[local-name() = 'AttributeValue']/text()");
        if (is_array($presencial_nivel)){
          $presencial_nivel = (string)$presencial_nivel[0];
        }
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

        //show_error($cuenta_id = $_COOKIE['simple_bpm_login_cuenta_id']);

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

              $primer_nombre = (string)$primer_nombre[0];
              $primer_apellido = (string)$primer_apellido[0];
              $segundo_apellido = (string)$segundo_apellido[0];

              $nivel_confianza = $this->nivel_confianza($saml_response,$xml);
            }

            $uid = $xml->xpath("//*[local-name() = 'AttributeStatement']/*[local-name() = 'Attribute'][@Name='uid']/*[local-name() = 'AttributeValue']/text()");
            $random_password = random_string('alnum', 32);
            $session_index = $xml->xpath("//*[local-name() = 'AuthnStatement']/@SessionIndex");
            $name_id = $xml->xpath("//*[local-name() = 'NameID']/@NameQualifier");

            //si ingresa con cedula retorna dni por lo tanto se transforma a ci
            $uid = str_replace('dni-', 'ci-', $uid[0]);

            if(isset($_COOKIE['simple_bpm_query'])) {
              $cuenta_id = $_COOKIE['simple_bpm_login_cuenta_id'];
              //$aaa = base64_decode($_COOKIE['simple_bpm_query']);
              switch(base64_decode($_COOKIE['simple_bpm_query'])) {
                case 'backend':
                  if (UsuarioBackendSesion::login_saml($uid, $cuenta_id)) {
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
                  if (!UsuarioSesion::login_saml($uid,$cuenta_id)) {
                    //usuario qu ese logue al cda pero no es del front end para esta cuenta
                    $usuario = new Usuario();
                    $usuario->usuario = $uid;
                    $usuario->setPasswordWithSalt($random_password);
                    $usuario->nombres = $primer_nombre;
                    $usuario->apellido_paterno = $primer_apellido;
                    $usuario->apellido_materno = $segundo_apellido;
                    $usuario->email = null;
                    //$usuario->nivel_confianza = $nivel_confianza;
                    $usuario->save();

                    UsuarioSesion::login_saml($uid,$cuenta_id);

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
              //sin cookies no se puede obtener la cuenta
              if (!UsuarioSesion::login_saml($uid,null)) {
                $usuario = new Usuario();
                $usuario->usuario = $uid;
                $usuario->setPasswordWithSalt($random_password);
                $usuario->nombres = $primer_nombre;
                $usuario->apellido_paterno = $primer_apellido;
                $usuario->apellido_materno = $segundo_apellido;
                $usuario->email = null;
                //$usuario->nivel_confianza = $nivel_confianza;
                $usuario->save();

                UsuarioSesion::login_saml($uid,null);

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

    public function login_form() {
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
      $cuenta = Cuenta::cuentaSegunDominio();
      UsuarioSesion::registrar_acceso($cuenta->id);
      //show_error($cuenta->id);
      if(strtoupper(TIPO_DE_AUTENTICACION) == 'CDA' || strtoupper(TIPO_DE_AUTENTICACION) == 'LDAP') {
      //si se invoca al login con un redirect se alamcena en la sesion para luego del login exitoso redirigir
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
  }

    public function olvido() {
      redirect('/');
      $data['title']='Olvide mi contraseña';
      $this->load->view('autenticacion/olvido',$data);
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

    public function continuar_como_usuario_login_con_empresas(){
      $this->session->unset_userdata('usuario_con_empresas_luego_login');
      redirect('etapas/inbox');
    }

    public function login_o_registrar_empresa(){
      if(UsuarioSesion::usuario()){
        $rut = $this->input->post('rutEmpresa');
        $razon_social = $this->input->post('razonSocial');
        $email = $this->input->post('correoElectronico');

        $cuenta = Cuenta::cuentaSegunDominio();

        UsuarioSesion::login_o_registrar_empresa($rut, $razon_social, $email,  (int)$cuenta->id);
        redirect('etapas/inbox');
      }
    }

    public function login_empresa(){
      if(UsuarioSesion::usuario()->registrado || UsuarioSesion::usuario_actuando_como_empresa()){
        $lista_empresas = UsuarioSesion::lista_empresas_usuario();
        if($lista_empresas){
          $data['empresas'] = $lista_empresas;
          $data['sidebar'] = 'empresas_usuario';
          $data['content'] = 'autenticacion/login_empresa';
          $data['title'] = 'Empresas de usuario';
          $this->load->view('template', $data);
        }
        else{
          $this->session->unset_userdata('empresas_usuario');
          $this->session->unset_userdata('usuario_con_empresas_luego_login');
          redirect('etapas/inbox');
        }
      }
      else{
        $this->session->unset_userdata('empresas_usuario');
        $this->session->unset_userdata('usuario_con_empresas_luego_login');
        redirect('etapas/inbox');
      }
    }

    public function regresar_usuario_real_grep(){
      $cuenta = Cuenta::cuentaSegunDominio();
      $usuario_real =  Doctrine_Query::create()
                                    ->from('Usuario u')
                                    ->where('u.id = ?', UsuarioSesion::usuario_actuando_como_empresa())
                                    ->andWhere('u.cuenta_id = ?', $cuenta->id)
                                    ->fetchOne();
      UsuarioSesion::regresar_usuario_real_grep($usuario_real->usuario, (int)$cuenta->id);
      redirect('etapas/inbox');
    }
}
