<?php
require_once('campo.php');

class CampoBloque extends Campo{

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
          $bloque_id=$regla->getExpresionParaOutput($etapa->id);
        }
        else {
            $bloque_id=$this->valor_default;
        }
    }
}
