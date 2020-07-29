<?php

class Migration_17 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();

        $q->execute("UPDATE tarea t SET t.id_x_tarea= (SUBSTRING(MD5(RAND()) FROM 1 FOR 8)) WHERE t.id_x_tarea ='' ");
    }

    public function down() {
        $this->removeIndex('tarea', 'identificador_x_tarea_idx');
    }

}
