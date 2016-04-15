<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportes extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='gestion'){
            //echo 'No tiene permisos para acceder a esta seccion.';
            //exit;
            redirect('backend');
        }
    }

    public function index(){
        $procesos = Doctrine_Query::create()
            ->from('Proceso p')
            ->where('p.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
            ->where('p.nombre != ?', 'BLOQUE')
            ->orderBy('p.id')
            ->execute();

        $data['procesos']=$procesos;

        $data['title'] = 'Gestión';
        $data['content'] = 'backend/reportes/index';

        $this->load->view('backend/template', $data);
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $reportes = Doctrine_Query::create()
            ->from('Reporte r')
            ->where('r.proceso_id = ?', $proceso_id)
            ->execute();

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }
        $data['proceso'] = $proceso;
        $data['reportes'] = $reportes;

        $data['title'] = 'Documentos';
        $data['content'] = 'backend/reportes/listar';

        $this->load->view('backend/template', $data);
    }

    public function ver($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $reporte->generar();
    }

    public function crear($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para crear este documento';
            exit;
        }

        $data['edit'] = FALSE;
        $data['proceso'] = $proceso;
        $data['title'] = 'Edición de Documento';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar este documento';
            exit;
        }

        $data['reporte'] = $reporte;
        $data['edit'] = TRUE;
        $data['proceso'] = $reporte->Proceso;
        $data['title'] = 'Edición de Reporte';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($reporte_id = NULL) {
        $reporte = NULL;
        if ($reporte_id) {
            $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        } else {
            $reporte = new Reporte();
            $reporte->proceso_id = $this->input->post('proceso_id');
        }

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este documento.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('campos', 'Campos[]', 'required');

        $respuesta=new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $reporte->nombre = $this->input->post('nombre');
            $reporte->campos = $this->input->post('campos');
            $reporte->save();

            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/reportes/listar/' . $reporte->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este documento.';
            exit;
        }

        $proceso = $reporte->Proceso;
        $reporte->delete();

        redirect('backend/reportes/listar/' . $proceso->id);
    }

    public function reporte_satisfaccion($reporte_id = null) {

        $page_size = MAX_REGISTROS_PAGINA;
        $page_num = (int)($this->input->get('p') == null ? 1 : $this->input->get('p'));
        $offset = $page_size * ($page_num - 1);

        if(!UsuarioBackendSesion::usuario()->cuenta_id) {
            redirect('/backend');
        }

        if($reporte_id) {
            $reporte = Doctrine::getTable('ReporteSatisfaccion')->find($reporte_id);
            $reporte->reporte = json_decode($reporte->reporte);

            $data['detalle'] = $reporte;
            $data['title'] = 'Reporte de satisfacción';
            $data['content'] = 'backend/reportes/reporte_satisfaccion_detalle';

            $this->load->view('backend/template', $data);
        }
        else {
            $data['reportes'] = Doctrine_Query::create()
                ->from('ReporteSatisfaccion rs')
                ->orderBy('rs.id')
                ->offset($offset)
                ->limit($page_size)
                ->orderBy('rs.id DESC')
                ->execute();

            $data['reportes_chart'] = Doctrine_Query::create()
                ->from('ReporteSatisfaccion rs')
                ->orderBy('rs.id')
                ->limit(100)
                ->orderBy('rs.id DESC')
                ->execute();

            $total = Doctrine_Query::create()
                ->from('ReporteSatisfaccion rs')
                ->count();

            $data['reportes_total'] = $total;
            $data['pagina_actual'] = $page_num;
            $data['tam_pagina'] = $page_size;

            $data['title'] = 'Reporte de satisfacción';
            $data['content'] = 'backend/reportes/reporte_satisfaccion';

            $this->load->view('backend/template', $data);
        }
    }
}
