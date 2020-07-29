<?php
class UsuarioManagerSesion {

    private static $user;

    private function __construct() {
    }

    public static function usuario() {
        if (!isset(self::$user)) {

            $CI = & get_instance();

            if (!$user_id = $CI->session->userdata('usuario_manager_id')) {
                return FALSE;
            }

            if (!$u = Doctrine::getTable('UsuarioManager')->find($user_id)) {
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
            redirect('/manager/autenticacion/login');
        }

    }

    public static function login($usuario, $password) {
        $CI = & get_instance();

        $autorizacion = self::validar_acceso($usuario, $password);

        if ($autorizacion) {
            $u = Doctrine::getTable('UsuarioManager')->findOneByUsuario($usuario);

            $CI->session->set_userdata('usuario_manager_id', $u->id);
            self::$user = $u;

            return TRUE;
        }

        return FALSE;
    }

    public static function login_saml($usuario, $cuenta_id = NULL) {
        $CI = & get_instance();
        $u = self::validar_acceso_saml($usuario);

        if ($u) {
          //Logueamos al usuario
          $CI->session->set_userdata('usuario_manager_id', $u->id);
          self::$user = $u;

          //si el usuario es del front end se lo intenta loguear tambien
          UsuarioSesion::login_saml($usuario,$cuenta_id);

          return true;
        }

        return false;
    }

    public static function validar_acceso_saml($usuario,$cuenta_id = NULL) {
        $usuario = Doctrine::getTable('UsuarioManager')->findOneByUsuario($usuario);

        if (!$usuario) {
          return false;
        }
        else {
          return $usuario;
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
              $CI->session->set_userdata('usuario_manager_id', $u->id);
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

    public static function validar_acceso_ldap($usuario) {
      $usuario = Doctrine_Query::create()
                    ->from('UsuarioManager um')
                    ->where('um.usuario = ?', $usuario)
                    ->execute();

        if (!$usuario[0]) {
          return false;
        }
        else {
          return $usuario[0];
        }
    }

    public static function validar_acceso($usuario, $password) {
        $u = Doctrine::getTable('UsuarioManager')->findOneByUsuario($usuario);
        if ($u) {
            // this mutates (encrypts) the input password
            $u_input = new UsuarioManager();
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
        $CI->session->unset_userdata('usuario_manager_id');
    }

    public static function logout_ldap() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_manager_id');
    }

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function registrado_saml() {
      if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
        return true;
      }
      else {
        return false;
      }
    }

    public function registrado_ldap() {
      if(isset($_COOKIE['simple_ldap_saml_session_ref_k'])) {
        return true;
      }
      else {
        return false;
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
      set_cookie('simple_bpm_query', base64_encode('manager'), 0, '/', HOST_SISTEMA_DOMINIO);
      set_cookie('simple_bpm_location', base64_encode($uri_array[0]), 0, '/', HOST_SISTEMA_DOMINIO);
    }
}
