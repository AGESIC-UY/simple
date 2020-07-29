<?php

class Migration_16 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();

        //simple 2.0
        //tabla proceso
        $q->execute("UPDATE proceso p SET p.root=p.id WHERE p.root IS NULL");
        $q->execute("UPDATE tarea AS t SET t.trazabilidad_id_oficina = NULL where t.trazabilidad_nombre_oficina = t.trazabilidad_id_oficina");


        //tabla Tarea
        $this->addColumn('tarea', 'id_x_tarea', 'varchar(8) default ""');

        $this->addIndex('tarea', 'identificador_x_tarea', array(
            'fields' => array('id_x_tarea', 'proceso_id')
        ));
        
        //tabla usuario_backend
        $this->addColumn('usuario_backend', 'seg_alc_auditor', 'boolean default "0"');        
    }

    public function down() {
        $this->removeIndex('tarea', 'identificador_x_tarea_idx');
    }

}
