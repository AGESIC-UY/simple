<?php
require_once('campo.php');
class CampoTextArea extends Campo{

    public $requiere_datos=false;

    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $valor_default=$this->valor_default;
        }

        $display = '<div class="control-group">';
        $display.='<label class="control-label" for="'.$this->id.'" data-fieldset="'.$this->fieldset.'">' . $this->etiqueta . (!in_array('required', $this->validacion) ? ' (Opcional):' : '*:') . '</label>';
        $display.='<div class="controls" data-fieldset="'.$this->fieldset.'">';
        $display.='<textarea id="'.$this->id.'" rows="5" class="input-xxlarge" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' name="' . $this->nombre . '">' . ($dato?htmlspecialchars($dato->valor):htmlspecialchars($valor_default)) . '</textarea>';
        if($this->ayuda)
            $display.='<span class="help-block">'.$this->ayuda.'</span>';
        $display.='</div>';
        $display.='</div>';

        return $display;
    }

}
