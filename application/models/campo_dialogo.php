<?php

require_once('campo.php');

class CampoDialogo extends Campo {

    public $requiere_nombre=false;
    public $requiere_datos=false;
    public $requiere_validacion=false;
    public $sin_etiqueta=true;
    public $valor_default_tamano='large';
    public $dialogo = true;

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

        $tipo = $this->extra->tipo;

        switch($tipo) {
          case 'alert':
            $campo_tipo = 'warning';
            break;
          case 'success':
            $campo_tipo = 'success';
            break;
        }

        ($dato ? $valor = $dato->valor : $valor = $valor_default);
        $display  = '<div class="control-group">';
        $display .= '<div class="dialogo validacion-'. $campo_tipo .' '. ($valor == "" ? "hidden" : "") .'" data-fieldset="'.$this->fieldset.'">';
        $display .= '<div class="alert alert-'. $tipo .'" id="'.$this->id.'">' . $valor . '</div>';

        // -- Boton disparador de accion del campo
        if($this->requiere_accion == 1 && $modo != 'visualizacion') {
          $display .= ' <div class="boton-dialogo"><button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button></div>';
        }

        $display .= '</div>';
        $display .= '</div>';

        return $display;
    }

    public function setReadonly($readonly){
        $this->_set('readonly', 1);
    }

    public function backendExtraFields() {
        if(isset($this->extra->tipo)) {
          $tipo = $this->extra->tipo;
        }
        else {
          $tipo = '';
        }

        switch($this->extra->dialogo_validacion) {
          case 'on':
            $checked = 'checked';
            break;
          default:
            $checked = '';
        }

        $display  = '<label for="tipo_dialogo">Tipo de diálogo</label> ';
        $display .= '<select id="tipo_dialogo" name="extra[tipo]" value="'. $tipo .'">';
        $display .= '<option value>-- Seleccione el tipo de diálogo --</option>';
        $display .= '<option value="alert" '. ($tipo == "alert" ? "selected" : "") .'>Alerta</option>';
        $display .= '<option value="success" '. ($tipo == "success" ? "selected" : "") .'>Confirmación</option>';
        $display .= '</select>';

        return $display;
    }

    public function backendExtraValidate() {
        $CI=&get_instance();
        $CI->form_validation->set_rules('extra[tipo]', 'Tipo', 'required');
    }
}
