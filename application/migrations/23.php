<?php

class Migration_23 extends Doctrine_Migration_Base {

    public function up() {
        // crea tabla obn_queries
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
            'consulta' => array(
                'type' => 'LONGTEXT'
            ),
            'consulta_sql' => array(
                'type' => 'LONGTEXT'
            ),
        );

        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );

        $this->createTable('obn_queries', $columnas, $opciones);

        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
            )
        );

        $this->createPrimaryKey('obn_queries', $pk_id);
        $this->changeColumn('obn_queries', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('obn_queries', 'id_obn', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('obn_queries', 'nombre', 'varchar(255) NOT NULL');
        $this->changeColumn('obn_queries', 'consulta', 'LONGTEXT NOT NULL');
        $this->changeColumn('obn_queries', 'consulta_sql', 'LONGTEXT NOT NULL');
        $this->changeColumn('obn_queries', 'tipo', "ENUM('query','count') NOT NULL");
        $this->addIndex('obn_queries', 'obn_nombre', array(
            'fields' => array('id_obn', 'nombre'), 'type' => 'unique'
        ));

        //creo FK
        $fk_obn = array(
            'local' => 'id_obn',
            'foreign' => 'id',
            'foreignTable' => 'obn_structure',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );
        $this->createForeignKey('obn_queries', 'fk_obn_queries', $fk_obn);
       
        
    }

    public function down() {
        
    }

}
