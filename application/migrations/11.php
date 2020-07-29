<?php

class Migration_11 extends Doctrine_Migration_Base {

    public function up() {

        //creo FK EN etapa_historial_ejecuciones
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

        $this->createForeignKey('etapa_historial_ejecuciones', 'fk_etapa_id', $fk_etapa);
        $this->createForeignKey('etapa_historial_ejecuciones', 'fk_usuario_id', $fk_usuario);

        //creo FK evento_pago
        $fk_accion = array(
            'local' => 'accion_id',
            'foreign' => 'id',
            'foreignTable' => 'accion',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );
        $fk_accion_ejec = array(
            'local' => 'accion_ejecutar_id',
            'foreign' => 'id',
            'foreignTable' => 'accion',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );

        $this->createForeignKey('evento_pago', 'fk_accion_id', $fk_accion);
        $this->createForeignKey('evento_pago', 'fk_accion_ejec_id', $fk_accion_ejec);
        
        $this->dropForeignKey('etapa_historial_ejecuciones', 'fk_etapa');
        $this->dropForeignKey('etapa_historial_ejecuciones', 'fk_usuario');
        $this->dropForeignKey('evento_pago', 'fk_accion');
        $this->dropForeignKey('evento_pago', 'fk_accion_ejec');
        
        $this->changeColumn('pago', 'fecha_actualizacion', 'varchar(19)');
    }

    public function down() {
        
    }

}
