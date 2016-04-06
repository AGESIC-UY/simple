<?php
require_once('campo.php');

class CampoFieldset extends Campo{

    public $requiere_nombre=true;
    public $requiere_datos=false;
    public $estatico=true;
    public $etiqueta_tamano='large';

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',0,array('default'=>0));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id) {
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->valor_default);
            $valor_default=$regla->getExpresionParaOutput($etapa->id);
        }else {
            $valor_default=$this->valor_default;
        }

        $display = '<fieldset class="custom-fieldset" id="'.$this->id.'" ' . ($modo == 'visualizacion' ? 'readonly' : '') . ' name="' . $this->nombre . '">';
        $display .= '<legend><span class="custom-fieldset-legend">'. $this->etiqueta .'</span></legend>';
        $display .= '</fieldset>';

        return $display;
    }

}
