<?php
require_once('campo.php');
class CampoTitle extends Campo{

    public $requiere_nombre=false;
    public $requiere_datos=false;
    public $estatico=true;

    function setTableDefinition() {
        parent::setTableDefinition();

        $this->hasColumn('readonly','bool',1,array('default'=>1));
    }

    function setUp() {
        parent::setUp();
        $this->setTableName("campo");
    }

    public function setReadonly($readonly){
        $this->_set('readonly', 1);
    }

    protected function display($modo, $dato,$etapa_id) {
        if($etapa_id){
            $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
            $regla=new Regla($this->etiqueta);
            $etiqueta=$regla->getExpresionParaOutput($etapa->id);
        }else{
            $etiqueta=$this->etiqueta;
        }

        $display='<h2 data-fieldset="'.$this->fieldset.'">'.$etiqueta.'</h2>';

        return $display;
    }




}
