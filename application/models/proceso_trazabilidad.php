<?php

class ProcesoTrazabilidad extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('proceso_id');
        $this->hasColumn('organismo_id');
        $this->hasColumn('proceso_externo_id');
        $this->hasColumn('envio_guid_automatico');
        $this->hasColumn('email_envio_guid');
    }

    function setUp() {
        parent::setUp();
    }
}
