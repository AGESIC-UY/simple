<?php
require_once('campo.php');
class CampoSelect extends Campo {

    public $reporte = true;
    public $asociar_obn = true;

    protected function display($modo, $dato,$etapa_id) {
        $regla = new Regla($this->valor_default);
        $valor_default = $regla->getExpresionParaOutput($etapa_id);

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
        // -- Boton disparador de accion del campo
        if($this->requiere_accion == 1 && $modo != 'visualizacion') {
          $display .= ' <button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button>';
        }

        if($this->ayuda_ampliada) {
          $display .= '<span><button type="button" class="tooltip_help_click" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';
          $display .= '<span class="hidden tooltip_help_line">'. strip_tags($this->ayuda_ampliada) .'</span></span>';
        }

        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';


        $display.='</div>';
        $display.='</div>';

        return $display;
    }

    public function backendExtraFields(){
    }
}
