<?php
 
class Migration_24 extends Doctrine_Migration_Base {
 
    public function up() {
        // crea tabla obn_attributes
        $columnas = array(
            'id_obn' => array(
                'type' => 'INT',
                'length' => 10,              
            ),
            'nombre' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'tipo' => array(
                'type' => 'varchar',
                'length' => 50,
            ),
            'clave_logica' => array(
                'type' => 'tinyint',
                'length' => 1,
            ),
            'multiple' => array(
                'type' => 'tinyint',
                'length' => 1,
            ),
            'valores' => array(
                'type' => 'varchar',
                'length' => 255,
            )
        );
 
        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );
 
        $this->createTable('obn_attributes', $columnas, $opciones);
 
        
 
        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
            )
        );
 
        $this->createPrimaryKey('obn_attributes', $pk_id);
        $this->changeColumn('obn_attributes', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST'); 
        $this->changeColumn('obn_attributes', 'nombre', 'varchar(255) NOT NULL');
        $this->changeColumn('obn_attributes', 'tipo', 'varchar(50) NOT NULL');
        $this->changeColumn('obn_attributes', 'multiple', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->changeColumn('obn_attributes', 'clave_logica', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->changeColumn('obn_attributes', 'id_obn', 'INT(10) UNSIGNED NOT NULL');
        $this->addIndex('obn_attributes', 'obn_nombre', array(
            'fields'=>array('id_obn','nombre'),'type'=>'unique'
        ));
        
         //creo FK
        $fk_obn = array(
            'local' => 'id_obn',
            'foreign' => 'id',
            'foreignTable' => 'obn_structure',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );
       
        $this->createForeignKey('obn_attributes', 'fk_obn_attributes', $fk_obn);

    }
 
    public function down() {
        
    }
 
}
