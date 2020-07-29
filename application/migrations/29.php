<?php

class Migration_29 extends Doctrine_Migration_Base {

    public function up() {
        $this->changeColumn('aud_bloque', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_usuario', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_accion', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_grupo_usuarios', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_usuario_backend', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_campo', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_conexion', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_documento', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_proceso', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_reporte', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_formulario', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_pasarela_pago', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_validacion', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_ws_operacion_respuesta', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_ejecutar_validacion', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_evento', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_evento_pago', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_pasarela_pago_antel', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_pasarela_pago_generica', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_tarea', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_ws_catalogo', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_ws_operacion', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_paso', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->changeColumn('aud_ejecutar_validacion', 'usuario_aud', 'varchar(50) NOT NULL');
        $this->addColumn('campo', 'variable_obn', 'varchar(100) DEFAULT ""');
        $this->addColumn('aud_campo', 'variable_obn', 'varchar(100) DEFAULT ""');
        $this->addColumn('formulario', 'tipo', 'varchar(100) DEFAULT "comun"');
        $this->addColumn('aud_formulario', 'tipo', 'varchar(100) DEFAULT "comun"');
    }

    public function down() {
        
    }

}
