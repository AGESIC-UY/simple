<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pasarela_pagos extends MY_BackendController {

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
        $data['pasarelas'] = Doctrine_Query::create()
            ->from('PasarelaPago p')
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
        }

        $data['title'] = 'Pasarela de Pagos';
        $data['content'] = 'backend/pasarela_pagos/editar';
        $this->load->view('backend/template', $data);
    }

    public function editar_form($pasarela_id){
        $pasarela=Doctrine::getTable('PasarelaPago')->find($pasarela_id);

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('metodo', 'Metodo', 'required|min_length[1]');

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
                    $pasarela_metodo->cantidad=$this->input->post('pasarela_metodo_antel_cantidad');
                    $pasarela_metodo->tasa_1=$this->input->post('pasarela_metodo_antel_tasa_1');
                    $pasarela_metodo->tasa_2=$this->input->post('pasarela_metodo_antel_tasa_2');
                    $pasarela_metodo->tasa_3=$this->input->post('pasarela_metodo_antel_tasa_3');
                    $pasarela_metodo->operacion=$this->input->post('pasarela_metodo_antel_operacion');
                    $pasarela_metodo->vencimiento=$this->input->post('pasarela_metodo_antel_vencimiento');
                    $pasarela_metodo->codigos_desglose=$this->input->post('pasarela_metodo_antel_codigos_desglose');
                    $pasarela_metodo->montos_desglose=$this->input->post('pasarela_metodo_antel_montos_desglose');
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
