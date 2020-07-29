<?php
 
class Migration_27 extends Doctrine_Migration_Base {
 
    public function up() {
        
        // crea tabla aud_obn_attributes
        $columnas = array(
            'id' => array(
                'type' => 'INT',
                'length' => 10,
            ),
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
 
        $this->createTable('aud_obn_attributes', $columnas, $opciones);
 
        
 
        //creo PK
        $pk_id = array(
            'id_aud' => array(
                'type' => 'integer',
            )
        );
 
        $this->createPrimaryKey('aud_obn_attributes', $pk_id);
        $this->changeColumn('aud_obn_attributes', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST'); 
        $this->changeColumn('aud_obn_attributes', 'id', 'INT(10)'); 
        $this->changeColumn('aud_obn_attributes', 'nombre', 'varchar(255) NOT NULL');
        $this->changeColumn('aud_obn_attributes', 'tipo', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_obn_attributes', 'multiple', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->changeColumn('aud_obn_attributes', 'clave_logica', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->changeColumn('aud_obn_attributes', 'id_obn', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('aud_obn_attributes', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
    }
 
    public function down() {
        
    }
 
}
