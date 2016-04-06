<?php

class Migration_13 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('tarea', 'paso_confirmacion', 'boolean', null, array('notnull'=>1, 'default' => 1));
    }


    public function down() {
        $this->removeColumn('tarea', 'paso_confirmacion');
    }

}