<?php
 
class Migration_22 extends Doctrine_Migration_Base {
 
    public function up() {
        //migraciones necesarias para la versión 2.1
        // crea tabla obn_structure
        $columnas = array(
            'descripcion' => array(
                'type' => 'varchar',
                'length' => 255,              
            ),
            'identificador' => array(
                'type' => 'varchar',
                'length' => 255,
                'unique'=>true,
            ),
            'json' => array(
                'type' => 'LONGTEXT'
            ),
            'id_tabla_interna' => array(
                'type' => 'varchar',
                'length' => 50,
            ),
            'cuenta_id' => array(
                'type' => 'INT',
                'length' => 10,
            )
        );
 
        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );
 
        $this->createTable('obn_structure', $columnas, $opciones);
 
        
 
        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
            )
        );
 
        $this->createPrimaryKey('obn_structure', $pk_id);
        $this->changeColumn('obn_structure', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST'); 
        $this->changeColumn('obn_structure', 'identificador', 'varchar(255) NOT NULL');
        $this->changeColumn('obn_structure', 'descripcion', 'varchar(255) NOT NULL');
        $this->changeColumn('obn_structure', 'cuenta_id', 'INT(10) UNSIGNED NOT NULL');
        
        $fk_obn = array(
            'local' => 'cuenta_id',
            'foreign' => 'id',
            'foreignTable' => 'cuenta',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );
        $this->createForeignKey('obn_structure', 'fk_obn_cuenta', $fk_obn);
       
    }
 
    public function down() {
        
    }
 
}
