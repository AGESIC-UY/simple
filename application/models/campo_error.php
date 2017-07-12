<?php

require_once('campo.php');

class CampoError extends Campo{

    public $requiere_nombre=false;
    public $requiere_datos=false;
    public $requiere_validacion=false;
    public $sin_etiqueta=true;

    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id) {
          $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
          $regla=new Regla($this->valor_default);
          $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
            $valor_default=$this->valor_default;
        }

        $this->extra->dialogo_validacion = 'on';

        ($dato ? $valor = htmlspecialchars($dato->valor) : $valor = htmlspecialchars($valor_default));
        $display  = '<div class="control-group">';
        $display .= '<div class="campo_error validacion-error '. ($valor == "" ? "hidden" : "") .'" data-fieldset="'.$this->fieldset.'">';
        $display .= '<span class="alert alert-error" id="'.$this->id.'">' . ($dato?$dato->valor:$valor_default) . '</span>';
        $display .= '</div>';
        $display .= '</div>';

        return $display;
    }

    public function setReadonly($readonly){
        $this->_set('readonly', 1);
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }
}
