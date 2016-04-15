<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Documentos extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='modelamiento'){
            //echo 'No tiene permisos para acceder a esta seccion.';
            //exit;
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
        $data['documentos'] = $data['proceso']->Documentos;

        $data['title'] = 'Documentos';
        $data['content'] = 'backend/documentos/index';

        $this->load->view('backend/template', $data);
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
        $data['content'] = 'backend/documentos/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($documento_id) {
        $documento = Doctrine::getTable('Documento')->find($documento_id);

        if ($documento->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar este documento';
            exit;
        }

        $data['documento'] = $documento;
        $data['edit'] = TRUE;
        $data['proceso']=$documento->Proceso;
        $data['title'] = 'Edición de Documento';
        $data['content'] = 'backend/documentos/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($documento_id=NULL){
        $documento=NULL;
        if($documento_id){
            $documento=Doctrine::getTable('Documento')->find($documento_id);
        }else{
            $documento=new Documento();
            $documento->proceso_id=$this->input->post('proceso_id');
        }

        if($documento->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
                echo 'Usuario no tiene permisos para editar este documento.';
                exit;
            }

        $this->form_validation->set_rules('nombre','Nombre','required');
        $this->form_validation->set_rules('tipo','Tipo','required');
        $this->form_validation->set_rules('contenido','Contenido','required');

        if($this->input->post('tipo')=='certificado'){
            $this->form_validation->set_rules('titulo','Título','required');
            $this->form_validation->set_rules('subtitulo','Subtítulo','required');
            $this->form_validation->set_rules('servicio','Servicio','required');
            $this->form_validation->set_rules('servicio_url','URL del Servicio','required|prep_url');
            $this->form_validation->set_rules('firmador_nombre','Nombre del firmador');
            $this->form_validation->set_rules('firmador_cargo','Cargo del firmador');
            $this->form_validation->set_rules('firmador_servicio','Servicio del firmador');
            $this->form_validation->set_rules('firmador_imagen','Imagen de la firmas');
            $this->form_validation->set_rules('validez','Dias de validez','is_natural_no_zero');
            $this->form_validation->set_rules('validez_habiles','Habiles');
        }

        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE){
            $documento->nombre=$this->input->post('nombre');
            $documento->tipo=$this->input->post('tipo');
            $documento->contenido=$this->input->post('contenido',false);
            $documento->tamano=$this->input->post('tamano');
            $documento->hsm_configuracion_id=$this->input->post('hsm_configuracion_id');

            if($documento->tipo=='certificado'){
                $documento->titulo=$this->input->post('titulo');
                $documento->subtitulo=$this->input->post('subtitulo');
                $documento->servicio=$this->input->post('servicio');
                $documento->servicio_url=$this->input->post('servicio_url');
                $documento->logo=$this->input->post('logo');
                $documento->timbre=$this->input->post('timbre');
                $documento->firmador_nombre=$this->input->post('firmador_nombre');
                $documento->firmador_cargo=$this->input->post('firmador_cargo');
                $documento->firmador_servicio=$this->input->post('firmador_servicio');
                $documento->firmador_imagen=$this->input->post('firmador_imagen');
                $documento->validez=$this->input->post('validez');
                $documento->validez_habiles=$this->input->post('validez_habiles');
            }

            $documento->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/documentos/listar/'.$documento->Proceso->id);
        }else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function previsualizar($documento_id){
        $documento=Doctrine::getTable('Documento')->find($documento_id);

        if($documento->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos';
            exit;
        }

        $documento->previsualizar();
    }


    public function eliminar($documento_id){
        $documento=Doctrine::getTable('Documento')->find($documento_id);

        if($documento->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar este documento.';
            exit;
        }

        $proceso=$documento->Proceso;
        $documento->delete();

        redirect('backend/documentos/listar/'.$proceso->id);

    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
