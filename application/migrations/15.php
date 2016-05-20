<?php

class Migration_15 extends Doctrine_Migration_Base {
  
  public function up() {

    // --
    // -- Crea tabla Bloque
    // --
    $columnas = array(
      'nombre' => array(
         'type'   => 'varchar',
         'length' => 100,
         'notnull' => true
      )
    );

    $opciones = array(
    'type'    => 'INNODB',
    'charset' => 'utf8'
    );
    $this->createTable('bloque', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('bloque', $columna_id);

    // --
    // -- Crea tabla Pago
    // --
    $columnas = array(
      'id_tramite' => array(
         'type'   => 'string',
         'length' => 40,
         'notnull' => true
      ),
      'id_solicitud' => array(
         'type'   => 'integer',
         'length' => 40,
         'notnull' => true
      ),
      'estado' => array(
         'type'   => 'varchar',
         'length' => 14,
         'notnull' => true
      ),
      'fecha_actualizacion' => array(
         'type'   => 'varchar',
         'length' => 16,
         'notnull' => true
      ),
      'pasarela' => array(
         'type'   => 'varchar',
         'length' => 14,
         'notnull' => true
      )
    );
    $opciones = array(
    'type'    => 'INNODB',
    'charset' => 'utf8'
    );
    $this->createTable('pago', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('pago', $columna_id);

    // --
    // -- Crea tabla PasarelaPago
    // --
    $columnas = array(
      'nombre' => array(
          'type'   => 'string',
          'length' => 64,
          'notnull' => true
      ),
      'metodo' => array(
          'type'   => 'varchar',
          'length' => 64,
          'notnull' => true
      ),
      'activo' => array(
          'type'   => 'integer',
          'length' => 1,
          'notnull' => true,
          'default' => 1
      ),
      'cuenta_id' => array(
          'type'   => 'integer',
          'length' => 20,
          'notnull' => true
      )
    );
    $opciones = array(
    'type'    => 'INNODB',
    'charset' => 'utf8'
    );
    $this->createTable('pasarela_pago', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('pasarela_pago', $columna_id);

    // --
    // -- Crea tabla PasarelaPagoAntel
    // --
    $columnas = array(
      'pasarela_pago_id' => array(
         'type'   => 'integer',
         'length' => 10,
         'notnull' => true
      ),
      'id_tramite' => array(
         'type'   => 'integer',
         'length' => 10,
         'notnull' => true
      ),
      'cantidad' => array(
         'type'   => 'integer',
         'length' => 1,
         'default' => 1
      ),
      'tasa_1' => array(
         'type'   => 'string',
         'length' => '10',
         'notnull' => true
      ),
      'tasa_2' => array(
         'type'   => 'string',
         'length' => '10',
         'notnull' => true
      ),
      'tasa_3' => array(
         'type'   => 'string',
         'length' => '10',
         'notnull' => true
      ),
      'operacion' => array(
         'type'   => 'varchar',
         'length' => '1',
         'default' => 'P'
      ),
      'vencimiento' => array(
         'type'   => 'varchar',
         'length' => '12',
         'notnull' => true
      ),
      'codigos_desglose' => array(
         'type'   => 'varchar',
         'length' => '450',
         'notnull' => true
      ),
      'montos_desglose' => array(
         'type'   => 'varchar',
         'length' => '450',
         'notnull' => true
      ),
      'clave_organismo' => array(
         'type'   => 'varchar',
         'length' => '60',
         'notnull' => true
      )
    );
    $opciones = array(
     'type'    => 'INNODB',
     'charset' => 'utf8'
    );
    $this->createTable('pasarela_pago_antel', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('pasarela_pago_antel', $columna_id);

    // --
    // -- Crea tabla Pdi
    // --
    $columnas = array(
      'cuenta_id' => array(
          'type'   => 'integer',
          'length' => 10,
          'notnull' => true
      ),
      'sts' => array(
          'type'   => 'varchar',
          'length' => 200,
          'notnull' => true
      ),
      'policy' => array(
          'type'   => 'varchar',
          'length' => 200,
          'notnull' => true
      ),
      'certificado_organismo' => array(
          'type'   => 'varchar',
          'length' => 400,
          'notnull' => true
      ),
      'clave_organismo' => array(
          'type'   => 'varchar',
          'length' => 200,
          'notnull' => true
      ),
      'certificado_ssl' => array(
          'type'   => 'varchar',
          'length' => 400,
          'notnull' => true
      ),
      'clave_ssl' => array(
          'type'   => 'varchar',
          'length' => 200,
          'notnull' => true
      )
    );
    $opciones = array(
      'type'    => 'INNODB',
      'charset' => 'utf8'
    );
    $this->createTable('pdi', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('pdi', $columna_id);

    // --
    // -- Crea tabla ProcesoTrazabilidad
    // --
    $columnas = array(
      'proceso_id' => array(
         'type'   => 'integer',
         'length' => 10,
         'notnull' => true
      ),
      'organismo_id' => array(
         'type'   => 'varchar',
         'length' => 200,
         'notnull' => true
      ),
      'proceso_externo_id' => array(
         'type'   => 'varchar',
         'length' => 200,
         'notnull' => true
      )
    );
    $opciones = array(
      'type'    => 'INNODB',
      'charset' => 'utf8'
    );
    $this->createTable('proceso_trazabilidad', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('proceso_trazabilidad', $columna_id);

    // --
    // -- Crea tabla ReporteSatisfaccion
    // --
    $columnas = array(
      'usuario_id' => array(
          'type'   => 'integer',
          'length' => 10,
          'notnull' => true
      ),
      'fecha' => array(
          'type'   => 'datetime',
          'notnull' => true
      ),
      'reporte' => array(
          'type'   => 'text',
          'notnull' => true
      )
    );
    $opciones = array(
      'type'    => 'INNODB',
      'charset' => 'utf8'
    );
    $this->createTable('reporte_satisfaccion', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('reporte_satisfaccion', $columna_id);

    // --
    // -- Crea tabla WsCatalogo
    // --
    $columnas = array(
      'nombre' => array(
         'type'   => 'varchar',
         'length' => 64,
         'notnull' => true
      ),
      'wsdl' => array(
         'type'   => 'varchar',
         'length' => 400,
         'notnull' => true
      ),
      'endpoint_location' => array(
         'type'   => 'varchar',
         'length' => 400,
         'notnull' => true
      ),
      'activo' => array(
         'type'   => 'integer',
         'length' => 1,
         'default' => 1
      ),
      'conexion_timeout' => array(
         'type'   => 'integer',
         'length' => 3,
         'default' => 30
      ),
      'respuesta_timeout' => array(
         'type'   => 'integer',
         'length' => 3,
         'default' => 30
      ),
      'url_logica' => array(
         'type'   => 'varchar',
         'length' => 400,
         'notnull' => true
      ),
      'url_fisica' => array(
         'type'   => 'varchar',
         'length' => 400,
         'notnull' => true
      ),
      'rol' => array(
         'type'   => 'varchar',
         'length' => 200,
         'notnull' => true
      ),
      'tipo' => array(
         'type'   => 'varchar',
         'length' => 40,
         'notnull' => true
      )
    );
    $opciones = array(
     'type'    => 'INNODB',
     'charset' => 'utf8'
    );
    $this->createTable('ws_catalogo', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('ws_catalogo', $columna_id);

    // --
    // -- Crea tabla WsOperacion
    // --
    $columnas = array(
      'codigo' => array(
          'type'   => 'varchar',
          'length' => 12,
          'unique' => true
      ),
      'catalogo_id' => array(
          'type'   => 'integer',
          'length' => 10,
          'notnull' => true
      ),
      'nombre' => array(
          'type'   => 'varchar',
          'length' => 100,
          'notnull' => true
      ),
      'operacion' => array(
          'type'   => 'varchar',
          'length' => 100,
          'notnull' => true
      ),
      'soap' => array(
          'type'   => 'longtext',
          'notnull' => true
      ),
      'ayuda' => array(
          'type'   => 'longtext',
          'notnull' => true
      ),
      'respuestas' => array(
          'type'   => 'longtext',
          'notnull' => true
      )
    );
    $opciones = array(
      'type'    => 'INNODB',
      'charset' => 'utf8'
    );
    $this->createTable('ws_operacion', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('ws_operacion', $columna_id);

    // --
    // -- Crea tabla WsOperacionRespuesta
    // --
    $columnas = array(
      'operacion_id' => array(
         'type'   => 'integer',
         'length' => 10,
         'notnull' => true
      ),
      'respuesta_id' => array(
         'type'   => 'varchar',
         'length' => 20,
         'notnull' => true
      ),
      'xslt' => array(
         'type'   => 'longtext'
      )
    );
    $opciones = array(
      'type'    => 'INNODB',
      'charset' => 'utf8'
    );
    $this->createTable('ws_operacion_respuesta', $columnas, $opciones);

    $columna_id = array(
      'id' => array(
        'type' => 'integer',
        'autoincrement' => true
      )
    );
    $this->createPrimaryKey('ws_operacion_respuesta', $columna_id);

    // --
    // -- Modifica tabla Campo
    // --
    $this->addColumn('campo', 'fieldset', 'varchar', 100, array('notnull' => 1));
    $this->changeColumn('campo', 'nombre', 'VARCHAR(100)', null, array('notnull' => 1));

    // --
    // -- Modifica tabla Formulario
    // --
    $this->addColumn('formulario', 'bloque_id', 'integer', 10, array('notnull' => 0));

    // --
    // -- Modifica tabla Paso
    // --
    $this->addColumn('paso', 'nombre', 'varchar', 255, array('notnull' => 0));

    // --
    // -- Modifica tabla Tarea
    // --
    $this->addColumn('tarea', 'trazabilidad', 'integer', 1, array('notnull' => 1, 'default' => 1));

    // --
    // -- Modifica tabla UsuarioBackend
    // --
    $this->addColumn('usuario_backend', 'usuario', 'varchar', 128, array('notnull' => 0));
  }
}
