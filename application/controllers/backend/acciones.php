<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Acciones extends MY_BackendController {

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
        $data['acciones'] = $data['proceso']->Acciones;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['title'] = 'Triggers';
        $data['content'] = 'backend/acciones/index';

        $this->load->view('backend/template', $data);
    }

    public function ajax_seleccionar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        $data['servicios'] = Doctrine_Query::create()
                ->from('WsCatalogo c')
                ->where('c.activo = ?', 1)
                ->orderBy('c.nombre')
                ->execute();

        $data['operaciones'] = array();
        foreach ($data['servicios'] as $servicio) {
            $data['operaciones'][$servicio->id] = array();

            $ops = Doctrine_Query::create()
                    ->from('WsOperacion o')
                    ->where('o.catalogo_id = ?', $servicio->id)
                    ->execute();

            foreach ($ops as $op) {
                array_push($data['operaciones'][$servicio->id], $op);
            }
        }

        $data['pasarela_pagos'] = Doctrine_Query::create()
                ->from('PasarelaPago p')
                ->where('p.activo = ?', 1)
                ->orderBy('p.nombre')
                ->execute();

        $data['obn'] = Doctrine_Query::create()
                ->from('ObnStructure p')
                ->orderBy('p.identificador')
                ->execute();

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['proceso_id'] = $proceso_id;
        $this->load->view('backend/acciones/ajax_seleccionar', $data);
    }

    public function seleccionar_form($proceso_id, $operacion = null) {

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $this->form_validation->set_rules('tipo', 'Tipo', 'required');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $tipo = $this->input->post('tipo');
            if ($tipo == "variable_obn" && ($this->input->post('action_obn') != '') && !is_null($operacion)) {
                $operacion = $operacion . "?tipo=" . $this->input->post('action_obn');
            } else if ((!$operacion) && ($this->input->post('operacion') != '')) {
                $operacion = $this->input->post('operacion');
            }
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/acciones/crear/' . $proceso_id . '/' . $tipo . '/' . $operacion);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function crear($proceso_id, $tipo, $operacion = null) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $acciones = Doctrine::getTable('Accion')->findByProcesoId($proceso_id);

        $acciones_array = array();
        foreach ($acciones as $value) {
            if (!($value instanceof AccionPasarelaPago)) {
                $acciones_array[] = $value;
            }
        }
        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        if ($tipo == 'enviar_correo')
            $accion = new AccionEnviarCorreo();
        else if ($tipo == 'webservice')
            $accion = new AccionWebservice();

        else if ($tipo == 'webservice_extended')
            $accion = new AccionWebserviceExtended();

        else if ($tipo == 'pasarela_pago')
            $accion = new AccionPasarelaPago();

        else if ($tipo == 'variable')
            $accion = new AccionVariable();

        else if ($tipo == 'archivo')
            $accion = new AccionArchivo();

        else if ($tipo == 'traza')
            $accion = new AccionTraza();

        else if ($tipo == 'variable_obn') {
            $accion = new AccionVariableObn();
            $accion->extra = ['obn' => $operacion, 'tipo' => $_GET['tipo']];
        }

        $data['edit'] = FALSE;
        $data['proceso'] = $proceso;
        $data['tipo'] = $tipo;
        $data['accion'] = $accion;
        $data['operacion'] = $operacion;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;
        $data['content'] = 'backend/acciones/editar';
        $data['title'] = 'Crear Acción';
        $this->load->view('backend/template', $data);
    }

    public function editar($accion_id) {
        $accion = Doctrine::getTable('Accion')->find($accion_id);
        if (!$accion) {
            exit('La acción solicitada no existe');
        }
        if ($accion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar las acciones de este proceso';
            exit;
        }
        $data['edit'] = TRUE;
        $data['proceso'] = $accion->Proceso;
        $data['accion'] = $accion;
        $procesosArchivados = $accion->Proceso->findProcesosArchivados((($accion->Proceso->root) ? $accion->Proceso->root : $accion->Proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['content'] = 'backend/acciones/editar';
        $data['title'] = 'Editar Acción';
        $this->load->view('backend/template', $data);
    }

    public function editar_form($accion_id = NULL, $operacion = null) {
        $accion = NULL;
        if ($accion_id) {
            $accion = Doctrine::getTable('Accion')->find($accion_id);
            $op = "update";
        } else {
            if ($this->input->post('tipo') == 'enviar_correo')
                $accion = new AccionEnviarCorreo();
            else if ($this->input->post('tipo') == 'webservice')
                $accion = new AccionWebservice();

            else if ($this->input->post('tipo') == 'webservice_extended')
                $accion = new AccionWebserviceExtended();

            else if ($this->input->post('tipo') == 'pasarela_pago') {
                $accion = new AccionPasarelaPago();
            } else if ($this->input->post('tipo') == 'variable')
                $accion = new AccionVariable();

            else if ($this->input->post('tipo') == 'archivo')
                $accion = new AccionArchivo();

            else if ($this->input->post('tipo') == 'traza')
                $accion = new AccionTraza();

            else if ($this->input->post('tipo') == 'variable_obn') {
                $accion = new AccionVariableObn();
                $extra = $this->input->post('extra', false);
                $accion->extra = ['obn' => $extra['obn'], 'tipo' => $extra['tipo']];
            }


            $accion->proceso_id = $this->input->post('proceso_id');
            $accion->tipo = $this->input->post('tipo');
            $op = "insert";
        }

        if ($accion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar esta accion.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        if ($accion->tipo == 'variable_obn') {
            $accion->validateForm($accion->extra->tipo);
        } else {
            $accion->validateForm();
        }
        if (!$accion_id) {
            $this->form_validation->set_rules('proceso_id', 'Proceso', 'required');
            $this->form_validation->set_rules('tipo', 'Tipo', 'required');
        }

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            if (!$accion) {
                
            }

            if (($accion->tipo == 'pasarela_pago' && !isset($accion->extra->metodo)) || (isset($accion->extra->metodo) && $accion->extra->metodo == "antel")) {
                $this->setEventosPagosFromArray($this->input->post('eventos', false), $accion);
            }

            $accion->nombre = $this->input->post('nombre');
            $accion->extra = $this->input->post('extra', false);
            
            
            $accion->save();
            auditar('Accion', $op, $accion->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/acciones/listar/' . $accion->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($accion_id) {
        $accion = Doctrine::getTable('Accion')->find($accion_id);

        if ($accion->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar esta accion.';
            exit;
        }

        $eventoPago = Doctrine::getTable('EventoPago')->findByAccionId($accion->id);

        foreach ($eventoPago as $value) {
            $evento = Doctrine::getTable('EventoPago')->find($value->id);
            auditar('EventoPago', "delete", $evento->id, UsuarioBackendSesion::usuario()->usuario);
            $evento->delete();
        }
        $proceso = $accion->Proceso;
        auditar('Accion', "delete", $accion->id, UsuarioBackendSesion::usuario()->usuario);
        $accion->delete();
        redirect('backend/acciones/listar/' . $proceso->id);
    }

    public function setEventosPagosFromArray($eventos_array, $accion) {
        //Limpiamos la lista antigua
        foreach ($accion->getEventoPago() as $key => $val) {
            $accion->removeEventoPago($val);
            //unset($accion->EventosPagos[$key]);
        }

        //Agregamos los nuevos

        if (is_array($eventos_array)) {
            foreach ($eventos_array as $key => $p) {
                //print_r($p);
                //Seteamos el paso_id solamente si el paso es parte de esta tarea.
                $ex_evento = new EventoPago();
                $ex_evento->regla = $p['regla'];
                $ex_evento->instante = $p['instante'];
                $ex_evento->accion_ejecutar_id = $p['accion_id'];
                $ex_evento->traza = $p['traza'];
                $ex_evento->tipo_registro_traza = $p['tipo_registro_traza'];
                $ex_evento->descripcion_traza = $p['descripcion_traza'];
                $ex_evento->descripcion_error_soap = $p['descripcion_error_soap'];
                $ex_evento->variable_error_soap = $p['variable_error_soap'];
                $ex_evento->etiqueta_traza = $p['etiqueta_traza'];
                $ex_evento->visible_traza = $p['visible_traza'];
                $accion->EventosPagos[] = $ex_evento;
            }
        }
    }

}
