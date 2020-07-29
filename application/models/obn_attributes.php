<?php

class ObnAttributes extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('id_obn');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('clave_logica');
        $this->hasColumn('multiple');
        $this->hasColumn('valores');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('ObnStructure', array(
            'local' => 'id_obn',
            'foreign' => 'id'
        ));
    } 
}
