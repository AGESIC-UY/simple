<?php

class Parametro extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('clave');
        $this->hasColumn('valor');
    }

    function setUp() {
      parent::setUp();
    }
}
