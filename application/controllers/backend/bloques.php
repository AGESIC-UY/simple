<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bloques extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='gestion') {
            //echo 'No tiene permisos para acceder a esta seccion.';
            //exit;
            redirect('backend');
        }
    }

    public function index() {
        $data['bloques'] = Doctrine_Query::create()
            ->from('Bloque b')
            ->orderBy('b.nombre')
            ->execute();

        $data['title']='Bloques';
        $data['content']='backend/bloques/index';

        $this->load->view('backend/template', $data);
    }

    public function crear() {
        $bloque=new Bloque();
        $bloque->nombre='Bloque-'.$this->generar_codigo_bloque();
        $bloque->save();

        $procesos = Doctrine_Query::create()
            ->from('Proceso p')->execute();

        $proceso = new Proceso();
        $proceso->nombre = 'BLOQUE';
        $proceso->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $proceso->save();

        $formulario=new Formulario();
        $formulario->proceso_id=$proceso->id;
        $formulario->bloque_id=$bloque->id;
        $formulario->nombre='Formulario';
        $formulario->save();

        redirect('backend/bloques/editar/'.$bloque->id);
    }

    public function eliminar($bloque_id) {
        $bloque=Doctrine::getTable('Bloque')->find($bloque_id);

        $bloque->delete();

        redirect('backend/bloques/index/');
    }

    public function editar($bloque_id) {
        $data['bloque'] = Doctrine::getTable('Bloque')->find($bloque_id);
        $formulario = Doctrine_Query::create()
            ->from('Formulario f')
            ->where('f.bloque_id = ?', $bloque_id)
            ->execute();

        $data['formulario'] = $formulario[0];

        $data['title'] = 'Catalogos de Bloques';
        $data['content'] = 'backend/bloques/editar';
        $this->load->view('backend/template', $data);
    }

    public function editar_form($bloque_id) {
        $bloque=Doctrine::getTable('Bloque')->find($bloque_id);

        $this->form_validation->set_rules('nombre_bloque', 'Nombre_bloque', 'required');

        if ($this->form_validation->run() == TRUE) {
            $bloque->nombre=$this->input->post('nombre_bloque');
            $bloque->save();

            redirect('backend/bloques/index/');
        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();

            redirect('backend/bloques/editar/' . $bloque->id);
        }
    }

    public function ajax_editar($formulario_id){
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);
        $bloque=Doctrine::getTable('Bloque')->find($this->input->get('bloque_id'));

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $data['formulario']=$formulario;
        $data['bloque']=$bloque;

        $this->load->view('backend/bloques/ajax_editar',$data);
    }

    public function editar_posicion_campos($formulario_id){
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $json=$this->input->post('posiciones');
        $formulario->updatePosicionesCamposFromJSON($json);
    }

    public function ajax_editar_campo($campo_id){
        $campo=Doctrine::getTable('Campo')->find($campo_id);
        $bloque=Doctrine::getTable('Bloque')->find($this->input->get('bloque_id'));

        if($campo->Formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este campo.';
            exit;
        }

        $data['edit']=TRUE;
        $data['campo']=$campo;
        $data['formulario']=$campo->Formulario;
        $data['bloque'] = $bloque;

        $this->load->view('backend/bloques/ajax_editar_campo',$data);
    }

    public function ajax_agregar_campo($formulario_id, $tipo){
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);
        $bloque=Doctrine::getTable('Bloque')->find($this->input->get('bloque_id'));

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para agregar campos a este formulario.';
            exit;
        }

        $campo=Campo::factory($tipo);
        $campo->formulario_id=$formulario_id;


        $data['edit']=false;
        $data['formulario']=$formulario;
        $data['campo']=$campo;
        $data['bloque'] = $bloque;
        $this->load->view('backend/bloques/ajax_editar_campo',$data);
    }

    public function editar_campo_form($campo_id=NULL) {
        $campo=NULL;
        $bloque_id = $this->input->post('bloque_id');

        if($campo_id) {
            $campo=Doctrine::getTable('Campo')->find($campo_id);
        }
        else {
          $formulario=Doctrine::getTable('Formulario')->find($this->input->post('formulario_id'));
          $campo=Campo::factory($this->input->post('tipo'));
          $campo->formulario_id=$formulario->id;
          $campo->posicion=1+$formulario->getUltimaPosicionCampo();
        }

        if($campo->Formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id) {
          echo 'Usuario no tiene permisos para editar este campo.';
          exit;
        }

        $this->form_validation->set_rules('nombre','Nombre','required');
        $this->form_validation->set_rules('etiqueta','Etiqueta','required');
        $this->form_validation->set_rules('validacion','Validación','callback_clean_validacion');
        if(!$campo_id){
            $this->form_validation->set_rules('formulario_id','Formulario','required|callback_check_permiso_formulario');
            $this->form_validation->set_rules('tipo','Tipo de Campo','required');
        }
        $campo->backendExtraValidate();

        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE) {
            if(!$campo){
            }

            $campo->nombre=$this->input->post('nombre');
            $campo->etiqueta=$this->input->post('etiqueta',false);
            $campo->readonly=$this->input->post('readonly');
            $campo->valor_default=$this->input->post('valor_default',false);
            $campo->ayuda=$this->input->post('ayuda');
            $campo->validacion=explode('|',$this->input->post('validacion'));
            $campo->dependiente_tipo=$this->input->post('dependiente_tipo');
            $campo->dependiente_campo=$this->input->post('dependiente_campo');
            $campo->dependiente_valor=$this->input->post('dependiente_valor');
            $campo->dependiente_relacion=$this->input->post('dependiente_relacion');
            $campo->datos=$this->input->post('datos');
            $campo->documento_id=$this->input->post('documento_id');
            $campo->fieldset=$this->input->post('fieldset');
            $campo->extra=$this->input->post('extra');
            $campo->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/bloques/editar/'.$bloque_id);

        } else{
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar_campo($campo_id){
        $campo=Doctrine::getTable('Campo')->find($campo_id);
        $bloque_id = $this->input->get('bloque_id');

        if($campo->Formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar este campo.';
            exit;
        }

        $campo->delete();

        redirect('backend/bloques/editar/'.$bloque_id);
    }

    public function generar_codigo_bloque($length=4) {
      $arr = array('A', 'B', 'C', 'D', 'E', 'F',
               'G', 'H', 'I', 'J', 'K', 'L',
               'M', 'N', 'O', 'P', 'R', 'S',
               'T', 'U', 'V', 'X', 'Y', 'Z',
               '1', '2', '3', '4', '5', '6',
               '7', '8', '9', '0');
      $token = "";
      for ($i = 0; $i < $length; $i++) {
          $index = rand(0, count($arr) - 1);
          $token .= $arr[$index];
      }
      return $token;
    }
}
