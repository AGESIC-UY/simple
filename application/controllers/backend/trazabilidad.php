<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trazabilidad extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='modelamiento'){
            echo 'No tiene permisos para acceder a esta seccion.';
            exit;
        }
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['proceso'] = $proceso;

        $data['title'] = 'Trazabilidad';
        $data['content'] = 'backend/trazabilidad/index';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($proceso_id) {
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $this->form_validation->set_rules('organismo_id', 'Organismo_ID', 'required');
        $this->form_validation->set_rules('proceso_externo_id', 'Proceso_externo_ID', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $proceso->ProcesoTrazabilidad->organismo_id=$this->input->post('organismo_id');
            $proceso->ProcesoTrazabilidad->proceso_externo_id=$this->input->post('proceso_externo_id');
            $proceso->ProcesoTrazabilidad->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect = site_url('backend/trazabilidad/listar/'.$proceso_id);
        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }
}
