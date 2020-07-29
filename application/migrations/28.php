<?php

class Migration_28 extends Doctrine_Migration_Base {

    public function up() {

        // crea tabla obn_datos_seguimiento
        $columnas = array(
            'obn_id' => array(
                'type' => 'INT',
                'length' => 10,
            ),
            'etapa_id' => array(
                'type' => 'INT',
                'length' => 10,
            ),
            'nombre' => array(
                'type' => 'varchar',
                'length' => 255,
            ),
            'valor' => array(
                'type' => 'LONGTEXT',
            )
        );

        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );

        $this->createTable('obn_datos_seguimiento', $columnas, $opciones);



        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
            )
        );

        $this->createPrimaryKey('obn_datos_seguimiento', $pk_id);
        $this->changeColumn('obn_datos_seguimiento', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');
        $this->changeColumn('obn_datos_seguimiento', 'etapa_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('obn_datos_seguimiento', 'obn_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('obn_datos_seguimiento', 'nombre', 'varchar(200) NOT NULL');


        $fk_obn = array(
            'local' => 'etapa_id',
            'foreign' => 'id',
            'foreignTable' => 'etapa',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );
        $this->createForeignKey('obn_datos_seguimiento', 'fk_ds_etapa', $fk_obn);

        $fk_obn = array(
            'local' => 'obn_id',
            'foreign' => 'id',
            'foreignTable' => 'obn_structure',
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        );
        $this->createForeignKey('obn_datos_seguimiento', 'fk_ds_obn', $fk_obn);

        $this->addIndex('obn_datos_seguimiento', 'ods_unique', array(
            'fields' => array('nombre', 'obn_id','etapa_id'), 'type' => 'unique'
        ));
    }

    public function down() {
        
    }

}
