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
        $display .= '<input id="'.$this->id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' type="text" name="' . $this->nombre . '" value="' . ($dato?htmlspecialchars($dato->valor):htmlspecialchars($valor_default)) . '" data-modo="'.$modo.'" />';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';

        return $display;
    }
}
