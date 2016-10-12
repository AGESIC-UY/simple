<?php

class UsuarioSesion {

    private static $user;

    private function __construct() {
    }

    public static function usuario() {

        if (!isset(self::$user)) {

            $CI = & get_instance();

            if (!$user_id = $CI->session->userdata('usuario_id')) {
                return FALSE;
            }

            if (!$u = Doctrine::getTable('Usuario')->find($user_id)) {
                return FALSE;
            }

            self::$user = $u;
        }

        return self::$user;
    }

    public static function force_login() {
        $CI = & get_instance();

        $CI->load->library('LightOpenID');
        if ($CI->lightopenid->mode == 'id_res') {
            self::login_open_id();
        }

        if (!self::usuario()) {
            //Creo un usuario no registrado
            $usuario = new Usuario();
            $usuario->usuario = random_string('unique');
            $usuario->setPasswordWithSalt(random_string('alnum', 32));
            $usuario->registrado = 0;
            $usuario->save();

            $CI->session->set_userdata('usuario_id', $usuario->id);
            self::$user = $usuario;
        }
    }

    public static function login($usuario, $password) {
        $CI = & get_instance();

        $u = self::validar_acceso($usuario, $password);

        if ($u) {
            //Logueamos al usuario
            $CI->session->set_userdata('usuario_id', $u->id);
            self::$user = $u;

            return TRUE;
        }

        return FALSE;
    }

    public static function login_saml($usuario) {
        $CI = & get_instance();
        $u = self::validar_acceso_saml($usuario);

        if ($u) {
          //Logueamos al usuario
          $CI->session->set_userdata('usuario_id', $u->id);
          self::$user = $u;

          return TRUE;
        }

        return FALSE;
    }

    public static function validar_acceso($usuario_o_email, $password) {
        $users = Doctrine::getTable('Usuario')->findByUsuarioAndOpenId($usuario_o_email, 0);

        if ($users->count()==0) {
            $users = Doctrine::getTable('Usuario')->findByEmailAndOpenId($usuario_o_email, 0);
        }

        if ($users->count()==0) {
            return FALSE;
        }

        foreach ($users as $u) {    //Se debe chequear en varias cuentas, ya que en las cuentas del legado (antiguas) podian haber usuarios con el mismo correo.
            // this mutates (encrypts) the input password
            $u_input = new Usuario();
            $u_input->setPasswordWithSalt($password, $u->salt);

            // password match (comparing encrypted passwords)
            if ($u->password == $u_input->password) {
                unset($u_input);


                return $u;
            }

            unset($u_input);
        }

        return FALSE;
    }

    public static function validar_acceso_saml($usuario) {
        $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndOpenId($usuario, 0);

        if (count($usuario) == 0) {
          return false;
        }
        else {
          return $usuario[0];
        }
    }

    private static function login_open_id() {
        $CI = & get_instance();
        if ($CI->lightopenid->validate() && strpos($CI->lightopenid->claimed_id, 'https://www.claveunica.cl/') === 0) {
            $atributos = $CI->lightopenid->getAttributes();
            $usuario = Doctrine::getTable('Usuario')->findOneByUsuarioAndOpenId($CI->lightopenid->claimed_id, 1);
            if (!$usuario) {
                $usuario = new Usuario();
                $usuario->usuario = $CI->lightopenid->claimed_id;
                $usuario->registrado = 1;
                $usuario->open_id = 1;
            }
            $usuario->rut = $atributos['person/guid'];
            $usuario->save();

            $CI->session->set_userdata('usuario_id', $usuario->id);
            self::$user = $usuario;
        }
    }

    public static function logout() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_id');
    }

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function registrado_saml() {
      if(isset($_COOKIE['simple_bpm_saml_session_ref'])) {
        return true;
      }
      else {
        return false;
      }
    }

    public function limpiar_sesion() {
      // -- Limpia datos de autenticación
      if ((!UsuarioSesion::usuario()->registrado) && (isset($_COOKIE['simple_bpm_saml_session_ref']))) {
        $this->load->helper('cookies_helper');
        set_cookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
        set_cookie('simple_bpm_saml', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
      }
    }

    function registrar_acceso() {
      if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
          isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocolo = 'https://';
      }
      else {
        $protocolo = 'http://';
      }

      $uri = $protocolo.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      $uri_array = explode('/autenticacion', $uri);

      $this->load->helper('cookies_helper');
      set_cookie('simple_bpm_query', base64_encode('frontend'), 0, '/', HOST_SISTEMA_DOMINIO);
      set_cookie('simple_bpm_location', base64_encode($uri_array[0]), 0, '/', HOST_SISTEMA_DOMINIO);
    }
}
