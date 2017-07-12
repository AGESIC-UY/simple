<?php

class Cuenta extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('nombre_largo');
        $this->hasColumn('mensaje');
        $this->hasColumn('logo');
        $this->hasColumn('api_token');
        $this->hasColumn('codigo_analytics');
        $this->hasColumn('correo_remitente');
    }

    function setUp() {
        parent::setUp();

        $this->hasMany('UsuarioBackend as UsuariosBackend', array(
            'local' => 'id',
            'foreign' => 'cuenta_id'
        ));

        $this->hasMany('Usuario as Usuarios', array(
            'local' => 'id',
            'foreign' => 'cuenta_id'
        ));

        $this->hasMany('GrupoUsuarios as GruposUsuarios', array(
            'local' => 'id',
            'foreign' => 'cuenta_id'
        ));

        $this->hasMany('Proceso as Procesos', array(
            'local' => 'id',
            'foreign' => 'cuenta_id'
        ));

        $this->hasMany('Widget as Widgets', array(
            'local' => 'id',
            'foreign' => 'cuenta_id',
            'orderBy' => 'posicion'
        ));

        $this->hasMany('HsmConfiguracion as HsmConfiguraciones', array(
            'local' => 'id',
            'foreign' => 'cuenta_id'
        ));

        $this->hasOne('Pdi', array(
            'local' => 'id',
            'foreign' => 'cuenta_id'
        ));
    }

    public function updatePosicionesWidgetsFromJSON($json) {
        $posiciones = json_decode($json);

        Doctrine_Manager::connection()->beginTransaction();
        foreach ($this->Widgets as $c) {
            $c->posicion = array_search($c->id, $posiciones);
            $c->save();
        }
        Doctrine_Manager::connection()->commit();
    }

    //Retorna el objecto cuenta perteneciente a este dominio.
    //Retorna null si no estamos en ninguna cuenta valida.
    public static function cuentaSegunDominio() {
        static $firstTime=true;
        static $cuentaSegunDominio=null;
        if ($firstTime) {
            $firstTime=false;
            $CI = &get_instance();
            $host = $CI->input->server('HTTP_HOST');
            $main_domain=$CI->config->item('main_domain');
            if($main_domain) {
              $main_domain = addcslashes($main_domain,'.');
              preg_match('/(.+)\.'.$main_domain.'/', $host, $matches);
              if (isset ($matches[1])) {
                  $cuentaSegunDominio = Doctrine::getTable('Cuenta')->findOneByNombre($matches[1]);
              }
              else {
                if(CUENTA_DEFAULT_RAIZ) {
                  $cuentaSegunDominio=Doctrine_Query::create()->from('Cuenta c')->limit(1)->fetchOne();
                }
              }
            }
            else {
              $cuentaSegunDominio=Doctrine_Query::create()->from('Cuenta c')->limit(1)->fetchOne();
            }
        }

        return $cuentaSegunDominio;
    }

    public function getLogoADesplegar(){
        if($this->logo)
            return base_url('uploads/logos/'.$this->logo);
        else
            return base_url('assets/img/logo.svg');
    }

    public function usesClaveUnicaOnly(){
        foreach($this->Procesos as $p){
            $tareaInicial=$p->getTareaInicial();
            if($tareaInicial && $tareaInicial->acceso_modo!='claveunica')
                return false;
        }

        return true;
    }
}
