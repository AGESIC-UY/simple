<?php

class Pago extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('id_tramite_interno');
        $this->hasColumn('id_tramite');
        $this->hasColumn('id_etapa');
        $this->hasColumn('id_solicitud');
        $this->hasColumn('estado');
        $this->hasColumn('fecha_actualizacion');
        $this->hasColumn('pasarela');
    }

    function setUp() {
        parent::setUp();
    }
}
