<?php
require_once('campo.php');

class CampoEstadoPago extends Campo {

    public $requiere_nombre=false;
    public $requiere_datos=false;
    public $estatico=true;

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

      $variable_tramite = new Regla('@@id_tramite_pasarela_pagos');
      $id_tramite = $variable_tramite->getExpresionParaOutput($etapa->id);

      $display = '<div class="no-margin-box">';
      $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
      $display .= '<div class="mensaje_estado_pago hidden"></div>';
      $display .= '<div class="well text-center">';
      $display .= '<input name="IdSol" value="'. $valor_default .'" type="hidden" />';
      $display .= '<input name="IdTramite" value="'. $id_tramite .'" type="hidden" />';
      $display .= '<input name="IdEtapa" value="'. $etapa_id .'" type="hidden" />';
      $display .= '<button class="btn btn-primary consulta_estado_pago">'. $this->etiqueta .'</button>';
      $display .= '</div>';
      $display .= '</div>';
      $display .= '</div>';

      return $display;
    }

    public function backendExtraValidate() {
      $CI=&get_instance();
      $CI->form_validation->set_rules('valor_default', 'Valor_default', 'required');
    }

    public function formValidate($etapa_id) {
      $variable_estado = new Regla('@@estado_solicitud_pago');
      $estado = $variable_estado->getExpresionParaOutput($etapa_id);
      
      switch($estado) {
        case 'realizado':
          return true;
          break;
        default:
          return 'error_estado_pago';
      }
    }
}
