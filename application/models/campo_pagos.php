<?php
require_once('campo.php');

class CampoPagos extends Campo {

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=true;
    public $etiqueta_tamano='xxlarge';

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',1,array('default'=>1));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato, $etapa_id) {
      if($etapa_id) {
          $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
          $regla=new Regla($this->valor_default);
          $valor_default=$regla->getExpresionParaOutput($etapa->id);
      }
      else {
          $valor_default=$this->valor_default;
      }

      foreach($this->Formulario->Proceso->Acciones as $accion) {
        if($accion->id == $valor_default) {
          $pasarela = $accion->extra;
        }
      }

      // ID tramite
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->id_tramite, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $id_tramite = $dato->valor;
      }
      else {
        $id_tramite = $pasarela->id_tramite;
      }

      //  tasa 1
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->tasa_1, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $tasa_1 = $dato->valor;
      }
      else {
        $tasa_1 = $pasarela->tasa_1;
      }

      //  tasa 2
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->tasa_2, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $tasa_2 = $dato->valor;
      }
      else {
        $tasa_2 = $pasarela->tasa_2;
      }

      //  tasa 3
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->tasa_3, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $tasa_3 = $dato->valor;
      }
      else {
        $tasa_3 = $pasarela->tasa_3;
      }

      // vencimiento
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->vencimiento, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $vencimiento = $dato->valor;
      }
      else {
        $vencimiento = $pasarela->vencimiento;
      }

      $fecha = str_replace('/', '', $vencimiento.'0000');
      $fecha = strtotime($fecha);
      $fecha_vencimiento = date("YmdHi", $fecha);

      //  codigos desglose
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->codigos_desglose, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $codigos_desglose = $dato->valor;
      }
      else {
        $codigos_desglose = $pasarela->codigos_desglose;
      }

      //  montos desglose
      preg_match("/^(@@)([a-zA-Z0-9_-]*)$/", $pasarela->montos_desglose, $variable_encontrada);
      if($variable_encontrada) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace("@@", "", $variable_encontrada[0]), $etapa_id);
        $montos_desglose = $dato->valor;
      }
      else {
        $montos_desglose = $pasarela->montos_desglose;
      }

      $id_sol =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('id_sol_pasarela_pagos', $etapa_id);
      $token =  Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('token_pasarela_pagos', $etapa_id);

      $display = '<div class="no-margin-box">';
      $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
      $display .= '<div class="well text-center">';
      $display .= '<div data-action="'. POST_PASARELA_PAGO .'" id="form_pasarela_pago">';

      $display .= '<input name="Token" value="'. (isset($token->valor) ? $token->valor : '') .'" type="hidden" />';

      $display .= '<p>'. $this->etiqueta .'</p>';
      $display .= '<input type="submit" value="Continuar" class="btn btn-primary" />';
      $display .= '</div>';
      $display .= '</div>';
      $display .= '</div>';
      $display .= '</div>';

      if(isset($etapa)) {
        preg_match('/('. $etapa->id .')\/([0-9]*)/', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $match);

        if($match) {
          $secuencia = (int)$match[2] + 1;

          $url_vuelta = site_url('etapas/ejecutar/' . $etapa->id . '/' . $secuencia);

          $CI=&get_instance();
          $CI->load->helper('cookies_helper');
          set_cookie('simple_bpm_gwp_redirect', base64_encode($url_vuelta), 0, '/', HOST_SISTEMA_DOMINIO);
        }
      }

      return $display;
    }

    public function backendExtraValidate(){
      $CI=&get_instance();
      $CI->form_validation->set_rules('valor_default', 'Valor_default', 'required');
    }
}
