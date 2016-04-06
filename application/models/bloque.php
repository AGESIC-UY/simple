<?php

class Bloque extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Formulario', array(
            'local' => 'id',
            'foreign' => 'bloque_id'
        ));
    } 
}
