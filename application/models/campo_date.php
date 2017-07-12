<?php
require_once('campo.php');
class CampoDate extends Campo{

    public $requiere_datos=false;

    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=$this->valor_default;
        }
        $display  = '<div class="control-group">';
        $display.='<label class="control-label" for="'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ':' : '*:') . '</label>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.='<input id="'.$this->id.'" class="datepicker" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="text" name="'.$this->nombre.'" value="' . ($dato && $dato->valor?date('d-m-Y',strtotime($dato->valor)):$valor_default) . '" placeholder="dd-mm-aaaa" />';

        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';

        if($this->ayuda_ampliada)
          $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';

        $display.='</div>';
        $display.='</div>';

        return $display;
    }
}
