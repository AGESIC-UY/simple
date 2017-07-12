<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pasarela_pagos extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if(UsuarioBackendSesion::usuario()->rol!='super' && UsuarioBackendSesion::usuario()->rol!='gestion') {
            redirect('backend');
        }
    }

    public function index() {
        $data['pasarelas'] = Doctrine_Query::create()
            ->from('PasarelaPago p')
            ->where('p.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
            ->orderBy('p.nombre')
            ->execute();

        $data['title']='Pasarela_pagos';
        $data['content']='backend/pasarela_pagos/index';

        $this->load->view('backend/template', $data);
    }

    public function crear(){
        $pasarela=new PasarelaPago();
        $pasarela->activo=1;
        $pasarela->nombre='Pasarela';
        $pasarela->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;

        $pasarela->save();

        redirect('backend/pasarela_pagos/editar/'.$pasarela->id);
    }

    public function eliminar($pasarela_id){
        $pasarela=Doctrine::getTable('PasarelaPago')->find($pasarela_id);

        $pasarela->delete();

        switch($pasarela->metodo) {
            case 'antel':
                $metodo = Doctrine_Query::create()
                    ->from('PasarelaPagoAntel pa')
                    ->where('pa.pasarela_pago_id = ?', $pasarela_id)
                    ->execute();

                $metodo->delete();
                break;
            case 'generico':
                $metodo = Doctrine_Query::create()
                    ->from('PasarelaPagoGenerica pg')
                    ->where('pg.pasarela_pago_id = ?', $pasarela_id)
                    ->execute();

                $metodo->delete();
                break;
        }

        redirect('backend/pasarela_pagos/index/');
    }

    public function editar($pasarela_id) {
        $pasarela=Doctrine::getTable('PasarelaPago')->find($pasarela_id);

        $data['pasarela'] = $pasarela;

        switch($pasarela->metodo) {
            case 'antel':
                $metodo = Doctrine_Query::create()
                    ->from('PasarelaPagoAntel pa')
                    ->where('pa.pasarela_pago_id = ?', $pasarela_id)
                    ->execute();

                $data['pasarela_metodo'] = $metodo[0];
                break;
            case 'generico':
                $metodo = Doctrine_Query::create()
                    ->from('PasarelaPagoGenerica pg')
                    ->where('pg.pasarela_pago_id = ?', $pasarela_id)
                    ->execute();

                $data['pasarela_metodo'] = $metodo[0];
                break;
        }

        $data['title'] = 'Pasarela de Pagos';
        $data['content'] = 'backend/pasarela_pagos/editar';
        $this->load->view('backend/template', $data);
    }

    public function editar_form($pasarela_id){
        $pasarela=Doctrine::getTable('PasarelaPago')->find($pasarela_id);

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('metodo', 'Metodo', 'required|min_length[1]');

        if($this->input->post('metodo') == 'antel') {
          $this->form_validation->set_rules('pasarela_metodo_antel_certificado', 'Certificado SSL', 'required');
          $this->form_validation->set_rules('pasarela_metodo_antel_clave_certificado', 'Clave privada', 'required');
        }

        if ($this->form_validation->run() == TRUE) {
            $pasarela->activo=($this->input->post('activo') == '1') ? 1 : 0;
            $pasarela->nombre=$this->input->post('nombre');
            $pasarela->metodo=$this->input->post('metodo');
            $pasarela->save();

            // -- Métodos de pagos
            switch($this->input->post('metodo')) {
                // -- Método ANTEL
                case 'antel':
                    if($this->input->post('pasarela_metodo_antel_id') != '') {
                        $pasarela_metodo=Doctrine::getTable('PasarelaPagoAntel')->find($this->input->post('pasarela_metodo_antel_id'));
                    }
                    else {
                        $pasarela_metodo=new PasarelaPagoAntel();
                    }

                    $pasarela_metodo->pasarela_pago_id=$pasarela_id;
                    $pasarela_metodo->id_tramite=$this->input->post('pasarela_metodo_antel_id_tramite');
                    $pasarela_metodo->id_organismo=$this->input->post('pasarela_metodo_antel_id_organismo');
                    $pasarela_metodo->cantidad=$this->input->post('pasarela_metodo_antel_cantidad');
                    $pasarela_metodo->tasa_1=$this->input->post('pasarela_metodo_antel_tasa_1');
                    $pasarela_metodo->tasa_2=$this->input->post('pasarela_metodo_antel_tasa_2');
                    $pasarela_metodo->tasa_3=$this->input->post('pasarela_metodo_antel_tasa_3');
                    $pasarela_metodo->operacion=$this->input->post('pasarela_metodo_antel_operacion');
                    $pasarela_metodo->vencimiento=$this->input->post('pasarela_metodo_antel_vencimiento');
                    $pasarela_metodo->codigos_desglose=$this->input->post('pasarela_metodo_antel_codigos_desglose');
                    $pasarela_metodo->montos_desglose=$this->input->post('pasarela_metodo_antel_montos_desglose');
                    $pasarela_metodo->clave_organismo=$this->input->post('pasarela_metodo_antel_clave_organismo');
                    $pasarela_metodo->clave_tramite=$this->input->post('pasarela_metodo_antel_clave_tramite');
                    $pasarela_metodo->certificado=$this->input->post('pasarela_metodo_antel_certificado');
                    $pasarela_metodo->clave_certificado=$this->input->post('pasarela_metodo_antel_clave_certificado');
                    $pasarela_metodo->pass_clave_certificado=$this->input->post('pasarela_metodo_antel_pass_clave_certificado');

                    $pasarela_metodo->tema_email_inicio=$this->input->post('pasarela_metodo_antel_tema_email_inicio');
                    $pasarela_metodo->cuerpo_email_inicio=$this->input->post('pasarela_metodo_antel_cuerpo_email_inicio');
                    $pasarela_metodo->tema_email_ok=$this->input->post('pasarela_metodo_antel_tema_email_ok');
                    $pasarela_metodo->cuerpo_email_ok=$this->input->post('pasarela_metodo_antel_cuerpo_email_ok');
                    $pasarela_metodo->tema_email_pendiente=$this->input->post('pasarela_metodo_antel_tema_email_pendiente');
                    $pasarela_metodo->cuerpo_email_pendiente=$this->input->post('pasarela_metodo_antel_cuerpo_email_pendiente');
                    $pasarela_metodo->tema_email_timeout=$this->input->post('pasarela_metodo_antel_tema_email_timeout');
                    $pasarela_metodo->cuerpo_email_timeout=$this->input->post('pasarela_metodo_antel_cuerpo_email_timeout');

                    $pasarela_metodo->save();
                    break;
                // -- Método GENERICO
                case 'generico':
                    if($this->input->post('pasarela_metodo_generico_id') != '') {
                        $pasarela_metodo=Doctrine::getTable('PasarelaPagoGenerica')->find($this->input->post('pasarela_metodo_generico_id'));
                    }
                    else {
                        $pasarela_metodo=new PasarelaPagoGenerica();
                    }

                    $pasarela_metodo->pasarela_pago_id=$pasarela_id;
                    $pasarela_metodo->codigo_operacion_soap=$this->input->post('pasarela_metodo_generico_codigo_operacion_soap');
                    $pasarela_metodo->codigo_operacion_soap_consulta=$this->input->post('pasarela_metodo_generico_codigo_operacion_soap_consulta');
                    $pasarela_metodo->variable_evaluar=$this->input->post('pasarela_metodo_generico_variable_evaluar');
                    $pasarela_metodo->variable_idsol=$this->input->post('pasarela_metodo_generico_variable_idsol');
                    $pasarela_metodo->variable_idestado=$this->input->post('pasarela_metodo_generico_variable_idestado');
                    $pasarela_metodo->url_redireccion=$this->input->post('pasarela_metodo_generico_url_redireccion');
                    $pasarela_metodo->url_ticket=$this->input->post('pasarela_metodo_generico_url_ticket');
                    $pasarela_metodo->ticket_metodo=$this->input->post('pasarela_metodo_generico_ticket_metodo');
                    $pasarela_metodo->metodo_http=$this->input->post('pasarela_metodo_generico_metodo_http');
                    $pasarela_metodo->mensaje_reimpresion_ticket=$this->input->post('pasarela_metodo_generico_mensaje_reimpresion_ticket');
                    $pasarela_metodo->tema_email_inicio=$this->input->post('pasarela_metodo_generico_tema_email_inicio');
                    $pasarela_metodo->cuerpo_email_inicio=$this->input->post('pasarela_metodo_generico_cuerpo_email_inicio');
                    $pasarela_metodo->variable_redireccion=$this->input->post('pasarela_metodo_generico_variable_redireccion');

                    if(!$this->input->post('pasarela_metodo_generico_metodo_http_variable')) {
                      $variables = '';
                    }
                    else {
                      $variables = $this->input->post('pasarela_metodo_generico_metodo_http_variable');
                    }

                    if(!$this->input->post('pasarela_metodo_generico_ticket_variables')) {
                      $variables_ticket = '';
                    }
                    else {
                      $variables_ticket = $this->input->post('pasarela_metodo_generico_ticket_variables');
                    }

                    $pasarela_metodo->variables_post = json_encode($variables);
                    $pasarela_metodo->ticket_variables = json_encode($variables_ticket);

                    $pasarela_metodo->save();
                    break;
            }

            $respuesta->validacion=TRUE;
            $respuesta->redirect=site_url('backend/pasarela_pagos/index');
        }
        else {
            $respuesta->validacion=FALSE;
            $respuesta->errores=validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function ajax_editar_modelo($pasarela_id) {
        $pasarela = Doctrine::getTable('PasarelaPago')->find($pasarela_id);

        $modelo=$this->input->post('modelo');

        $pasarela->updateModelFromJSON($modelo);
    }
}
