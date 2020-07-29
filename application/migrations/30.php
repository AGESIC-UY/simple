<?php

class Migration_30 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('dato_seguimiento', 'valor', 'MEDIUMTEXT NOT NULL');
    }

    public function down() {
        
    }

}
