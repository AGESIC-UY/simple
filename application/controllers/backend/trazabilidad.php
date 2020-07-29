<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Trazabilidad extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('modelamiento')) {
            redirect('backend');
        }
        $this->load->helper('auditoria_helper');
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['cuenta'] = Doctrine::getTable('Cuenta')->find(UsuarioBackendSesion::usuario()->cuenta_id);
        $data['proceso'] = $proceso;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;
        $data['title'] = 'Trazabilidad';
        $data['content'] = 'backend/trazabilidad/index';
        $this->load->view('backend/template', $data);
    }

    public function editar_form($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $envio_guid_automatico = $this->input->post('envio_guid_automatico');
        $email_envio_guid = $this->input->post('email_envio_guid');

        if ($envio_guid_automatico) {

            $variable_modelador = trim($email_envio_guid)[0] == '@' && trim($email_envio_guid)[1] == '@';
            $variable_global = trim($email_envio_guid)[0] == '@' && trim($email_envio_guid)[1] == '!';


            if ($variable_global || $variable_modelador) {
                $this->form_validation->set_rules('email_envio_guid', 'Variable de email', 'required|variable_simple_valida_envio_guid[' . $proceso_id . ']');
            } else {
                $this->form_validation->set_rules('email_envio_guid', 'Variable de email', 'required|valid_emails');
            }
        }
        
        $involucrado = $this->input->post('trazabilidad_involucrado');
        if($involucrado){
            $this->form_validation->set_rules('trazabilidad_involucrado', 'Involucrado', 'required');
        }
        
        $this->form_validation->set_rules('organismo_id', 'Organismo_ID', 'required');
        //$this->form_validation->set_rules('proceso_externo_id', 'Proceso_externo_ID', 'required');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $proceso->ProcesoTrazabilidad->organismo_id = $this->input->post('organismo_id');

            //$proceso->ProcesoTrazabilidad->proceso_externo_id = $this->input->post('proceso_externo_id');

            $proceso->ProcesoTrazabilidad->envio_guid_automatico = $envio_guid_automatico;
            if ($envio_guid_automatico) {
                $email_envio_guid = $this->input->post('email_envio_guid');
            } else {
                $email_envio_guid = null;
            }
            if($involucrado){                
                $proceso->ProcesoTrazabilidad->traza_involucrado = $this->input->post('trazabilidad_involucrado');
            }else{
                $proceso->ProcesoTrazabilidad->traza_involucrado = 0;
            }
            $proceso->ProcesoTrazabilidad->email_envio_guid = $email_envio_guid;
            $proceso->ProcesoTrazabilidad->save();
            auditar('Proceso', "update", $proceso->id, UsuarioBackendSesion::usuario()->usuario);

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/trazabilidad/listar/' . $proceso_id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

}
