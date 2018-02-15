<?php
require_once('campo.php');
class CampoCheckbox extends Campo {

  public $reporte = true;

    protected function display($modo, $dato) {
        $display  = '<div class="control-group">';
        $display .= '<span class="control-label" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</span>';
        $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';

        $opcion = 1;
        foreach ($this->datos as $d) {
            if($this->ayuda_ampliada && $opcion == 1) {

              $display .= '<span><button type="button" class="tooltip_help_click_radio" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button></span>';
              $display.='<label class="checkbox" for="'.$this->id.'_'.$d->valor.'">';
              $display.='<input id="'.$this->id.'_'.$d->valor.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="checkbox" name="' . $this->nombre . '[]" value="' . $d->valor . '" ' . ($dato && $dato->valor && in_array($d->valor, $dato->valor) ? 'checked' : '') . ' />';
              $display.= $d->etiqueta;
            }
            else {
              $display.='<label   class="checkbox" for="'.$this->id.'_'.$d->valor.'">';
              $display.='<input id="'.$this->id.'_'.$d->valor.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="checkbox" name="' . $this->nombre . '[]" value="' . $d->valor . '" ' . ($dato && $dato->valor && in_array($d->valor, $dato->valor) ? 'checked' : '') . ' />';

              $display.= $d->etiqueta;
            }

            $opcion++;

            $display .= '</label>';
        }
        // -- Boton disparador de accion del campo
        if($this->requiere_accion == 1 && $modo != 'visualizacion') {
          $display .= ' <button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button>';
        }

        if ($this->ayuda_ampliada){
            $display .= '<span class="hidden tooltip_help_line">'. strip_tags($this->ayuda_ampliada) .'</span>';
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
