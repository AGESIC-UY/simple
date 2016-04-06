<?php

class Migration_14 extends Doctrine_Migration_Base {

    public function up() {
        $this->addColumn('tarea', 'previsualizacion', 'text', null, array('notnull'=>1, 'default' => ''));
    }


    public function down() {
        $this->removeColumn('tarea', 'previsualizacion');
    }

}