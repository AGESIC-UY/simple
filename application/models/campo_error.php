<?php

require_once('campo.php');

class CampoError extends Campo{

    public $requiere_nombre=true;
    public $requiere_datos=false;

    protected function display($modo, $dato,$etapa_id) {
        if($etapa_id) {
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
            $valor_default=$this->valor_default;
        }

        ($dato ? $valor = htmlspecialchars($dato->valor) : $valor = htmlspecialchars($valor_default));
        $display  = '<div class="control-group">';
        $display .= '<div class="campo_error '. ($valor == "" ? "hidden" : "") .'" data-fieldset="'.$this->fieldset.'">';
        $display .= '<span id="'.$this->id.'">' . ($dato?htmlspecialchars($dato->valor):htmlspecialchars($valor_default)) . '</span>';
        $display .= '</div>';
        $display .= '</div>';

        return $display;
    }
}
