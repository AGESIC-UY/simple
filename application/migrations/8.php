<?php
 
class Migration_8 extends Doctrine_Migration_Base {
 
    public function up() {
        //migraciones necesarias para la versión 1.5
        // crea tabla evento_pago
        $columnas = array(
            'regla' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'instante' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'traza' => array(
                'type' => 'tinyint',
                'length' => 1,
            ),
            'tipo_registro_traza' => array(
                'type' => 'int',
                'length' => 2,
            ),
            'descripcion_traza' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'descripcion_error_soap' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'variable_error_soap' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'accion_id' => array(
                'type' => 'INT',
                'length' => 10,
                'null' => TRUE,
            ),
            'accion_ejecutar_id' => array(
                'type' => 'INT',
                'length' => 10,
                'null' => TRUE,
            )
        );
 
        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );
 
        $this->createTable('evento_pago', $columnas, $opciones);
 
        $this->changeColumn('evento_pago', 'accion_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('evento_pago', 'tipo_registro_traza', 'INT(2)');
 
        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
            )
        );
 
        $this->createPrimaryKey('evento_pago', $pk_id);
        $this->changeColumn('evento_pago', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('evento_pago', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
 
        //creo FK
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
        
 
        $this->createForeignKey('evento_pago', 'fk_accion', $fk_accion);
        $this->createForeignKey('evento_pago', 'fk_accion_ejec', $fk_accion_ejec);
    }
 
    public function down() {
        
    }
 
}
