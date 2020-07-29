<?php
require_once('campo.php');
class CampoParagraph extends Campo{

    public $requiere_nombre=false;
    public $requiere_datos=false;
    public $estatico=true;
    public $etiqueta_tamano='xxlarge';

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',1,array('default'=>1));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    protected function display($modo, $dato, $etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->etiqueta);
            $etiqueta=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
            $etiqueta=$this->etiqueta;
        }

        $display = '<div><div data-fieldset="'.$this->fieldset.'"><p>'.$etiqueta.'</p></div></div>';

        // -- Boton disparador de accion del campo
        if($this->requiere_accion == 1 && $modo != 'visualizacion') {
          $display .= ' <button type="button" class="btn requiere_accion_disparador" data-campo="'. $this->id .'">'.$this->requiere_accion_boton.'</button>';
        }

        return $display;
    }

    public function setReadonly($readonly){
        $this->_set('readonly', 1);
    }


}
