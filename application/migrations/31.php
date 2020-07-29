<?php

class Migration_31 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('cuenta', 'traza_involucrado', 'tinyint(1) NOT NULL DEFAULT 1');
        $this->addColumn('proceso_trazabilidad', 'traza_involucrado', 'tinyint(1) NOT NULL DEFAULT 1');
    }

    public function down() {
        
    }

}
