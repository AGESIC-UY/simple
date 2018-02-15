<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portada extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
      $usuario_id =UsuarioSesion::usuario()->id;
      $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();
      $pendientes=Doctrine::getTable('Etapa')->cantidadPendientes(UsuarioSesion::usuario()->id,  $cuenta_segun_dominio);
        if(UsuarioSesion::usuario()->registrado && $pendientes >0)
            redirect('etapas/inbox');
        else
            redirect('tramites/disponibles');
    }

}
