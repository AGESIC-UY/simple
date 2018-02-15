<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Acciones extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('modelamiento')){
            redirect('backend');
        }
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['proceso'] = $proceso;
        $data['acciones'] = $data['proceso']->Acciones;

        $data['title'] = 'Triggers';
        $data['content'] = 'backend/acciones/index';

        $this->load->view('backend/template', $data);
    }

    public function ajax_seleccionar($proceso_id){
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        $data['servicios'] = Doctrine_Query::create()
            ->from('WsCatalogo c')
            ->where('c.activo = ?', 1)
            ->orderBy('c.nombre')
            ->execute();

        $data['operaciones'] = array();
        foreach($data['servicios'] as $servicio) {
          $data['operaciones'][$servicio->id] = array();

          $ops = Doctrine_Query::create()
              ->from('WsOperacion o')
              ->where('o.catalogo_id = ?', $servicio->id)
              ->execute();

          foreach($ops as $op) {
            array_push($data['operaciones'][$servicio->id], $op);
          }
        }

        $data['pasarela_pagos'] = Doctrine_Query::create()
            ->from('PasarelaPago p')
            ->where('p.activo = ?', 1)
            ->orderBy('p.nombre')
            ->execute();

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['proceso_id']=$proceso_id;
        $this->load->view('backend/acciones/ajax_seleccionar',$data);
    }

    public function seleccionar_form($proceso_id, $operacion=null) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $this->form_validation->set_rules('tipo','Tipo','required');

        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE){
            $tipo=$this->input->post('tipo');
            if((!$operacion) && ($this->input->post('operacion') != '')) {
              $operacion = $this->input->post('operacion');
            }
            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/acciones/crear/'.$proceso_id.'/'.$tipo.'/'.$operacion);
        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function crear($proceso_id,$tipo,$operacion=null) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        if($tipo=='enviar_correo')
            $accion=new AccionEnviarCorreo();
        else if($tipo=='webservice')
            $accion=new AccionWebservice();

        else if($tipo=='webservice_extended')
            $accion=new AccionWebserviceExtended();

        else if($tipo=='pasarela_pago')
            $accion=new AccionPasarelaPago();

        else if($tipo=='variable')
            $accion=new AccionVariable();

        else if($tipo=='archivo')
                $accion=new AccionArchivo();

        $data['edit']=FALSE;
        $data['proceso']=$proceso;
        $data['tipo']=$tipo;
        $data['accion']=$accion;
        $data['operacion']=$operacion;

        $data['content']='backend/acciones/editar';
        $data['title']='Crear Acción';
        $this->load->view('backend/template',$data);
    }

    public function editar($accion_id){
        $accion = Doctrine::getTable('Accion')->find($accion_id);

        if ($accion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['edit']=TRUE;
        $data['proceso']=$accion->Proceso;
        $data['accion']=$accion;

        $data['content']='backend/acciones/editar';
        $data['title']='Editar Acción';
        $this->load->view('backend/template',$data);
    }

    public function editar_form($accion_id=NULL, $operacion=null){
        $accion=NULL;
        if($accion_id){
            $accion=Doctrine::getTable('Accion')->find($accion_id);
        }else{
            if($this->input->post('tipo')=='enviar_correo')
                $accion=new AccionEnviarCorreo();
            else if($this->input->post('tipo')=='webservice')
                $accion=new AccionWebservice();

            else if($this->input->post('tipo')=='webservice_extended')
                $accion=new AccionWebserviceExtended();

            else if($this->input->post('tipo')=='pasarela_pago')
                $accion=new AccionPasarelaPago();

            else if($this->input->post('tipo')=='variable')
                $accion=new AccionVariable();

            else if($this->input->post('tipo')=='archivo')
                    $accion=new AccionArchivo();

            $accion->proceso_id=$this->input->post('proceso_id');
            $accion->tipo=$this->input->post('tipo');
        }

        if($accion->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
                echo 'Usuario no tiene permisos para editar esta accion.';
                exit;
            }

        $this->form_validation->set_rules('nombre','Nombre','required');
        $accion->validateForm();
        if(!$accion_id){
            $this->form_validation->set_rules('proceso_id','Proceso','required');
            $this->form_validation->set_rules('tipo','Tipo','required');
        }

        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE){
            if(!$accion){

            }

            $accion->nombre=$this->input->post('nombre');
            $accion->extra=$this->input->post('extra',false);
            $accion->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/acciones/listar/'.$accion->Proceso->id);
        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($accion_id){
        $accion=Doctrine::getTable('Accion')->find($accion_id);

        if($accion->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar esta accion.';
            exit;
        }

        $proceso=$accion->Proceso;
        $accion->delete();

        redirect('backend/acciones/listar/'.$proceso->id);
    }
}
