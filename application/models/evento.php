<?php

class Evento extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('regla');
        $this->hasColumn('instante');
        $this->hasColumn('accion_id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('paso_id');
        $this->hasColumn('instanciar_api');
        $this->hasColumn('traza');
        $this->hasColumn('tipo_registro_traza');
        $this->hasColumn('descripcion_traza');
        $this->hasColumn('etiqueta_traza');
        $this->hasColumn('visible_traza');
        $this->hasColumn('descripcion_error_soap');
        $this->hasColumn('variable_error_soap');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Accion',array(
            'local'=>'accion_id',
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
