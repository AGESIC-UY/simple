<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portada extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        /*if(UsuarioSesion::registrado_saml()) {
          redirect(site_url());
        }
        else {
          return UsuarioBackendSesion::force_login();
        }*/
        return UsuarioBackendSesion::force_login();
    }

    public function index() {
      if(UsuarioBackendSesion::has_rol('super') || UsuarioBackendSesion::has_rol('gestion'))
        redirect('backend/gestion');
      else if (UsuarioBackendSesion::has_rol('modelamiento'))
        redirect('backend/procesos');
      else if(UsuarioBackendSesion::has_rol('operacion') || UsuarioBackendSesion::has_rol('seguimiento'))
        redirect('backend/seguimiento');
      else if(UsuarioBackendSesion::has_rol('configuracion'))
        redirect('backend/configuracion');
      else if(UsuarioBackendSesion::has_rol('desarrollo'))
        redirect('backend/api');
    }
}
