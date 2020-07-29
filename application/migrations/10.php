<?php

class Migration_10 extends Doctrine_Migration_Base {

    public function up() {

        $this->addIndex('etapa', 'vencimiento_at', array(
            'fields' => array('vencimiento_at')
        ));

        $this->addColumn('pago', 'usuario', 'varchar', null, array('notnull' => 1, 'default' => 'Sistema'));

               //creo FK
        $fk_etapa = array(
            'local' => 'etapa_id',
            'foreign' => 'id',
            'foreignTable' => 'etapa',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );

        $fk_usuario = array(
            'local' => 'usuario_id',
            'foreign' => 'id',
            'foreignTable' => 'usuario',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );

        $this->createForeignKey('etapa_historial_ejecuciones', 'fk_etapa', $fk_etapa);
        $this->createForeignKey('etapa_historial_ejecuciones', 'fk_usuario', $fk_usuario);
    }

    public function down() {
        $this->removeColumn('pago', 'usuario');
        $this->removeIndex('etapa_historial_ejecuciones', 'fk_etapa');
        $this->removeIndex('etapa_historial_ejecuciones', 'fk_usuario');
        $this->dropForeignKey('etapa_historial_ejecuciones', 'fk_etapa');
        $this->dropForeignKey('etapa_historial_ejecuciones', 'fk_usuario');
    }

}
