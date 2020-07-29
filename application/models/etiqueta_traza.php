<?php

class EtiquetaTraza extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('etiqueta');
        $this->hasColumn('descripcion');
    }

    function setUp() {
        parent::setUp();
    }
}
