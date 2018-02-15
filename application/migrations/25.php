<?php
class Migration_25 extends Doctrine_Migration_Base
{
    public function up()
    {
        //migraciones necesarias para la versión 1.4 r1

        /*Se incluyó la columna de nombre original a la tabla file*/
        $this->addColumn('file', 'file_origen', 'VARCHAR(255) DEFAULT null');

        //-- Cambios para integracion con GREP

        // crea tabla etapa_historial_ejecuciones
        $columnas = array(
          'etapa_id' => array(
            'type'   => 'INT',
            'length' => 10,
          ),
          'secuencia' => array(
            'type'   => 'INT',
            'length' => 10
          ),
          'usuario_id' => array(
            'type'   => 'INT',
            'length' => 10,
          ),
          'descripcion' => array(
            'type'   => 'varchar',
            'length' => 255
          ),
          'fecha' => array(
            'type'   => 'varchar',
            'length' => 255
          )
        );

        $opciones = array(
         'type'    => 'INNODB',
         'charset' => 'utf8'
        );

        $this->createTable('etapa_historial_ejecuciones', $columnas, $opciones);

        $this->changeColumn('etapa_historial_ejecuciones', 'secuencia', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('etapa_historial_ejecuciones', 'etapa_id', 'INT(10) UNSIGNED NOT NULL');
        $this->changeColumn('etapa_historial_ejecuciones', 'usuario_id', 'INT(10) UNSIGNED NOT NULL');

        //creo PK
        $pk_id = array(
          'id' => array(
            'type' => 'integer',
          )
        );

        $this->createPrimaryKey('etapa_historial_ejecuciones', $pk_id);
        $this->changeColumn('etapa_historial_ejecuciones', 'id', 'INT(10) UNSIGNED AUTO_INCREMENT FIRST');

        //creo FK
        $fk_etapa = array(
          'local'        => 'etapa_id',
          'foreign'      => 'id',
          'foreignTable' => 'etapa',
        );

        $fk_usuario = array(
          'local'        => 'usuario_id',
          'foreign'      => 'id',
          'foreignTable' => 'usuario',
        );

        $this->createForeignKey('etapa_historial_ejecuciones', 'fk_etapa', $fk_etapa);
        $this->createForeignKey('etapa_historial_ejecuciones', 'fk_usuario', $fk_usuario);

        //modifica tabla proceso para GREP
        $this->addColumn('proceso', 'codigo_tramite_ws_grep', 'INT(11) DEFAULT null');

        //la tabla etapa_historial_ejecuciones
        $this->addColumn('etapa_historial_ejecuciones', 'nombre_paso', 'VARCHAR(255) DEFAULT null');

        //cambio de la version 1.4 la tabla proceso_trazabilidad
        $this->changeColumn('proceso_trazabilidad', 'envio_guid_automatico', 'TINYINT(1) DEFAULT 1');

        //se elimina indice unico
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute("ALTER TABLE usuario DROP INDEX `usuario_unique`");
    }

    public function down()
    {
    }
}
