<?php

class DatoSeguimiento extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('valor');
        $this->hasColumn('etapa_id');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Etapa',array(
            'local'=>'etapa_id',
            'foreign'=>'id'
        ));
    }

    public function setValor($valor){
        if(is_string($valor)){
            //Si es que no es un JSON lo que recibimos, lo codificamos nosotros.
            $val = json_decode($valor);
            if (!is_array($val) && !is_object($val))
                $valor = json_encode($valor);
        }else{
            $valor = json_encode($valor);
        }

        $this->_set('valor', $valor);
    }

    public function getValor(){
        return json_decode($this->_get('valor'));
    }

    public function toPublicArray(){
        $publicArray=array(
            $this->nombre=>$this->valor
        );

        return $publicArray;
    }
}
