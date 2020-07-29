<?php

class Migration_26 extends Doctrine_Migration_Base {

    public function up() {
       
        //tabla aud_obn_queries
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
            'consulta' => array(
                'type' => 'LONGTEXT'
            ),
            'consulta_sql' => array(
                'type' => 'LONGTEXT'
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

        $this->createTable('aud_obn_queries', $columnas, $opciones);

        //creo PK
        $pk_id = array(
            'id_aud' => array(
                'type' => 'integer',
            )
        );

        $this->createPrimaryKey('aud_obn_queries', $pk_id);
        $this->changeColumn('aud_obn_queries', 'id_aud', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('aud_obn_queries', 'id', 'INT(10)');
        $this->changeColumn('aud_obn_queries', 'id_obn', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('aud_obn_queries', 'nombre', 'varchar(255) NOT NULL');
        $this->changeColumn('aud_obn_queries', 'consulta', 'LONGTEXT NOT NULL');
        $this->changeColumn('aud_obn_queries', 'consulta_sql', 'LONGTEXT NOT NULL');
        $this->changeColumn('aud_obn_queries', 'tipo', "ENUM('query','count') NOT NULL");
        $this->changeColumn('aud_obn_queries', 'tipo_operacion_aud', "ENUM('update','delete','insert')");
        
        
    }

    public function down() {
        
    }

}
