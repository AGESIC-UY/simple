<?php

class Pdi extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('sts');
        $this->hasColumn('policy');
        $this->hasColumn('certificado_organismo');
        $this->hasColumn('clave_organismo');
        $this->hasColumn('certificado_ssl');
        $this->hasColumn('clave_ssl');
    }

    function setUp() {
        parent::setUp();
    }
}
