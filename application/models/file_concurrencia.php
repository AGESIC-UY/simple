<?php

class FileConcurrencia extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('filename');
    }

    function setUp() {
        parent::setUp();
    }
}
