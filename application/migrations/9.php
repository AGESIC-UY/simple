<?php

class Migration_9 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('documento', 'validez_habiles', 'boolean', null, array('notnull'=>1));
        $this->addColumn('file', 'validez_habiles', 'boolean', null, array('notnull'=>1));
    }


    public function down() {
        $this->removeColumn('documento', 'validez_habiles');
        $this->removeColumn('file', 'validez_habiles');
    }

}