<?php

class Paso extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('orden');
        $this->hasColumn('modo');
        $this->hasColumn('regla');
        $this->hasColumn('nombre');
        $this->hasColumn('formulario_id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('generar_pdf');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Formulario', array(
            'local' => 'formulario_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Tarea', array(
            'local' => 'tarea_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Evento as Eventos', array(
            'local' => 'id',
            'foreign' => 'paso_id'
        ));
    }

    //Un paso se considera como readonly si es que esta en modo visualizacion o todos sus campos son readonly
    public function getReadonly(){
        if($this->modo=='visualizacion')
            return true;

        foreach($this->Formulario->Campos as $c)
            if(!$c->readonly)
                return false;

        return true;
    }

}
