<?php

class Migration_15 extends Doctrine_Migration_Base {
  public function up() {
    // --
    // -- Crea tabla Bloque
    // --
    $this->createTable('bloque');
    $this->addColumn('bloque', 'id', 'integer', 9, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('bloque', 'nombre', 'varchar', 64, array('notnull' => 1));

    // --
    // -- Modifica tabla Campo
    // --
    $this->addColumn('campo', 'fieldset', 'varchar', 64, array('notnull' => 1));

    // --
    // -- Modifica tabla Formulario
    // --
    $this->addColumn('formulario', 'bloque_id', 'integer', 10, array('notnull' => 0));

    // --
    // -- Crea tabla PasarelaPago
    // --
    $this->createTable('pasarela_pago');
    $this->addColumn('pasarela_pago', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('pasarela_pago', 'nombre', 'varchar', 64, array('notnull' => 1));
    $this->addColumn('pasarela_pago', 'metodo', 'varchar', 64, array('notnull' => 1));
    $this->addColumn('pasarela_pago', 'activo', 'integer', 1, array('notnull' => 1, 'default' => 1));

    // --
    // -- Crea tabla PasarelaPagoAntel
    // --
    $this->createTable('pasarela_pago_antel');
    $this->addColumn('pasarela_pago_antel', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('pasarela_pago_antel', 'pasarela_pago_id', 'integer', 10, array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'id_tramite', 'integer', 10, array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'cantidad', 'integer', 1, array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'tasa_1', 'decimal', '6,2', array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'tasa_2', 'decimal', '6,2', array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'tasa_3', 'decimal', '6,2', array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'operacion', 'varchar', 1, array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'vencimiento', 'varchar', 12, array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'codigos_desglose', 'varchar', 450, array('notnull' => 1));
    $this->addColumn('pasarela_pago_antel', 'montos_desglose', 'varchar', 450, array('notnull' => 1));

    // --
    // -- Modifica tabla Paso
    // --
    $this->addColumn('paso', 'nombre', 'varchar', 255, array('notnull' => 0));

    // --
    // -- Crea tabla ProcesoTrazabilidad
    // --
    $this->createTable('proceso_trazabilidad');
    $this->addColumn('proceso_trazabilidad', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('proceso_trazabilidad', 'proceso_id', 'integer', 10, array('notnull' => 1));
    $this->addColumn('proceso_trazabilidad', 'organismo_id', 'varchar', 255, array('notnull' => 1));
    $this->addColumn('proceso_trazabilidad', 'proceso_externo_id', 'varchar', 255, array('notnull' => 1));

    // --
    // -- Crea tabla ReporteSatisfaccion
    // --
    $this->createTable('reporte_satisfaccion');
    $this->addColumn('reporte_satisfaccion', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('reporte_satisfaccion', 'usuario_id', 'integer', 10, array('notnull' => 1));
    $this->addColumn('reporte_satisfaccion', 'fecha', 'datetime', null, array('notnull' => 1));
    $this->addColumn('reporte_satisfaccion', 'reporte', 'text', 2000, array('notnull' => 1));

    // --
    // -- Modifica tabla Tarea
    // --
    $this->addColumn('tarea', 'trazabilidad', 'integer', 1, array('notnull' => 1, 'default' => 1));

    // --
    // -- Crea tabla WsCatalogo
    // --
    $this->createTable('ws_catalogo');
    $this->addColumn('ws_catalogo', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('ws_catalogo', 'nombre', 'varchar', 64, array('notnull' => 1));
    $this->addColumn('ws_catalogo', 'wsdl', 'varchar', 255, array('notnull' => 1));
    $this->addColumn('ws_catalogo', 'endpoint_location', 'varchar', 255, array('notnull' => 1));
    $this->addColumn('ws_catalogo', 'activo', 'integer', 1, array('notnull' => 1, 'default' => 1));
    $this->addColumn('ws_catalogo', 'conexion_timeout', 'integer', 3, array('notnull' => 1, 'default' => 30));
    $this->addColumn('ws_catalogo', 'respuesta_timeout', 'integer', 3, array('notnull' => 1, 'default' => 30));

    // --
    // -- Crea tabla WsOperacion
    // --
    $this->createTable('ws_operacion');
    $this->addColumn('ws_operacion', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('ws_operacion', 'catalogo_id', 'integer', 10, array('notnull' => 1));
    $this->addColumn('ws_operacion', 'wsdl', 'varchar', 255, array('notnull' => 1));
    $this->addColumn('ws_operacion', 'nombre', 'varchar', 155, array('notnull' => 1));
    $this->addColumn('ws_operacion', 'operacion', 'varchar', 155, array('notnull' => 1));
    $this->addColumn('ws_operacion', 'soap', 'text', 9000, array('notnull' => 1));
    $this->addColumn('ws_operacion', 'ayuda', 'text', 2000, array('notnull' => 1));
    $this->addColumn('ws_operacion', 'respuestas', 'text', 9000, array('notnull' => 1));

    // --
    // -- Crea tabla WsOperacionRespuesta
    // --
    $this->createTable('ws_operacion_respuesta');
    $this->addColumn('ws_operacion_respuesta', 'id', 'integer', 10, array('notnull' => 1, 'auto_increment' => 1, 'unsigned' => 1));
    $this->addColumn('ws_operacion_respuesta', 'operacion_id', 'integer', 10, array('notnull' => 1));
    $this->addColumn('ws_operacion_respuesta', 'respuesta_id', 'integer', 19, array('notnull' => 1));
    $this->addColumn('ws_operacion_respuesta', 'xslt', 'text', 9000, array('notnull' => 1));
  }

  public function down() {
    // --
    // -- Destruye tabla Bloque
    // --
    $this->dropTable('bloque');

    // --
    // -- Elimina columna fieldset de tabla Campo
    // --
    $this->removeColumn('campo', 'fieldset');

    // --
    // -- Destruye tabla PasarelaPago
    // --
    $this->dropTable('pasarela_pago');

    // --
    // -- Destruye tabla PasarelaPagoAntel
    // --
    $this->dropTable('pasarela_pago_antel');

    // --
    // -- Elimina columna nombre de tabla Paso
    // --
    $this->removeColumn('paso', 'nombre');

    // --
    // -- Destruye tabla ReporteSatisfaccion
    // --
    $this->dropTable('reporte_satisfaccion');

    // --
    // -- Elimina columna trazabilidad de tabla Tarea
    // --
    $this->removeColumn('tarea', 'trazabilidad');

    // --
    // -- Destruye tabla WsCatalogo
    // --
    $this->dropTable('ws_catalogo');

    // --
    // -- Destruye tabla WsOperacion
    // --
    $this->dropTable('ws_operacion');

    // --
    // -- Destruye tabla WsOperacionRespuesta
    // --
    $this->dropTable('ws_operacion_respuesta');
  }
}
