<?php

class ProcesoTrazabilidad extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('proceso_id');
        $this->hasColumn('organismo_id');
        $this->hasColumn('proceso_externo_id');
    }

    function setUp() {
        parent::setUp();
    }
}
