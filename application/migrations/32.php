<?php

class Migration_32 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('documento', 'imagenes', 'LONGTEXT DEFAULT NULL');
        $this->addColumn('aud_documento', 'imagenes', 'LONGTEXT DEFAULT NULL');
    }

    public function down() {
        
    }

}
