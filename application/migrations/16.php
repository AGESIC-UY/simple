<?php
class Migration_16 extends Doctrine_Migration_Base {
    public function up(){
       //migraciones necesarias para la versión 1.1

       // --
       // -- Modifica tabla tarea
       // --
       $this->addColumn('tarea', 'automatica', 'boolean');
       $this->addColumn('tarea', 'nivel_confianza', 'VARCHAR(255)');
       $this->changeColumn('tarea', 'trazabilidad', 'default 1');
       $this->addColumn('tarea', 'trazabilidad_estado', 'INTEGER(1) NOT NULL default 2');
       $this->addColumn('tarea', 'trazabilidad_cabezal', 'boolean default 1');

       $this->addColumn('tarea', 'paso_final_pendiente', 'VARCHAR(1000) default "Para confirmar y enviar el formulario a la siguiente etapa haga click en Finalizar."');
       $this->addColumn('tarea', 'paso_final_standby', 'VARCHAR(1000) default "Luego de hacer click en Finalizar esta etapa quedara detenida momentaneamente hasta que se completen el resto de etapas pendientes."');
       $this->addColumn('tarea', 'paso_final_completado', 'VARCHAR(1000) default "El formulario está completo y listo para enviarse, una vez enviado no podrá realizar modificaciones."');
       $this->addColumn('tarea', 'paso_final_sincontinuacion', 'VARCHAR(1000) default "Este trámite no tiene una etapa donde continuar."');

       // --
       // -- Modifica tabla usuarios backend
       // --
       $this->addColumn('usuario_backend', 'seg_alc_control_total', 'boolean NOT NULL DEFAULT true');
       $this->addColumn('usuario_backend', 'seg_alc_grupos_usuarios', 'VARCHAR(1000)');
       $this->addColumn('usuario_backend', 'seg_reasginar', 'boolean NOT NULL DEFAULT true');

       // --
       // -- Crea tabla PasarelaPagoAntel
       // --
       $columnas = array(
         'pasarela_pago_id' => array(
            'type'   => 'integer',
            'length' => 10,
            'notnull' => true
         ),
         'codigo_operacion_soap' => array(
            'type'   => 'varchar',
            'length' => 64,
            'notnull' => true
         ),
         'url_redireccion' => array(
            'type'   => 'varchar',
            'length' => 200,
            'notnull' => true
         ),
         'url_ticket' => array(
            'type'   => 'varchar',
            'length' => 200
         ),
         'metodo_http' => array(
            'type'   => 'varchar',
            'length' => 4,
            'default' => 'GET'
         ),
       );
       $opciones = array(
        'type'    => 'INNODB',
        'charset' => 'utf8'
       );
       $this->createTable('pasarela_pago_generica', $columnas, $opciones);

       $columna_id = array(
         'id' => array(
           'type' => 'integer',
           'autoincrement' => true
         )
       );
       $this->createPrimaryKey('pasarela_pago_generica', $columna_id);

       $this->addColumn('pasarela_pago_generica', 'variables_post', 'VARCHAR(800)');
       $this->addColumn('pasarela_pago_generica', 'variable_evaluar', 'VARCHAR(100)');
       $this->addColumn('pasarela_pago_generica', 'variable_idsol', 'VARCHAR(100)');
       $this->addColumn('pasarela_pago_generica', 'codigo_operacion_soap_consulta', 'VARCHAR(64)');
       $this->addColumn('pasarela_pago_generica', 'mensaje_reimpresion_ticket', 'VARCHAR(400)');
       $this->addColumn('pasarela_pago_generica', 'variable_idestado', 'VARCHAR(100)');
       $this->addColumn('pasarela_pago_generica', 'tema_email_inicio', 'VARCHAR(300)');
       $this->addColumn('pasarela_pago_generica', 'cuerpo_email_inicio', 'VARCHAR(700)');
       $this->addColumn('pasarela_pago_generica', 'variable_redireccion', 'VARCHAR(100)');
       $this->addColumn('pasarela_pago_generica', 'ticket_metodo', 'VARCHAR(4)');
       $this->addColumn('pasarela_pago_generica', 'ticket_variables', 'VARCHAR(800)');

       // --
       //-- Modifica tabla Formulario
       // --
       $this->changeColumn('formulario', 'leyenda', 'VARCHAR(400)');

       // --
       //-- Modifica tabla Reporte
       // --
       $this->addColumn('reporte', 'tipo', 'VARCHAR(20)');

       // --
       //-- Crea tabla Parametros
       // --
       $columnas1 = array(
         'cuenta_id' => array(
            'type'   => 'integer',
            'length' => 10,
            'notnull' => true
         ),
         'clave' => array(
            'type'   => 'varchar',
            'length' => 100,
            'notnull' => true
         ),
         'valor' => array(
            'type'   => 'varchar',
            'length' => 200,
            'notnull' => true
         )
       );
       $opciones1 = array(
        'type'    => 'INNODB',
        'charset' => 'utf8'
       );
       $this->createTable('parametro', $columnas1, $opciones1);

       $columna_id1 = array(
         'id' => array(
           'type' => 'integer',
           'autoincrement' => true
         )
       );
       $this->createPrimaryKey('parametro', $columna_id1);

    }

    public function down(){

    }
}
