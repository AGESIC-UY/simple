<?php
require_once('campo.php');
class CampoSelect extends Campo {

    protected function display($modo, $dato) {
        $valor_default=json_decode($this->valor_default);

        $display = '<div class="control-group">';
        $display.= '<label class="control-label" for="'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</label>';
        $display.= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.='<select id="'.$this->id.'" name="' . $this->nombre . '" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'">';
        $display.='<option value="">Seleccionar</option>';
        if($this->datos) foreach ($this->datos as $d) {
            $display.='<option value="' . $d->valor . '" ' . ($dato && $d->valor == $dato->valor ? 'selected' : '') . '>' . $d->etiqueta . '</option>';
        }
        $display.='</select>';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';

        return $display;
    }

    public function backendExtraFields(){
    }
}
