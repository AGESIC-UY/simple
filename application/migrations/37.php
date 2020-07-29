<?php

class Migration_37 extends Doctrine_Migration_Base {

    public function up() {

        // crea tabla file_concurrencia
        $columnas = array(       
            'filename' => array(
                'type' => 'INT',
                'length' => 10,
                'notnull'  => 1,
            ),
        );

        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );

        $this->createTable('file_concurrencia', $columnas, $opciones);



        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
            )
        );

        $this->createPrimaryKey('file_concurrencia', $pk_id);
        $this->changeColumn('file_concurrencia', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('file_concurrencia', 'filename', 'INT(10) NOT NULL');
        
        $definition = array(
        'fields' => array(
            'filename' => array(
                'type' => 'INT',
                'length' => 10,
                'notnull'  => 1,
            )
        ),
        'unique' => true
    );

    $this->createConstraint( 'file_concurrencia', 'filename', $definition );
    }

    public function down() {
        
    }

}
