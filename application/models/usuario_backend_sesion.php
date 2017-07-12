<?php
class UsuarioBackendSesion {

    private static $user;

    private function __construct() {
    }

    public static function usuario() {

        if (!isset(self::$user)) {

            $CI = & get_instance();

            if (!$user_id = $CI->session->userdata('usuario_backend_id')) {
                return FALSE;
            }

            if (!$u = Doctrine::getTable('UsuarioBackend')->find($user_id)) {
                return FALSE;
            }

            self::$user = $u;
        }

        return self::$user;
    }

    public static function force_login(){
        $CI = & get_instance();

        if(!self::usuario()){
            $CI->session->set_flashdata('redirect',current_url());
            redirect('/backend/autenticacion/login');
        }
    }

    public static function login($email, $password) {
        $CI = & get_instance();

        $autorizacion = self::validar_acceso($email, $password);

        if ($autorizacion) {
            $u = Doctrine::getTable('UsuarioBackend')->findOneByEmail($email);

            $CI->session->set_userdata('usuario_backend_id', $u->id);
            self::$user = $u;

            return TRUE;
        }

        return FALSE;
    }

    public static function validar_acceso($email, $password) {
        $u = Doctrine::getTable('UsuarioBackend')->findOneByEmail($email);
        if ($u) {

            // this mutates (encrypts) the input password
            $u_input = new UsuarioBackend();
            $u_input->setPasswordWithSalt($password,$u->salt);

            // password match (comparing encrypted passwords)
            if ($u->password == $u_input->password) {
                unset($u_input);

                return TRUE;
            }

            unset($u_input);
        }

        // login failed
        return FALSE;
    }

    public static function logout() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_backend_id');
    }

    public static function logout_ldap() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_backend_id');
    }

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public static function login_saml($usuario) {
        $CI = & get_instance();
        $u = self::validar_acceso_saml($usuario);

        if ($u) {
          //Logueamos al usuario
          $CI->session->set_userdata('usuario_backend_id', $u->id);
          self::$user = $u;
          //si el usuario es del front end se lo intenta loguear tambien
          UsuarioSesion::login_saml($usuario);

          return true;
        }

        return false;
    }

    public static function validar_acceso_saml($usuario) {
        $usuario = Doctrine::getTable('UsuarioBackend')->findOneByUsuario($usuario);

        if (!$usuario) {
          return false;
        }
        else {
          return $usuario;
        }
    }

    public function registrado_saml() {
      if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
        return true;
      }
      else {
        return false;
      }
    }

    public static function login_ldap($usuario, $password) {
      $ldapconfig['host'] = LDAP_HOST;
      $ldapconfig['puerto'] = LDAP_PUERTO;
      $ldapconfig['basedn'] = LDAP_BASE_DN;
      $ldapconfig['attr'] = LDAP_ATTR;
      $ldapconfig['user_con'] = LDAP_USER;
      $ldapconfig['pass_con'] = LDAP_PASS;
      $ldapconfig['version'] = LDAP_VERSION;

      $ds = ldap_connect($ldapconfig['host'], $ldapconfig['puerto']);
      ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $ldapconfig['version']);

      if ($bind=ldap_bind($ds, $ldapconfig['user_con'], $ldapconfig['pass_con'])) {
        $result = ldap_search($ds, $ldapconfig['basedn'], $ldapconfig['attr'] . "=" . $usuario);
        $info = ldap_get_entries($ds, $result);
        $userdn = $info[0]["dn"];
        $count = $info["count"];

        if($count == 1) {
          if ($bind = ldap_bind($ds, $userdn, $password)) {
            $CI = & get_instance();
            $u = self::validar_acceso_ldap($usuario);

            if ($u) {
              // Logueamos al usuario
              $CI->session->set_userdata('usuario_backend_id', $u->id);
              self::$user = $u;
              return true;
            }
            else {
                return false;
            }
          }
          else {
            return false;
          }
        }
        else {
          return false;
        }
      }
    }

    public function registrado_ldap() {
      if(self::usuario()){
        return true;
      }
      else {
        return false;
      }
      /*if(isset($_COOKIE['simple_bpm_ldap_session_ref_k'])) {
        return true;
      }
      else {
        return false;
      }*/
    }

    public static function validar_acceso_ldap($usuario) {
        $usuario = Doctrine_Query::create()
                      ->from('UsuarioBackend ub')
                      ->where('ub.usuario = ?', $usuario)
                      ->execute();

        if (!$usuario[0]) {
          return false;
        }
        else {
          return $usuario[0];
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
      set_cookie('simple_bpm_query', base64_encode('backend'), 0, '/', HOST_SISTEMA_DOMINIO);
      set_cookie('simple_bpm_location', base64_encode($uri_array[0]), 0, '/', HOST_SISTEMA_DOMINIO);
    }

}
