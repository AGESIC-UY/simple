<?php
require_once('campo.php');
class CampoCheckbox extends Campo {

    protected function display($modo, $dato) {
        $display  = '<div class="control-group">';
        $display .= '<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
        $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';

        $opcion = 1;
        foreach ($this->datos as $d) {
            if($this->ayuda_ampliada && $opcion == 1) {

              $display.='<label class="checkbox" for="'.$this->id.'_'.$d->valor.'">';

              $display.='<input id="'.$this->id.'_'.$d->valor.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="checkbox" name="' . $this->nombre . '[]" value="' . $d->valor . '" ' . ($dato && $dato->valor && in_array($d->valor, $dato->valor) ? 'checked' : '') . ' />';

              $display.= $d->etiqueta;

              $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';
            }
            else {
              $display.='<label class="checkbox" for="'.$this->id.'_'.$d->valor.'">';
              $display.='<input id="'.$this->id.'_'.$d->valor.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="checkbox" name="' . $this->nombre . '[]" value="' . $d->valor . '" ' . ($dato && $dato->valor && in_array($d->valor, $dato->valor) ? 'checked' : '') . ' />';

              $display.= $d->etiqueta;
            }

            $opcion++;

            $display .= '</label>';
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
