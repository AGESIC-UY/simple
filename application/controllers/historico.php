<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Historico extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        if(UsuarioSesion::usuario()->registrado) {

          $usuario_backend_sesion = Doctrine::getTable('UsuarioBackend')->findOneByUsuarioAndCuentaId(UsuarioSesion::usuario()->usuario,UsuarioSesion::usuario()->cuenta_id);
          // if ($u && $u->rol == 'seguimiento'){
          if($usuario_backend_sesion && (UsuarioBackend::user_has_rol($usuario_backend_sesion->id, 'seguimiento') || UsuarioBackend::user_has_rol($usuario_backend_sesion->id, 'super'))){
            $usuario_con_acceso = true;
          }else{
            $usuario_con_acceso = false;
          }

          if(!$usuario_con_acceso) {
            redirect('tramites/disponibles');
          }
        }
        else {
            redirect('tramites/disponibles');
        }
    }

    public function ver($tramite_id) {
        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

        $data['tramite'] = $tramite;
        $data['etapas'] = Doctrine_Query::create()->from('Etapa e, e.Tramite t')->where('t.id = ?', $tramite->id)->orderBy('id desc')->execute();

        $data['title'] = 'Seguimiento - ' . $tramite->Proceso->nombre;
        $data['content'] = 'ver_historico';
        $this->load->view('template_iframe', $data);

    }

    public function ver_etapas_ajax($tramite_id, $tarea_identificador) {
        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);

        $etapas = Doctrine_Query::create()
                ->from('Etapa e, e.Tarea tar, e.Tramite t')
                ->where('t.id = ? AND tar.identificador = ?', array($tramite_id, $tarea_identificador))
                ->execute();


        $data['etapas'] = $etapas;


        $this->load->view('ver_etapas_ajax', $data);
    }

    public function ver_etapa($etapa_id, $secuencia = 0) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $paso = $etapa->getPasoEjecutable($secuencia);

        $data['etapa'] = $etapa;
        $data['paso']=$paso;
        $data['secuencia'] = $secuencia;

        $data['title'] = 'Seguimiento - ' . $etapa->Tarea->nombre;
        $data['content'] = 'ver_etapa_historico';
        $this->load->view('template_iframe', $data);
    }

}
