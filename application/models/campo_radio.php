<?php
require_once('campo.php');
class CampoRadio extends Campo {

    protected function display($modo, $dato) {
        $display = '<div class="control-group">';
        $display.= '<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ' (Opcional):') . '</span>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
        foreach ($this->datos as $d) {
            $display.='<label class="radio" for="'.$d->valor.'">';
            $display.='<input id="'.$d->valor.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="radio" name="' . $this->nombre . '" value="' . $d->valor . '" ' . ($dato && $d->valor == $dato->valor ? 'checked' : '') . ' />';
            $display.=$d->etiqueta . '</label>';
        }
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';
        return $display;
    }

    public function backendExtraValidate(){
        $CI=&get_instance();
        $CI->form_validation->set_rules('datos','Datos','required');
    }

}
