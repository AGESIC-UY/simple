<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Formularios extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='modelamiento'){
            redirect('backend');
        }
    }

    public function listar($proceso_id) {
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $data['proceso']=$proceso;
        $data['formularios']=$data['proceso']->Formularios;

        $data['title']='Formularios';
        $data['content']='backend/formularios/index';

        $this->load->view('backend/template',$data);
    }

    public function crear($proceso_id) {
        $proceso=Doctrine::getTable('Proceso')->find($proceso_id);

        if($proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para crear un formulario dentro de este proceso.';
            exit;
        }

        $formulario=new Formulario();
        $formulario->proceso_id=$proceso->id;
        $formulario->nombre='Formulario-'.$this->generar_codigo_formulario();
        $formulario->save();

        redirect('backend/formularios/editar/'.$formulario->id);
    }

    public function eliminar($formulario_id) {
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar este formulario.';
            exit;
        }

        $proceso=$formulario->Proceso;
        $formulario->delete();

        redirect('backend/formularios/listar/'.$proceso->id);
    }


    public function editar($formulario_id) {
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $data['formulario']=$formulario;
        $data['proceso']=$formulario->Proceso;

        $data['title']=$formulario->nombre;
        $data['content']='backend/formularios/editar';

        $this->load->view('backend/template',$data);
    }

    public function ajax_editar($formulario_id) {
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $data['formulario']=$formulario;

        $this->load->view('backend/formularios/ajax_editar',$data);
    }

    public function editar_form($formulario_id) {
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este formulario.';
            exit;
        }

        $this->form_validation->set_rules('nombre','Nombre','required');

        if($this->input->post('contenedor')) {
          $this->form_validation->set_rules('leyenda','Leyenda','required');
        }

        $respuesta=new stdClass();
        if ($this->form_validation->run()==TRUE) {
            $formulario->nombre=$this->input->post('nombre');
            $formulario->leyenda=$this->input->post('leyenda');
            $formulario->contenedor=$this->input->post('contenedor');
            $formulario->save();

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/formularios/editar/'.$formulario->id);

        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function ajax_editar_campo($campo_id){
        $campo=Doctrine::getTable('Campo')->find($campo_id);

        if($campo->Formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para editar este campo.';
            exit;
        }

        $pagos = array();
        foreach($campo->Formulario->Proceso->Acciones as $accion) {
          if($accion->tipo == 'pasarela_pago') {
            array_push($pagos, $accion);
          }
        }

        $bloques = Doctrine_Query::create()->from('Bloque')->execute();

        $data['edit']=TRUE;
        $data['campo']=$campo;
        $data['formulario']=$campo->Formulario;
        $data['pagos'] = $pagos;
        $data['bloques'] = $bloques;

        $this->load->view('backend/formularios/ajax_editar_campo',$data);
    }

    public function editar_campo_form($campo_id=NULL){
        $campo=NULL;
        if($campo_id){
            $campo=Doctrine::getTable('Campo')->find($campo_id);

            if($campo->Formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id) {
                echo 'Usuario no tiene permisos para editar este campo.';
                exit;
            }

            $formulario_id = $campo->Formulario->id;
        }
        else {
            $formulario=Doctrine::getTable('Formulario')->find($this->input->post('formulario_id'));

            if($this->input->post('tipo') != 'bloque') {
                $campo=Campo::factory($this->input->post('tipo'));
                $campo->formulario_id=$formulario->id;
                $campo->posicion=1+$formulario->getUltimaPosicionCampo();
            }
            else {
              $this->form_validation->set_rules('valor_default', 'valor_default', 'required');

              if((!$this->input->post('nombre')) || (!$this->input->post('etiqueta'))) {
                $respuesta->validacion=FALSE;
                $respuesta->errores=validation_errors();

                echo json_encode($respuesta);
                exit;
              }
            }

            $formulario_id = $formulario->id;
        }

        $this->form_validation->set_rules('nombre','Nombre', 'required');
        $this->form_validation->set_rules('etiqueta','Etiqueta', 'required');
        $this->form_validation->set_rules('validacion','Validación', 'callback_clean_validacion');

        if(!$campo_id) {
            $this->form_validation->set_rules('formulario_id','Formulario','required|callback_check_permiso_formulario');
            $this->form_validation->set_rules('tipo','Tipo de Campo','required');
        }

        if(isset($campo))
            $campo->backendExtraValidate();

        $respuesta=new stdClass();
        if($this->form_validation->run()==TRUE) {
            if(!$campo) {

            }
            else {
                $campo->nombre=str_replace(" ", "_", $this->input->post('nombre'));
                $campo->etiqueta=$this->input->post('etiqueta',false);
                $campo->readonly=$this->input->post('readonly');
                $campo->valor_default=$this->input->post('valor_default',false);
                $campo->ayuda=$this->input->post('ayuda');
                $campo->ayuda_ampliada=$this->input->post('ayuda_ampliada');
                $campo->validacion=explode('|',$this->input->post('validacion'));
                $campo->dependiente_tipo=$this->input->post('dependiente_tipo');
                $campo->dependiente_campo=$this->input->post('dependiente_campo');
                $campo->dependiente_valor=$this->input->post('dependiente_valor');
                $campo->dependiente_relacion=$this->input->post('dependiente_relacion');
                $campo->datos=$this->input->post('datos');
                $campo->documento_id=$this->input->post('documento_id');
                $campo->fieldset=$this->input->post('fieldset');

                if($campo->tipo=='pagos'){
                  $campo->pago_online = $this->input->post('check_pago_online');
                }

                if($campo->tipo=='agenda'){
                  $campo->requiere_agendar = $this->input->post('check_requiere_agendar');
                }

                if($campo->tipo=='documento'){
                  $campo->firma_electronica = $this->input->post('check_firma_electronica');
                }

                //si el tipo de campo es un tabla responsive
                //cuando se quitan las columnas intermedias de la tabla//el array queda con indices vacios
                //por ejemplo extra[0], extra[3] esto hace que el json_decode lo haga mal
                //se recrea el array para que los indices queden correlativos.
                if ($campo->tipo=='tabla-responsive'){
                  $newarray = array();
                  $cols = array();
                  $arra = $this->input->post('extra')["columns"];
                  foreach ($arra as $element) {
                    array_push($cols,$element);
                  }
                  $newarray["columns"] = $cols;

                  $campo->extra=$newarray;

                }else{
                  $campo->extra=$this->input->post('extra');
                }






                $campo->documento_tramite=$this->input->post('documento_tramite');
                $campo->email_tramite=$this->input->post('email_tramite');
                $campo->save();
            }
        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        if($this->input->post('tipo') == 'bloque') {
            $formulario_bloque = Doctrine_Query::create()->from('Formulario f')
                        ->where('f.bloque_id = ?', $this->input->post('valor_default'))
                        ->execute();
            $formulario_bloque = $formulario_bloque[0];
            $campos_bloque = Doctrine_Query::create()->from('Campo c')
                        ->where('c.formulario_id = ?', $formulario_bloque->id)
                        ->orderBy('c.posicion')
                        ->execute();

            foreach($campos_bloque as $campo_bloque) {
                $campo_nuevo=Campo::factory($campo_bloque->tipo);
                $campo_nuevo->formulario_id=$formulario_id;

                if(($campo_bloque->tipo == 'fieldset') || ($campo_bloque->tipo == 'encuesta')) {
                  $campo_nuevo->nombre='BLOQUE_'.$this->input->post('nombre').'.'.$campo_bloque->nombre;
                }
                else {
                    $campo_nuevo->nombre=$campo_bloque->nombre;
                }

                $campo_nuevo->etiqueta=$campo_bloque->etiqueta;
                $campo_nuevo->readonly=$campo_bloque->readonly;
                $campo_nuevo->valor_default=$campo_bloque->valor_default;
                $campo_nuevo->ayuda=$campo_bloque->ayuda;
                $campo_nuevo->ayuda_ampliada=$campo_bloque->ayuda_ampliada;
                $campo_nuevo->validacion=$campo_bloque->validacion;
                $campo_nuevo->dependiente_tipo=$campo_bloque->dependiente_tipo;
                $campo_nuevo->dependiente_campo=$campo_bloque->dependiente_campo;
                $campo_nuevo->dependiente_valor=$campo_bloque->dependiente_valor;
                $campo_nuevo->dependiente_relacion=$campo_bloque->dependiente_relacion;
                $campo_nuevo->datos=$campo_bloque->datos;
                $campo_nuevo->documento_id=$campo_bloque->documento_id;
                $campo_nuevo->fieldset=$this->input->post('nombre').'.'.$campo_bloque->fieldset;
                $campo_nuevo->posicion=$campo_bloque->posicion;
                $campo_nuevo->extra=$campo_bloque->extra;
                $campo_nuevo->save();
            }
        }

        if($this->form_validation->run() == TRUE) {
            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/formularios/editar/'.$formulario_id);
        }

        echo json_encode($respuesta);
    }

    public function ajax_agregar_campo($formulario_id, $tipo){
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para agregar campos a este formulario.';
            exit;
        }

        $campo=Campo::factory($tipo);
        $campo->formulario_id=$formulario_id;

        $pagos = array();
        foreach($formulario->Proceso->Acciones as $accion) {
          if($accion->tipo == 'pasarela_pago') {
            array_push($pagos, $accion);
          }
        }

        $bloques = Doctrine_Query::create()->from('Bloque')->execute();

        $data['edit']=false;
        $data['formulario']=$formulario;
        $data['campo']=$campo;
        $data['pagos'] = $pagos;
        $data['bloques'] = $bloques;

        $this->load->view('backend/formularios/ajax_editar_campo',$data);
    }

    public function eliminar_campo($campo_id){
        $campo=Doctrine::getTable('Campo')->find($campo_id);

        if($campo->Formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            echo 'Usuario no tiene permisos para eliminar este campo.';
            exit;
        }

        $formulario=$campo->Formulario;
        $campo->delete();

        redirect('backend/formularios/editar/'.$formulario->id);
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

    public function check_permiso_formulario($formulario_id){
        $formulario=Doctrine::getTable('Formulario')->find($formulario_id);

        if($formulario->Proceso->cuenta_id!=UsuarioBackendSesion::usuario()->cuenta_id){
            $this->form_validation->set_message('check_permiso_formulario' ,'Usuario no tiene permisos para agregar campos a este formulario.');
            return FALSE;
        }

        return TRUE;
    }

    function clean_validacion($validacion){
        return preg_replace('/\|\s*$/','',$validacion);
    }

    public function generar_codigo_formulario($length=6) {
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
