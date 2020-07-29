<?php

class Migration_14 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        //migraciones necesarias para la versión 2.0
        // crea tabla etiquetas de trazabilidad
        $columnas = array(
            'etiqueta' => array(
                'type' => 'varchar',
                'length' => 255,
                'notnull' => 1,
            ),
            'descripcion' => array(
                'type' => 'varchar',
                'length' => 255,
            )
        );


        $opciones = array(
            'type' => 'INNODB',
            'charset' => 'utf8'
        );

        $this->createTable('etiqueta_traza', $columnas, $opciones);

        //creo PK
        $pk_id = array(
            'id' => array(
                'type' => 'integer',
                'autoincrement' => true
            )
        );

        $this->createPrimaryKey('etiqueta_traza', $pk_id);

        $definition = array(
            'fields' => array(
                'etiqueta' => array()
            ),
            'unique' => true
        );

        $this->createConstraint('etiqueta_traza', 'etiqueta_uk', $definition);

        //trazabilidad v2
        //tabla tarea
        $this->addColumn('tarea', 'etiqueta_traza', 'varchar(255) default ""');
        $this->addColumn('tarea', 'visible_traza', 'varchar(255) default "VISIBLE"');
        $this->addColumn('tarea', 'trazabilidad_nombre_oficina', 'varchar(255) default ""');
        $this->changeColumn('tarea', 'trazabilidad_estado', 'varchar(20)');

        //tabla evento
        $this->addColumn('evento', 'etiqueta_traza', 'varchar(255) default ""');
        $this->addColumn('evento', 'visible_traza', 'varchar(255) default "VISIBLE"');
        $this->changeColumn('evento', 'tipo_registro_traza', 'varchar(20) default "COMUN"');

        //tabla paso
        $this->addColumn('paso', 'etiqueta_traza', 'varchar(255) default ""');
        $this->addColumn('paso', 'visible_traza', 'varchar(255) default "VISIBLE"');

        //tabla evento_pago
        $this->addColumn('evento_pago', 'etiqueta_traza', 'varchar(255) default ""');
        $this->addColumn('evento_pago', 'visible_traza', 'varchar(255) default ""');
        $this->changeColumn('evento_pago', 'tipo_registro_traza', 'varchar(20) default "COMUN"');

        //tabla proceso
        $this->addColumn('proceso', 'estado', 'varchar(255) default "public"');
        $this->addColumn('proceso', 'root', 'int(11) default NULL');
        $this->addColumn('proceso', 'version', 'int(11) default 1');
    }

    public function down() {
        
    }

}
