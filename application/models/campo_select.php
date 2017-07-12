<?php
require_once('campo.php');
class CampoSelect extends Campo {

    protected function display($modo, $dato) {
        $valor_default= $this->valor_default;

        //show_error(var_dump($dato));
        $display = '<div class="control-group">';
        $display.= '<label class="control-label" for="'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (in_array('required', $this->validacion) ? '*:' : ':') . '</label>';
        $display.= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.='<select id="'.$this->id.'" name="' . $this->nombre . '" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' data-modo="'.$modo.'">';
        $display.='<option value="">Seleccionar</option>';

        if ($dato){
          if($this->datos) foreach ($this->datos as $d) {
              $display.='<option value="' . $d->valor . '" ' . ($dato && $d->valor == $dato->valor ? 'selected' : '') . '>' . $d->etiqueta . '</option>';
          }
        }else{
          if($this->datos) foreach ($this->datos as $d) {
              $display.='<option value="' . $d->valor . '" ' . ($valor_default && $d->valor == $valor_default ? 'selected' : '') . '>' . $d->etiqueta . '</option>';
          }
        }

        $display.='</select>';

        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';

        if($this->ayuda_ampliada)
          $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';

        $display.='</div>';
        $display.='</div>';

        return $display;
    }

    public function backendExtraFields(){
    }
}
