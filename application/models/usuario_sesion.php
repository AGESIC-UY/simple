<?php

class UsuarioSesion {

    private static $user;

    private function __construct() {
    }

    public static function usuario() {

        if (!isset(self::$user)) {

            $CI = & get_instance();

            //show_error('usu:'.$CI->session->userdata('usuario_id'));
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
        $u = self::usuario();
        if (!$u) {
            //Creo un usuario no registrado
            $usuario = new Usuario();
            $usuario->usuario = random_string('unique');
            $usuario->setPasswordWithSalt(random_string('alnum', 32));
            $usuario->registrado = 0;
            $usuario->save();
            $CI->session->set_userdata('usuario_id', $usuario->id);
            self::$user = $usuario;
        }else if ($u->cuenta_id != null){
          //usuario con cuenta asociada
          //se verifica si cambia de cuenta se hace un logout
          $cuenta = Cuenta::cuentaSegunDominio();
          if ($u->cuenta_id != $cuenta->id){
            UsuarioSesion::logout();
            redirect('autenticacion/login');
          }
        }else{
          //el usuario es un ciudadano o es una empresa pero si existe un usuario
          //para la cuenta que se está se tiene que mudar de usuario
          $cuenta = Cuenta::cuentaSegunDominio();
          $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndCuentaId($u->usuario, $cuenta->id);
          if (count($usuario) >0){
            //mudar de usuario se esta pasando de un ciudadano a un usuario de la cuenta
            UsuarioSesion::logout();
            redirect('autenticacion/login');
          }else{
            /*$usuarioComoEmpresa = UsuarioSesion::usuario_actuando_como_empresa();
            if ($usuarioComoEmpresa){
              //si el usuario esta actuando como empresa, se verifica la cuenta de este usuario
              $usuarioComoEmpresa = Doctrine::getTable('Usuario')->findById($usuarioComoEmpresa);
              if ($usuarioComoEmpresa->cuenta_id && $usuarioComoEmpresa->cuenta_id != $cuenta->id){
                UsuarioSesion::logout();
                redirect('tramites/disponibles');
              }
            }*/
          }

        }
    }

    public static function login($usuario, $password) {
        $CI = & get_instance();
        $u = self::validar_acceso($usuario, $password);

        if ($u) {
            //Logueamos al usuario
            $CI->session->set_userdata('usuario_id', $u->id);
            self::$user = $u;

            $esUsuarioMesaDeEntrada = false;

            foreach ($u->GruposUsuarios as $grupo) {
              if($grupo->nombre == 'UsuarioMesaDeEntrada'){
                $esUsuarioMesaDeEntrada = true;
                break;
              }
            }

            $CI->session->set_userdata('usuarioMesaDeEntrada', $esUsuarioMesaDeEntrada);

            return TRUE;
        }

        return FALSE;
    }

    public static function login_saml($usuario, $cuenta_id) {
        $CI = & get_instance();
        $u = self::validar_acceso_saml($usuario,$cuenta_id);

        if($u) {

          $empresas = self::ws_empresas_vinculadas_usuario_grep($usuario);
          if($empresas && count($empresas) > 0) {
            $CI->session->set_userdata('empresas_usuario', $empresas);
            //variable disponible solo para el hasta que eligan como inician sesion despues del login
            $CI->session->set_userdata('usuario_con_empresas_luego_login', true);
          }
            //Logueamos al usuario
            $CI->session->set_userdata('usuario_id', $u->id);
            self::$user = $u;

            $esUsuarioMesaDeEntrada = false;

            foreach ($u->GruposUsuarios as $grupo) {
              if($grupo->nombre == 'UsuarioMesaDeEntrada'){
                $esUsuarioMesaDeEntrada = true;
                break;
              }
            }

            $CI->session->set_userdata('usuarioMesaDeEntrada', $esUsuarioMesaDeEntrada);

            return TRUE;
        }

        return FALSE;
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
              $CI->session->set_userdata('usuario_id', $u->id);
              self::$user = $u;

              $esUsuarioMesaDeEntrada = false;

              foreach ($u->GruposUsuarios as $grupo) {
                if($grupo->nombre == 'UsuarioMesaDeEntrada'){
                  $esUsuarioMesaDeEntrada = true;
                  break;
                }
              }

              $CI->session->set_userdata('usuarioMesaDeEntrada', $esUsuarioMesaDeEntrada);

              return true;
            }
            else {
              try {
                $random_password = random_string('alnum', 32);

                $nuevo_usuario = new Usuario();
                $nuevo_usuario->usuario = $usuario;
                $nuevo_usuario->setPasswordWithSalt($random_password);
                $nuevo_usuario->nombres = '';
                $nuevo_usuario->apellido_paterno = '';
                $nuevo_usuario->apellido_materno = '';
                $nuevo_usuario->email = null;
                $nuevo_usuario->save();
              }
              catch(Exception $e) {
                return false;
              }

              //Logueamos al usuario
              $CI->session->set_userdata('usuario_id', $nuevo_usuario->id);
              self::$user = $nuevo_usuario;

              return true;
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

    public static function login_usuario_pago($usuario) {
      $CI = & get_instance();
      $u = self::validar_acceso_usuario_pago($usuario);

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

    public static function validar_acceso_usuario_pago($usuario) {
        $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndOpenId($usuario, 0);

        if (count($usuario) == 0) {
          return false;
        }
        else {
          return $usuario[0];
        }
    }

    public static function getNivel_confianza($nivel_confianza = NULL){
      $CI = & get_instance();
      return $CI->session->userdata('usuario_nivel_confianza');
    }

    //almacena el nivel de confianza en la session
    public static function nivel_confianza($nivel_confianza){
      $CI = & get_instance();

      $CI->session->set_userdata('usuario_nivel_confianza', $nivel_confianza);
    }

    public static function validar_acceso_saml($usuario_code, $cuenta_id) {

        $toReturn = Doctrine::getTable('Usuario')->findUsuarioEnCuentaOrCiudadano($usuario_code,$cuenta_id);
        return $toReturn;

        /*if ($cuenta_id){
          $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndCuentaId($usuario_code, $cuenta_id);
          if (count($usuario) == 0){
            //puede ser un ciudadano, se busca como ciudadano, con cuenta NULL
            $usuario = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.usuario = ? and u.cuenta_id IS NULL', $usuario_code)
              ->execute();
            //$usuario = Doctrine::getTable('Usuario')->findByUsuarioAndCuentaId($usuario_code, NULL);
          }
        }else{
          $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndOpenId($usuario_code, 0);
        }


        if (count($usuario) == 0) {
          return false;
        }else {
          return $usuario[0];
        }*/
    }

    public static function validar_acceso_ldap($usuario) {
        $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndOpenId($usuario, 0);

        if (count($usuario) == 0) {
          return false;
        }
        else {
          return $usuario[0];
        }
    }


    private static function login_open_id() {
      return false;
        /*$CI = & get_instance();
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
        }*/
    }

    public static function logout() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_id');
        $CI->session->unset_userdata('usuario_nivel_confianza');

        UsuarioSesion::borrar_todos_los_datos_sesion();
    }

    public static function logout_ldap() {
        $CI = & get_instance();
        self::$user = NULL;
        $CI->session->unset_userdata('usuario_id');
        $CI->session->unset_userdata('usuario_nivel_confianza');

        UsuarioSesion::borrar_todos_los_datos_sesion();
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

    public function registrado_ldap() {
      if(self::usuario()){
        return true;
      }
      else {
        return false;
      }
      /*f(isset($_COOKIE['simple_bpm_ldap_session_ref'])) {
        return true;
      }
      else {
        return false;
      }*/
    }

    public function limpiar_sesion() {
      // -- Limpia datos de autenticación
      if ((!UsuarioSesion::usuario()->registrado) && (isset($_COOKIE['simple_bpm_saml_session_ref']))) {
        $this->load->helper('cookies_helper');
        set_cookie('simple_bpm_saml_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
        set_cookie('simple_bpm_saml', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
      }

      if ((!UsuarioSesion::usuario()->registrado) && (isset($_COOKIE['simple_bpm_ldap_session_ref']))) {
        $this->load->helper('cookies_helper');
        set_cookie('simple_bpm_ldap_session_ref', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
        set_cookie('simple_bpm_ldap', null, time()-1, '/', HOST_SISTEMA_DOMINIO);
      }
    }

    function registrar_acceso($cuenta_id = false) {
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
      set_cookie('simple_bpm_login_cuenta_id', $cuenta_id, 0, '/', HOST_SISTEMA_DOMINIO);
    }

    public static function usuarioMesaDeEntrada() {
        $CI = & get_instance();
        if (!$CI->session->userdata('usuarioMesaDeEntrada')) {
            return false;
        }
        else {
          return true;
        }
    }

    public static function ws_empresas_vinculadas_usuario_grep($documento_usuario) {
      //web service GREP empresas por documento
      try {
        $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://agesic.gub.uy/gestionRepresentante/v1">
                             <soapenv:Header/>
                             <soapenv:Body>
                                <v1:obtEmpresasUsuario>
                                   <paramObtEmpresasUsuario>
                                      <usuario>'.$documento_usuario.'</usuario>
                                   </paramObtEmpresasUsuario>
                                </v1:obtEmpresasUsuario>
                             </soapenv:Body>
                          </soapenv:Envelope>';

        $soap_header = array(
         "Content-type: text/xml;charset=\"utf-8\"",
         "Accept: text/xml",
         "Cache-Control: no-cache",
         "Pragma: no-cache",
         "Content-length: ". strlen($soap_body)
        );

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, WS_EMPRESA_USUARIO_GREP);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);
        $soap_response = curl_exec($soap_do);
        $curl_errno = curl_errno($soap_do);
        $curl_error = curl_error($soap_do);
        curl_close($soap_do);

        if ($curl_errno > 0) {
          log_message('Error', $curl_error);
          return false;
        }

        $xml = new SimpleXMLElement($soap_response);
        $datos = $xml->xpath("//*[local-name() = 'resultObtEmpresasUsuario']");

        $lista_empresas = array();

        for ($i=0; $i < count($datos[0]->empresasUsu); $i++) {
          $empresa_class = new stdClass();
          $empresa_class->correoElectronico = trim((string)$datos[0]->empresasUsu[$i]->correoElectronico);
          $empresa_class->esDuenio = trim((string)$datos[0]->empresasUsu[$i]->esDuenio);
          $empresa_class->razonSocial = trim((string)$datos[0]->empresasUsu[$i]->razonSocial);
          $empresa_class->rutEmpresa = trim((string)$datos[0]->empresasUsu[$i]->rutEmpresa);
          $lista_empresas[$i] = $empresa_class;
        }

        return $lista_empresas;
      }
      catch(Exception $e){
        log_message('Error', $e->getMessage());
        return false;
      }
    }

    public static function ws_permisos_tramites_usuario_grep($documento_usuario, $rut_empresa) {
      //web service GREP permisos de un usuario para acceder a teamites
      try {
        $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://agesic.gub.uy/gestionRepresentante/v1">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <v1:obtTramitesUsuarioEmpresa>
                             <paramObtTramitesUsuarioEmpresa>
                                <rutEmpresa>'.$rut_empresa.'</rutEmpresa>
                                <usuario>'.$documento_usuario.'</usuario>
                             </paramObtTramitesUsuarioEmpresa>
                          </v1:obtTramitesUsuarioEmpresa>
                       </soapenv:Body>
                    </soapenv:Envelope>';

        $soap_header = array(
         "Content-type: text/xml;charset=\"utf-8\"",
         "Accept: text/xml",
         "Cache-Control: no-cache",
         "Pragma: no-cache",
         "Content-length: ". strlen($soap_body)
        );

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, WS_PERMISOS_TRAMITES_USUARIO_GREP);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);
        $soap_response = curl_exec($soap_do);
        $curl_errno = curl_errno($soap_do);
        $curl_error = curl_error($soap_do);
        curl_close($soap_do);

        if ($curl_errno > 0) {
          log_message('Error', $curl_error);
          return false;
        }

        $xml = new SimpleXMLElement($soap_response);
        $datos = $xml->xpath("//*[local-name() = 'resultObtTramitesUsuarioEmpresa']");

        $lista_tramites = array();

        for ($i=0; $i < count($datos[0]->codTramites); $i++) {
          $lista_tramites[$i] = (int)$datos[0]->codTramites[$i];
        }

        return $lista_tramites;
      }
      catch(Exception $e){
        log_message('Error', $e->getMessage());
        return false;
      }
    }

    public static function lista_empresas_usuario() {
        $CI = & get_instance();

        $empresas = $CI->session->userdata('empresas_usuario');
        if($empresas && count($empresas) > 1) {
            return $empresas;
        }
        else {
          return false;
        }
    }

    public static function login_o_registrar_empresa($rut, $razon_social, $email , $cuenta_id) {
        $CI = & get_instance();
        $usuario_empresa_encontrada =  Doctrine_Query::create()
                                      ->from('Usuario u')
                                      ->where('u.usuario = ?', $rut)
                                      ->andWhere('u.cuenta_id = ?', $cuenta_id)
                                      ->fetchOne();

        if ($usuario_empresa_encontrada) {
            if(self::usuario_actuando_como_empresa()){
              $usuario_real = self::usuario_actuando_como_empresa();
            }
            else{
              $usuario_real = $CI->session->userdata('usuario_id');
            }
            $empresas = $CI->session->userdata('empresas_usuario');

            //no debe borrar el de nivel de confianza
            self::borrar_todos_los_datos_sesion();

            $CI->session->set_userdata('usuario_id', $usuario_empresa_encontrada->id);
            self::$user = Doctrine::getTable('Usuario')->find($usuario_empresa_encontrada->id);

            $CI->session->set_userdata('usuario_actuando_como_empresa', $usuario_real);

            $CI->session->set_userdata('empresas_usuario', $empresas);
        }
        else{
            $usuario = new Usuario();
            $usuario->usuario = $rut;
            $usuario->nombres = $razon_social;
            $usuario->email = $email;
            $usuario->registrado = 1;
            $usuario->cuenta_id = $cuenta_id;
            $usuario->save();

            $usuario_empresa_encontrada = Doctrine_Query::create()
                                          ->from('Usuario u')
                                          ->where('u.usuario = ?', $rut)
                                          ->andWhere('u.cuenta_id = ?', $cuenta_id)
                                          ->fetchOne();

            if(self::usuario_actuando_como_empresa()){
              $usuario_real = self::usuario_actuando_como_empresa();
            }
            else{
              $usuario_real = $CI->session->userdata('usuario_id');
            }

            $empresas = $CI->session->userdata('empresas_usuario');

            self::borrar_todos_los_datos_sesion();

            $CI->session->set_userdata('usuario_id', $usuario_empresa_encontrada->id);
            self::$user = Doctrine::getTable('Usuario')->find($usuario_empresa_encontrada->id);

            $CI->session->set_userdata('usuario_actuando_como_empresa', $usuario_real);

            $CI->session->set_userdata('empresas_usuario', $empresas);
        }
    }

   public static function borrar_todos_los_datos_sesion(){
     $CI = & get_instance();
     $user_data_session= $CI->session->all_userdata();

     foreach ($user_data_session as $key => $value) {
         if ($key!= 'usuario_nivel_confianza' && $key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
             $CI->session->unset_userdata($key);
         }
     }
   }

   public static function usuario_con_empresas_luego_login(){
     //usuario_con_empresas_luego_login solo se utiliza la primera vez que se loguea,
     //luego se borra la variable de session y solo se valida lista_empresas_usuario()
     $CI = & get_instance();
     return self::lista_empresas_usuario() && $CI->session->userdata('usuario_con_empresas_luego_login');
   }

   public static function usuario_actuando_como_empresa(){
     $CI = & get_instance();
     if($CI->session->userdata('usuario_actuando_como_empresa')){
       return $CI->session->userdata('usuario_actuando_como_empresa');
     }
     else{
       return false;
     }
   }

   public static function regresar_usuario_real_grep($usuario, $cuenta_id){
     self::borrar_todos_los_datos_sesion();
     self::login_saml($usuario, $cuenta_id);
   }

}
