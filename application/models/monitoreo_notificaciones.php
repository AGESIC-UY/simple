<?php

class MonitoreoNotificaciones extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('email');
    }

    function setUp() {
      parent::setUp();
    }
}
