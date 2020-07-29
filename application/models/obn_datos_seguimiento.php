<?php

class ObnDatosSeguimiento extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('obn_id');
        $this->hasColumn('etapa_id');
        $this->hasColumn('nombre');
        $this->hasColumn('valor');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Etapa', array(
            'local' => 'etapa_id',
            'foreign' => 'id'
        ));

        $this->hasOne('ObnStructure', array(
            'local' => 'obn_id',
            'foreign' => 'id'
        ));
    }


}
