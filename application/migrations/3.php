<?php

class Migration_3 extends Doctrine_Migration_Base {

    public function up() {
        // --migraciones necesarias para la versión 1.5
        // modifica tabla proceso
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();

        if ($q->getTable('proceso')->hasColumn('instanciar_api') != 1) {
            $this->addColumn('proceso', 'instanciar_api', 'TINYINT(1) default 0');
        }


        // modifica tabla evento
        if ($q->getTable('evento')->hasColumn('instanciar_api') != 1) {
            $this->addColumn('evento', 'instanciar_api', 'TINYINT(1) default 0');
        }

        $this->addColumn('evento', 'traza', 'TINYINT(1) default 0');
        $this->addColumn('evento', 'tipo_registro_traza', 'INT(2) default 0');
        $this->addColumn('evento', 'descripcion_traza', 'VARCHAR(255) DEFAULT null');
        $this->addColumn('evento', 'descripcion_error_soap', 'VARCHAR(255) DEFAULT null');
        $this->addColumn('evento', 'variable_error_soap', 'VARCHAR(255) DEFAULT null');

        //modifica tabla reporte
        $this->addColumn('reporte', 'grupos_usuarios_permiso', 'TEXT DEFAULT null');
        $this->addColumn('reporte', 'usuarios_permiso', 'TEXT DEFAULT null');

        //modifica tabla pasarela_pago_generica
        $this->addColumn('pasarela_pago_generica', 'descripciones_estados_traza', 'TEXT DEFAULT null');

        //modifica tabla pasarela_pago_antel
        $this->addColumn('pasarela_pago_antel', 'descripcion_pendiente_traza', 'TEXT DEFAULT null');
        $this->addColumn('pasarela_pago_antel', 'descripcion_iniciado_traza', 'TEXT DEFAULT null');
        $this->addColumn('pasarela_pago_antel', 'descripcion_token_solicita_traza', 'TEXT DEFAULT null');
        $this->addColumn('pasarela_pago_antel', 'descripcion_realizado_traza', 'TEXT DEFAULT null');
        $this->addColumn('pasarela_pago_antel', 'descripcion_error_traza', 'TEXT DEFAULT null');
        $this->addColumn('pasarela_pago_antel', 'descripcion_reachazado_traza', 'TEXT DEFAULT null');
    }

    public function down() {
        
    }

}
