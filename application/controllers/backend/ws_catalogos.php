<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ws_catalogos extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('gestion')) {
            redirect('backend');
        }
    }

    public function index() {
        $data['catalogos'] = Doctrine_Query::create()
            ->from('WsCatalogo c')
            ->orderBy('c.nombre')
            ->execute();

        $data['title']='Catalogo';
        $data['content']='backend/ws_catalogos/index';

        $this->load->view('backend/template', $data);
    }

    public function crear(){
        $catalogo=new WsCatalogo();
        $catalogo->activo=1;
        $catalogo->nombre='Servicio';
        $catalogo->tipo='soap';
        $catalogo->wsdl='WSDL URL';
        $catalogo->conexion_timeout='Timeout de conexión';
        $catalogo->respuesta_timeout='Timeout de respuesta';
        $catalogo->endpoint_location='Endpoint location';
        $catalogo->url_fisica='URL';
        $catalogo->url_logica='URL';
        $catalogo->rol='Rol';

        $catalogo->save();

        redirect('backend/ws_catalogos/editar/'.$catalogo->id);
    }

    public function eliminar($catalogo_id){
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);

        $catalogo->delete();

        redirect('backend/ws_catalogos/index/');
    }

    public function editar($catalogo_id) {
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);

        $data['catalogo'] = $catalogo;

        $data['title'] = 'Catalogos de Servicio';
        $data['content'] = 'backend/ws_catalogos/editar';
        $this->load->view('backend/template', $data);
    }

    public function ajax_editar($catalogo_id){
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);

        $data['catalogo']=$catalogo;

        $this->load->view('backend/ws_catalogos/ajax_editar',$data);
    }

    public function editar_form($catalogo_id){
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);

        if($this->input->post('tipo') == 'soap') {
          $this->form_validation->set_rules('activo', 'Activo');
          $this->form_validation->set_rules('nombre', 'Nombre', 'required');
          $this->form_validation->set_rules('wsdl', 'Wsdl', 'required|prep_url');
          $this->form_validation->set_rules('conexion_timeout', 'Conexion_Timeout', 'numeric');
          $this->form_validation->set_rules('respuesta_timeout', 'Respuesta_Timeout', 'numeric');
          $this->form_validation->set_rules('endpoint_location', 'Endpoint_Location', 'required|prep_url');

          if ($this->form_validation->run() == TRUE) {
              $catalogo->activo=($this->input->post('activo') == '1') ? 1 : 0;
              $catalogo->tipo=$this->input->post('tipo');
              $catalogo->nombre=$this->input->post('nombre');
              $catalogo->wsdl=$this->input->post('wsdl');
              $catalogo->conexion_timeout=$this->input->post('conexion_timeout');
              $catalogo->respuesta_timeout=$this->input->post('respuesta_timeout');
              $catalogo->endpoint_location=$this->input->post('endpoint_location');
              $catalogo->requiere_autenticacion=($this->input->post('requiere_autenticacion')) ? 1 : 0;
              $catalogo->requiere_autenticacion_tipo=$this->input->post('requiere_autenticacion_tipo');
              $catalogo->autenticacion_basica_user=$this->input->post('autenticacion_basica_user');
              $catalogo->autenticacion_basica_pass=$this->input->post('autenticacion_basica_pass');
              $catalogo->autenticacion_basica_cert=$this->input->post('autenticacion_basica_cert');
              $catalogo->autenticacion_basica_cert_pass=$this->input->post('autenticacion_basica_cert_pass');
              $catalogo->autenticacion_mutua_client=$this->input->post('autenticacion_mutua_client');
              $catalogo->autenticacion_mutua_client_pass=$this->input->post('autenticacion_mutua_client_pass');
              $catalogo->autenticacion_mutua_server=$this->input->post('autenticacion_mutua_server');
              $catalogo->autenticacion_mutua_user=$this->input->post('autenticacion_mutua_user');
              $catalogo->autenticacion_mutua_pass=$this->input->post('autenticacion_mutua_pass');
              $catalogo->autenticacion_mutua_client_key=$this->input->post('autenticacion_mutua_client_key');

              $catalogo->save();

              $respuesta->validacion=TRUE;
              $respuesta->redirect = site_url('backend/ws_catalogos/index/');
          }
          else {
              $respuesta->validacion=FALSE;
              $respuesta->errores=validation_errors();
          }
        }
        else {
          $this->form_validation->set_rules('activo', 'Activo');
          $this->form_validation->set_rules('nombre', 'Nombre', 'required');
          $this->form_validation->set_rules('url_fisica', 'URL_fisica', 'required|prep_url');
          $this->form_validation->set_rules('conexion_timeout', 'Conexion_Timeout', 'numeric');
          $this->form_validation->set_rules('respuesta_timeout', 'Respuesta_Timeout', 'numeric');
          $this->form_validation->set_rules('url_logica', 'URL_logica', 'required|prep_url');
          $this->form_validation->set_rules('rol', 'Rol', 'required');

          if ($this->form_validation->run() == TRUE) {
              $catalogo->activo=($this->input->post('activo') == '1') ? 1 : 0;
              $catalogo->nombre=$this->input->post('nombre');
              $catalogo->tipo=$this->input->post('tipo');
              $catalogo->url_fisica=$this->input->post('url_fisica');
              $catalogo->url_logica=$this->input->post('url_logica');
              $catalogo->rol=$this->input->post('rol');
              $catalogo->conexion_timeout=$this->input->post('conexion_timeout_pdi');
              $catalogo->respuesta_timeout=$this->input->post('respuesta_timeout_pdi');

              $catalogo->save();

              $respuesta->validacion=TRUE;
              $respuesta->redirect = site_url('backend/ws_catalogos/index/');
          }
          else {
              $respuesta->validacion=FALSE;
              $respuesta->errores=validation_errors();
          }
        }

        echo json_encode($respuesta);
    }

    public function ajax_editar_modelo($catalogo_id) {
        $catalogo = Doctrine::getTable('WsCatalogo')->find($catalogo_id);

        $modelo=$this->input->post('modelo');

        $catalogo->updateModelFromJSON($modelo);
    }

    public function operaciones_index($catalogo_id) {
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);

        $data['catalogo'] = $catalogo;
        $data['operaciones'] = Doctrine_Query::create()
            ->from('WsOperacion o')
            ->where('o.catalogo_id = ?', $catalogo->id)
            ->orderBy('o.nombre')
            ->execute();

        $data['title']='Operaciones de Servicios';
        $data['content']='backend/ws_catalogos/operaciones_index';

        $this->load->view('backend/template', $data);
    }

    public function operaciones_crear($catalogo_id){
        $operacion=new WsOperacion();

        do {
          $codigo = $this->generar_codigo_operacion();

          $codigo_existe = Doctrine_Query::create()
                        ->from('WsOperacion o')
                        ->where('o.codigo = ?', $codigo)
                        ->execute();
        }
        while (!$codigo_existe);

        $operacion->nombre='Operación';
        $operacion->codigo=$codigo;
        $operacion->operacion='NombreRealOperacion';
        $operacion->soap='Cuerpo SOAP';
        $operacion->ayuda='Texto de ayuda';
        $operacion->respuestas='';
        $operacion->save();

        redirect('backend/ws_catalogos/'.$catalogo_id.'/operaciones/editar/'.$operacion->id);
    }

    public function operaciones_editar($catalogo_id, $operacion_id) {
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);
        $operacion=Doctrine::getTable('WsOperacion')->find($operacion_id);
        $operacion_respuestas=Doctrine_Query::create()
            ->from('WsOperacionRespuesta r')
            ->where('r.operacion_id = ?', $operacion_id)
            ->orderBy('r.id')
            ->execute();

        $data['catalogo'] = $catalogo;
        $data['operacion'] = $operacion;
        $data['operacion_respuestas']=$operacion_respuestas;

        $data['title'] = 'Catalogos de Servicio';
        $data['content'] = 'backend/ws_catalogos/operacion_editar';
        $this->load->view('backend/template', $data);
    }

    public function operaciones_eliminar($catalogo_id, $operacion_id){
        $operacion=Doctrine::getTable('WsOperacion')->find($operacion_id);

        $operacion->delete();

        redirect('backend/ws_catalogos/'. $catalogo_id .'/operaciones');
    }

    public function operaciones_ajax_editar($catalogo_id, $operacion_id){
        $catalogo=Doctrine::getTable('WsCatalogo')->find($catalogo_id);
        $operacion=Doctrine::getTable('WsOperacion')->find($operacion_id);

        $data['catalogo']=$catalogo;
        $data['operacion']=$operacion;

        $this->load->view('backend/ws_catalogos/operacion_ajax_editar',$data);
    }

    public function operaciones_editar_form($operacion_id){
        $catalogo_id = $this->input->post('catalogo_id');
        $operacion=Doctrine::getTable('WsOperacion')->find($operacion_id);

        $this->form_validation->set_rules('codigo', 'Codigo', 'required|alpha_numeric|exact_length[12]');
        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('operacion', 'Operacion', 'required');
        $this->form_validation->set_rules('soap', 'Soap', 'required');
        $this->form_validation->set_rules('ayuda', 'Ayuda', 'required');

        $codigo_nuevo =  $this->input->post('codigo');
        $codigo_existe = Doctrine_Query::create()
                      ->from('WsOperacion o')
                      ->where('o.codigo = ?', $codigo_nuevo)
                      ->execute();

        if((count($codigo_existe) > 0) && ($codigo_nuevo != $operacion->codigo)) {
          $respuesta->errores = '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>El campo "<strong>Codigo</strong>" contiene un valor ya existente, por favor ingrese un código único.</div>';
          $respuesta->validacion=FALSE;
        }
        else {
          if ($this->form_validation->run() == TRUE) {
              $operacion->codigo=$codigo_nuevo;
              $operacion->nombre=$this->input->post('nombre');
              $operacion->operacion=$this->input->post('operacion');
              $operacion->catalogo_id=$catalogo_id;
              $operacion->soap=$this->input->post('soap');
              $operacion->ayuda=$this->input->post('ayuda');
              $operacion->respuestas=$this->input->post('respuestas');
              $operacion->save();

              if($this->input->post('xslt')) {
                  $xslt_campos = $_POST['xslt'];

                  $i = 0;
                  foreach($xslt_campos[$operacion->id] as $xslt_respuesta_key => $xslt_respuesta_valor) {
                      $operacion_respuesta = Doctrine_Query::create()->from('WsOperacionRespuesta')->where('respuesta_id = ?', $xslt_respuesta_key)->limit(1)->execute();

                      if(count($operacion_respuesta) > 0) {
                          $operacion_respuesta = $operacion_respuesta[0];
                          $operacion_respuesta->operacion_id = $operacion->id;
                          $operacion_respuesta->respuesta_id = $xslt_respuesta_key;

                          // -- Hack debido a que el atributo 'version' rompe el XML.
                          //    Tomar en cuenta que existe una función en backend_extendido.js
                          //    que se encarga de agregar los simbolos %% para este hack.
                          $xslt_respuesta_valor_nuevo = str_replace('%version%=', 'version=', $xslt_respuesta_valor);
                          $operacion_respuesta->xslt = $xslt_respuesta_valor_nuevo;

                          $operacion_respuesta->save();
                      }
                      else {
                          $nueva_operacion_respuesta=new WsOperacionRespuesta();
                          $nueva_operacion_respuesta->operacion_id = $operacion->id;
                          $nueva_operacion_respuesta->respuesta_id = $xslt_respuesta_key;
                          $nueva_operacion_respuesta->xslt = $xslt_respuesta_valor;
                          $nueva_operacion_respuesta->save();
                      }
                  }
              }

              $respuesta->validacion=TRUE;
              $respuesta->redirect = site_url('backend/ws_catalogos/'.$catalogo_id.'/operaciones');
          }
          else {
              $respuesta->validacion=FALSE;
              $respuesta->errores=validation_errors();
          }
        }

        echo json_encode($respuesta);
    }

    public function generar_codigo_operacion($length=12) {
      $arr = array('a', 'b', 'c', 'd', 'e', 'f',
               'g', 'h', 'i', 'j', 'k', 'l',
               'm', 'n', 'o', 'p', 'r', 's',
               't', 'u', 'v', 'x', 'y', 'z',
               'A', 'B', 'C', 'D', 'E', 'F',
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
