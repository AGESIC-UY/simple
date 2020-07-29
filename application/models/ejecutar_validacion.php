<?php

class EjecutarValidacion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('regla');
        $this->hasColumn('instante');
        $this->hasColumn('validacion_id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('paso_id');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Validacion',array(
            'local'=>'validacion_id',
            'foreign'=>'id'
        ));

        $this->hasOne('Tarea',array(
            'local'=>'tarea_id',
            'foreign'=>'id'
        ));

        $this->hasOne('Paso',array(
            'local'=>'paso_id',
            'foreign'=>'id'
        ));
    }

    public function setPasoId($paso_id){
        if($paso_id!='')
            $this->_set ('paso_id', $paso_id);
        else
            $this->_set ('paso_id', null);
    }
}
