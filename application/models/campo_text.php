<?php
require_once('campo.php');
class CampoText extends Campo{

    public $requiere_datos=false;

    protected function display($modo, $dato,$etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=$this->valor_default;
        }
        $display  = '<div class="control-group">';
        $display .= '<label class="control-label" for="'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ':' : '*:') . '</label>';
        $display .= '<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display .= '<input id="'.$this->id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="text" name="' . $this->nombre . '" value="' . ($dato?htmlspecialchars($dato->valor):htmlspecialchars($valor_default)) . '" data-modo="'.$modo.'" autocomplete="off" />';

        if($this->documento_tramite) {
          $display .= '<input class="hidden" title="documento_identidad_inicial" type="text" name="documento_tramite_inicial__e'.$etapa->id.'" value="'.$this->nombre.'" />';
        }

        if($this->email_tramite) {
          $display .= '<input class="hidden" type="text" name="email_tramite_inicial__e'.$etapa->id.'" value="'.$this->nombre.'" />';
        }

        if($this->ayuda_ampliada)
          $display .= '<button title="'. strip_tags($this->ayuda_ampliada) .'" class="tooltip_help" onclick="return false;"><span class="icn icn-circle-help"></span><span class="hide-read">Ayuda</span></button>';

        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';

        $display.='</div>';
        $display.='</div>';

        return $display;
    }
}
