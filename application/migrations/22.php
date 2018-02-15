<?php
class Migration_22 extends Doctrine_Migration_Base
{
    public function up()
    {
        //migraciones necesarias para la versión 1.3

        // --
        // -- crea tabla monitoreo
        // --
        $columnas = array(
          'proceso_id' => array(
            'type'   => 'integer',
            'length' => 11
          ),
          'url_web_service' => array(
             'type'   => 'text'
          ),
          'fecha' => array(
            'type'   => 'varchar',
            'length' => 255
          ),
          'tipo' => array(
            'type'   => 'varchar',
            'length' => 255
          ),
          'seguridad' => array(
            'type'   => 'TINYINT',
            'length' => 1
          ),
          'rol' => array(
            'type'   => 'varchar',
            'length' => 255
          ),
          'certificado' => array(
            'type'   => 'varchar',
            'length' => 255
          ),
          'error_texto' => array(
             'type'   => 'varchar',
             'length' => 255
          ),
          'error' => array(
             'type'   => 'TINYINT',
             'length' => 1
          ),
          'soap_peticion' => array(
             'type'   => 'text',
          ),
          'soap_respuesta' => array(
             'type'   => 'text',
          ),
          'catalogo_id' => array(
            'type'   => 'integer',
            'length' => 11
          )
        );

        $opciones = array(
         'type'    => 'INNODB',
         'charset' => 'utf8'
        );

        $this->createTable('monitoreo', $columnas, $opciones);

        $columna_id = array(
          'id' => array(
            'type' => 'integer',
            'autoincrement' => true
          )
        );

        $this->createPrimaryKey('monitoreo', $columna_id);


        // --
        // -- crea tabla monitoreo_notificaciones
        // --
        $columnas_notificaciones = array(
          'email' => array(
            'type'   => 'varchar',
            'length' => 255
          )
        );

        $opciones_notificaciones = array(
         'type'    => 'INNODB',
         'charset' => 'utf8'
        );

        $this->createTable('monitoreo_notificaciones', $columnas_notificaciones, $opciones_notificaciones);

        $columna_id_notificaciones = array(
          'id' => array(
            'type' => 'integer',
            'autoincrement' => true
          )
        );

        $this->createPrimaryKey('monitoreo_notificaciones', $columna_id_notificaciones);

        //la tabla usuario_backend
        $this->addColumn('usuario_backend', 'seg_reasginar_usu', 'boolean NOT NULL DEFAULT false');

        //la tabla etapa
        $this->addColumn('etapa', 'usuario_original_id', 'int(10) DEFAULT null');

    }

    public function down()
    {
    }
}
