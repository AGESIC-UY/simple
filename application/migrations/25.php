<?php
 
class Migration_25 extends Doctrine_Migration_Base {
 
    public function up() {
        //migraciones necesarias para la versión 2.1
        
        // crea tabla aud_obn_structure
        $columnas = array(
            'id' => array(
                'type' => 'INT',
                'length' => 10,
            ),
            'descripcion' => array(
                'type' => 'varchar',
                'length' => 255,              
            ),
            'identificador' => array(
                'type' => 'varchar',
                'length' => 255,
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
            ),
            'usuario_aud' => array(
                'type' => 'varchar',
                'length' => 50,
            ),
            'tipo_operacion_aud' => array(
                'type' => 'LONGTEXT'
            ),
            'fecha_aud' => array(
                'type' => 'DATETIME'
            ),
        );
 
        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );
 
        $this->createTable('aud_obn_structure', $columnas, $opciones);
 
        
 
        //creo PK
        $pk_id = array(
            'id_aud' => array(
                'type' => 'integer',
            )
        );
 
        $this->createPrimaryKey('aud_obn_structure', $pk_id);
        $this->changeColumn('aud_obn_structure', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST'); 
        $this->changeColumn('aud_obn_structure', 'id', 'INT(10)'); 
        $this->changeColumn('aud_obn_structure', 'identificador', 'varchar(255) NOT NULL');
        $this->changeColumn('aud_obn_structure', 'descripcion', 'varchar(255) NOT NULL');
        $this->changeColumn('aud_obn_structure', 'cuenta_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('aud_obn_structure', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
       
    }
 
    public function down() {
        
    }
 
}
