<?php

class ObnQueries extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('id_obn');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('consulta');
        $this->hasColumn('consulta_sql');
    }

    function setUp() {
        parent::setUp();
        
        $this->hasOne('ObnStructure', array(
            'local' => 'id_obn',
            'foreign' => 'id'
        ));
    }

}
