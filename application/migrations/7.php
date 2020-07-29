<?php
class Migration_7 extends Doctrine_Migration_Base
{
    public function up()
    {
        //migraciones necesarias para la versión 1.5

        // crea tabla validacion
        $columnas = array(
          'nombre' => array(
            'type'   => 'varchar',
            'length' => 255,
          ),
          'contenido' => array(
            'type'   => 'text',
          ),
          'proceso_id' => array(
            'type'   => 'INT',
            'length' => 10,
          ),
          'filename' => array(
            'type'   => 'varchar',
            'length' => 255
          )
        );

        $opciones = array(
         'type'    => 'INNODB',
         'charset' => 'utf8'
        );

        $this->createTable('validacion', $columnas, $opciones);

        $this->changeColumn('etapa_historial_ejecuciones', 'proceso_id', 'INT(10) UNSIGNED NOT NULL');

        //creo PK
        $pk_id = array(
          'id' => array(
            'type' => 'integer',
          )
        );

        $this->createPrimaryKey('validacion', $pk_id);
        $this->changeColumn('validacion', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //creo FK
        $fk_proceso = array(
          'local'        => 'proceso_id',
          'foreign'      => 'id',
          'foreignTable' => 'proceso',
          'onDelete'     => 'CASCADE',
          'onUpdate'     => 'CASCADE',
        );


        $this->createForeignKey('validacion', 'fk_etapa', $fk_proceso);
        
        // crea tabla ejecutar_validacion
        $columnas = array(
          'regla' => array(
            'type'   => 'varchar',
            'length' => 255,
          ),
          'instante' => array(
            'type'   => 'varchar',
            'length' => 255,
          ),
          'validacion_id' => array(
            'type'   => 'INT',
            'length' => 10,
          ),
            'tarea_id' => array(
            'type'   => 'INT',
            'length' => 10,
          ),
            'paso_id' => array(
            'type'   => 'INT',
            'length' => 10,
          )
        );

        $opciones = array(
         'type'    => 'INNODB',
         'charset' => 'utf8'
        );

        $this->createTable('ejecutar_validacion', $columnas, $opciones);

        $this->changeColumn('ejecutar_validacion', 'validacion_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('ejecutar_validacion', 'tarea_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('ejecutar_validacion', 'paso_id', 'INT(10) UNSIGNED NOT NULL');

        //creo PK
        $pk_id = array(
          'id' => array(
            'type' => 'integer',
          )
        );

        $this->createPrimaryKey('ejecutar_validacion', $pk_id);
        $this->changeColumn('ejecutar_validacion', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //creo FK
        $fk_validacion = array(
          'local'        => 'validacion_id',
          'foreign'      => 'id',
          'foreignTable' => 'validacion',
          'onDelete'     => 'CASCADE',
          'onUpdate'     => 'CASCADE',
        );
        $fk_tarea = array(
          'local'        => 'tarea_id',
          'foreign'      => 'id',
          'foreignTable' => 'tarea',
          'onDelete'     => 'CASCADE',
          'onUpdate'     => 'CASCADE',
        );
        $fk_paso = array(
          'local'        => 'paso_id',
          'foreign'      => 'id',
          'foreignTable' => 'paso',
          'onDelete'     => 'CASCADE',
          'onUpdate'     => 'CASCADE',
          
        );


        $this->createForeignKey('ejecutar_validacion', 'fk_validacion', $fk_validacion);
        $this->createForeignKey('ejecutar_validacion', 'fk_tarea', $fk_tarea);
        $this->createForeignKey('ejecutar_validacion', 'fk_paso', $fk_paso);

    }

    public function down()
    {
    }
}
