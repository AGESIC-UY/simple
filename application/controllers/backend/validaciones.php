<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Validaciones extends MY_BackendController {

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

        $data['proceso'] = $proceso;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['validaciones'] = $data['proceso']->Validaciones;
        $data['title'] = 'Validaciones';
        $data['content'] = 'backend/validaciones/index';

        $this->load->view('backend/template', $data);
    }

    public function crear($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para crear esta validación';
            exit;
        }

        $data['edit'] = FALSE;
        $data['proceso'] = $proceso;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['title'] = 'Edición de Validación';
        $data['content'] = 'backend/validaciones/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($validacion_id) {
        $validacion = Doctrine::getTable('Validacion')->find($validacion_id);

        if ($validacion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar esta validación';
            exit;
        }

        $data['validacion'] = $validacion;
        $data['edit'] = TRUE;
        $data['proceso'] = $validacion->Proceso;
        $procesosArchivados = $validacion->Proceso->findProcesosArchivados((($validacion->Proceso->root) ? $validacion->Proceso->root : $validacion->Proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['title'] = 'Edición de Validación';
        $data['content'] = 'backend/validaciones/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($validacion_id = NULL) {
        $CI = & get_instance();
        $CI->load->helper('filename_concurrencia_helper');
        $op = "update";
        if ($validacion_id) {
            $validacion = Doctrine::getTable('Validacion')->find($validacion_id);
            $nombre = $validacion->filename;
            if (is_file(DIR_VALIDACION . $nombre)) {
                unlink(DIR_VALIDACION . $nombre);
            }
        } else {
            $validacion = new Validacion();
            $validacion->proceso_id = $this->input->post('proceso_id');
            $op = "insert";
        }

        if ($validacion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar esta validación.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('contenido', 'Contenido', 'required');


        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $validacion->nombre = $this->input->post('nombre');
            $validacion->contenido = $this->input->post('contenido', false);
            $nombre = obtenerFileName() . ".js";
            $validacion->filename = $nombre;

            $validacion->save();
            auditar('Validacion', $op, $validacion->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/validaciones/listar/' . $validacion->Proceso->id);
            $this->load->helper('validacion_file_helper');
            validacionFile($nombre, $validacion);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($validacion_id) {
        $validacion = Doctrine::getTable('Validacion')->find($validacion_id);

        if ($validacion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar esta validación.';
            exit;
        }

        $ejec_validacion = Doctrine::getTable('EjecutarValidacion')->findByValidacionId($validacion_id);
        foreach ($ejec_validacion as $value) {
            $value->delete();
        }
        $nombre = $validacion->filename;
        unlink(DIR_VALIDACION . $nombre);

        $proceso = $validacion->Proceso;
        auditar('Validacion', "delete", $validacion->id, UsuarioBackendSesion::usuario()->usuario);
        $validacion->delete();

        redirect('backend/validaciones/listar/' . $proceso->id);
    }

}
